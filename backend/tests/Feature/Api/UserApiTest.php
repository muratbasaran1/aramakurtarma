<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\AuditLog;
use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_users_for_the_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'balikesir']);
        $otherTenant = Tenant::factory()->create(['slug' => 'tekirdag']);

        User::factory()->count(3)->for($tenant)->create();
        User::factory()->for($otherTenant)->create();

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_filters_users_by_status_and_role(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'edirne']);

        User::factory()->for($tenant)->create([
            'name' => 'Aktif Kullanıcı',
            'status' => 'active',
            'role' => 'operator',
        ]);

        User::factory()->for($tenant)->create([
            'name' => 'Pasif Kullanıcı',
            'status' => 'inactive',
            'role' => 'viewer',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users?status=active&role=operator');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.role', 'operator');
    }

    public function test_it_filters_users_by_unit_and_search(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'canakkale']);
        $unit = Unit::factory()->for($tenant)->create(['name' => 'Saha Ekibi']);
        $otherUnit = Unit::factory()->for($tenant)->create(['name' => 'Lojistik']);

        User::factory()->for($tenant)->create([
            'unit_id' => $unit->id,
            'name' => 'Saha Lideri',
            'email' => 'lider@example.org',
        ]);

        User::factory()->for($tenant)->create([
            'unit_id' => $otherUnit->id,
            'name' => 'Lojistik Üyesi',
            'email' => 'lojistik@example.org',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users?unit_id='.$unit->id.'&search=lider');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'lider@example.org');
    }

    public function test_it_displays_user_details_for_the_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ankara']);
        $unit = Unit::factory()->for($tenant)->create(['name' => 'Operasyon']);
        $user = User::factory()->for($tenant)->for($unit)->create([
            'name' => 'Panel Kullanıcısı',
            'email' => 'panel@example.org',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users/'.$user->id);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Panel Kullanıcısı')
            ->assertJsonPath('data.unit.id', $unit->id)
            ->assertJsonPath('data.unit_id', $unit->id);
    }

    public function test_it_prevents_cross_tenant_user_access(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'malatya']);
        $otherTenant = Tenant::factory()->create(['slug' => 'samsun']);
        $user = User::factory()->for($tenant)->create();

        $this->getJson('/api/tenants/'.$otherTenant->slug.'/users/'.$user->id)
            ->assertNotFound();
    }

    public function test_it_creates_user_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);
        $unit = Unit::factory()->for($tenant)->create();

        $payload = [
            'name' => 'Yeni Kullanıcı',
            'email' => 'yeni@example.org',
            'phone' => '+905551112233',
            'role' => 'ops_lead',
            'unit_id' => $unit->id,
            'documents' => ['certificate' => 'AFAD-001'],
            'documents_expires_at' => now()->addMonths(3)->toIso8601String(),
            'password' => 'super-secret-pass',
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/users', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'yeni@example.org')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'email' => 'yeni@example.org',
            'unit_id' => $unit->id,
        ]);

        $created = User::query()->where('email', 'yeni@example.org')->firstOrFail();
        $this->assertTrue(Hash::check('super-secret-pass', $created->password));

        $audit = AuditLog::query()->latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertSame('user.created', $audit->event);
        $this->assertSame($tenant->id, $audit->tenant_id);
        $this->assertSame('***', $audit->payload['changes']['password'] ?? null);
    }

    public function test_it_requires_documents_expiry_when_documents_present(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'trabzon']);

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/users', [
            'name' => 'Eksik Belge',
            'email' => 'eksik@example.org',
            'role' => 'responder',
            'password' => 'very-secure-pass',
            'documents' => ['id' => 'DOC-1'],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['documents_expires_at']);
    }

    public function test_it_blocks_unit_assignment_from_another_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'kirikkale']);
        $otherTenant = Tenant::factory()->create(['slug' => 'sinop']);
        $foreignUnit = Unit::factory()->for($otherTenant)->create();

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/users', [
            'name' => 'Çapraz Tenant',
            'email' => 'cross@example.org',
            'role' => 'ops',
            'password' => 'tenants-rule-123',
            'unit_id' => $foreignUnit->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['unit_id']);
    }

    public function test_it_updates_user_details(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mersin']);
        $unit = Unit::factory()->for($tenant)->create();
        $newUnit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($unit)->create([
            'status' => 'inactive',
        ]);

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/users/'.$user->id, [
            'name' => 'Güncellenmiş Kullanıcı',
            'status' => 'active',
            'unit_id' => $newUnit->id,
            'password' => 'updated-password-123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Güncellenmiş Kullanıcı')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.unit_id', $newUnit->id);

        $user->refresh();
        $this->assertSame('Güncellenmiş Kullanıcı', $user->name);
        $this->assertSame('active', $user->status);
        $this->assertSame($newUnit->id, $user->unit_id);
        $this->assertTrue(Hash::check('updated-password-123', $user->password));

        $updateAudit = AuditLog::query()->latest('id')->first();
        $this->assertNotNull($updateAudit);
        $this->assertSame('user.updated', $updateAudit->event);
        $this->assertSame($tenant->id, $updateAudit->tenant_id);
        $this->assertSame('***', $updateAudit->payload['changes']['password'] ?? null);
    }

    public function test_it_prevents_cross_tenant_updates(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'rize']);
        $otherTenant = Tenant::factory()->create(['slug' => 'artvin']);
        $user = User::factory()->for($tenant)->create();

        $this->patchJson('/api/tenants/'.$otherTenant->slug.'/users/'.$user->id, [
            'name' => 'Yetkisiz',
        ])->assertNotFound();
    }

    public function test_destroy_deletes_user_without_active_assignments(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'kastamonu']);
        $user = User::factory()->for($tenant)->create([
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->deleteJson('/api/tenants/'.$tenant->slug.'/users/'.$user->getKey());

        $response->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $user->getKey(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'user.deleted',
            'auditable_type' => User::class,
            'auditable_id' => $user->getKey(),
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_destroy_blocks_user_with_active_task_assignment(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bolu']);
        $incident = Incident::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->create();

        Task::factory()->for($tenant)->for($incident)->create([
            'assigned_to' => $user->getKey(),
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        $response = $this->deleteJson('/api/tenants/'.$tenant->slug.'/users/'.$user->getKey());

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Aktif görev ataması bulunan kullanıcı silinemez.');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
        ]);
    }

    public function test_destroy_blocks_user_from_other_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'amasya']);
        $otherTenant = Tenant::factory()->create(['slug' => 'tokat']);
        $user = User::factory()->for($tenant)->create();

        $response = $this->deleteJson('/api/tenants/'.$otherTenant->slug.'/users/'.$user->getKey());

        $response->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'tenant_id' => $tenant->getKey(),
        ]);
    }
}

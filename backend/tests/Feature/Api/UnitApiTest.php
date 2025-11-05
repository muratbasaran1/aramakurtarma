<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UnitApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_units_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ankara']);
        $otherTenant = Tenant::factory()->create();

        Unit::factory()->count(2)->for($tenant)->create();
        Unit::factory()->for($otherTenant)->create();

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.units.index', ['tenant' => $tenant->slug]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 2)
                    ->has(
                        'data.0',
                        fn (AssertableJson $item) => $item
                            ->where('tenant_id', $tenant->id)
                            ->etc()
                    )
                    ->etc()
            );
    }

    public function test_index_filters_by_type_and_search(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);

        Unit::factory()->for($tenant)->create(['name' => 'Komuta Ekibi', 'type' => 'command']);
        Unit::factory()->for($tenant)->create(['name' => 'Lojistik Destek', 'type' => 'logistics']);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.units.index', [
                'tenant' => $tenant->slug,
                'type' => ['command'],
                'search' => 'Komuta',
            ]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 1)
                    ->where('data.0.name', 'Komuta Ekibi')
                    ->where('data.0.type', 'command')
                    ->etc()
            );
    }

    public function test_show_returns_unit_with_recent_tasks_and_users(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'istanbul']);
        $unit = Unit::factory()->for($tenant)->create(['type' => 'medical', 'slug' => 'istanbul-medical']);
        $otherUnit = Unit::factory()->for($tenant)->create();

        User::factory()->count(2)->for($tenant)->for($unit)->create();
        User::factory()->for($tenant)->for($otherUnit)->create();

        $incident = Incident::factory()->for($tenant)->create();
        Task::factory()->for($tenant)->for($incident)->create([
            'assigned_unit_id' => $unit->id,
            'status' => 'in_progress',
        ]);
        Task::factory()->for($tenant)->for($incident)->create([
            'assigned_unit_id' => $unit->id,
            'status' => 'done',
        ]);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.units.show', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.id', $unit->id)
                    ->where('data.slug', $unit->slug)
                    ->where('data.users_count', 2)
                    ->where('data.tasks_count', 2)
                    ->where('data.active_tasks_count', 1)
                    ->has('data.users', 2)
                    ->has('data.recent_tasks', 2)
            );
    }

    public function test_show_accepts_numeric_slug_identifier(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'antalya']);
        $unit = Unit::factory()->for($tenant)->create(['slug' => '123', 'type' => 'logistics']);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.units.show', [
                'tenant' => $tenant->slug,
                'unit' => '123',
            ]));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $unit->id)
            ->assertJsonPath('data.slug', '123');
    }

    public function test_show_does_not_leak_other_tenant_units(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ankara']);
        $otherTenant = Tenant::factory()->create(['slug' => 'izmir']);

        $unit = Unit::factory()->for($otherTenant)->create(['slug' => 'izmir-lojistik']);

        $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.units.show', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]))
            ->assertNotFound();
    }

    public function test_store_creates_unit_with_auto_slug(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'kayseri']);

        $payload = [
            'name' => 'Saha Koordinasyon Ekibi',
            'type' => 'search-and-rescue',
            'metadata' => ['capacity' => 12],
        ];

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->postJson(route('api.tenants.units.store', ['tenant' => $tenant->slug]), $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Saha Koordinasyon Ekibi')
            ->assertJsonPath('data.slug', 'saha-koordinasyon-ekibi')
            ->assertJsonPath('data.type', 'search-and-rescue')
            ->assertJsonPath('data.metadata.capacity', 12);

        $this->assertDatabaseHas('units', [
            'tenant_id' => $tenant->id,
            'slug' => 'saha-koordinasyon-ekibi',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'unit.created',
            'auditable_type' => Unit::class,
            'auditable_id' => $response->json('data.id'),
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_store_rejects_duplicate_slug_within_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'eskisehir']);

        Unit::factory()->for($tenant)->create(['slug' => 'lojistik-merkez']);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->postJson(route('api.tenants.units.store', ['tenant' => $tenant->slug]), [
                'name' => 'Lojistik Merkez',
                'slug' => 'lojistik-merkez',
                'type' => 'logistics',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_update_changes_unit_details_and_slug(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'samsun']);
        $unit = Unit::factory()->for($tenant)->create([
            'name' => 'Komuta Merkezi',
            'slug' => 'komuta-merkezi',
            'type' => 'command',
            'metadata' => ['capacity' => 8],
        ]);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->patchJson(route('api.tenants.units.update', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]), [
                'name' => 'Komuta Merkezi 2',
                'slug' => 'komuta-merkezi-2',
                'type' => 'command',
                'metadata' => ['capacity' => 16],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Komuta Merkezi 2')
            ->assertJsonPath('data.slug', 'komuta-merkezi-2')
            ->assertJsonPath('data.metadata.capacity', 16);

        $this->assertDatabaseHas('units', [
            'tenant_id' => $tenant->id,
            'slug' => 'komuta-merkezi-2',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'unit.updated',
            'auditable_type' => Unit::class,
            'auditable_id' => $unit->getKey(),
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_update_allows_numeric_slug_identifier(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mardin']);
        $unit = Unit::factory()->for($tenant)->create([
            'name' => 'Saha Birimi',
            'slug' => '456',
            'type' => 'search-and-rescue',
        ]);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->patchJson(route('api.tenants.units.update', [
                'tenant' => $tenant->slug,
                'unit' => '456',
            ]), [
                'metadata' => ['capacity' => 5],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.slug', '456')
            ->assertJsonPath('data.metadata.capacity', 5);

        $this->assertDatabaseHas('units', [
            'tenant_id' => $tenant->id,
            'slug' => '456',
            'metadata->capacity' => 5,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'unit.updated',
            'auditable_type' => Unit::class,
            'auditable_id' => $unit->getKey(),
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_update_returns_404_for_unit_of_another_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'kars']);
        $otherTenant = Tenant::factory()->create(['slug' => 'ardahan']);

        $unit = Unit::factory()->for($otherTenant)->create(['slug' => 'ardahan-medikal']);

        $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->patchJson(route('api.tenants.units.update', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]), [
                'name' => 'Yetkisiz Güncelleme',
                'type' => 'medical',
            ])
            ->assertNotFound();
    }

    public function test_destroy_deletes_unit_without_dependencies(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'yalova']);
        $unit = Unit::factory()->for($tenant)->create(['slug' => 'destek-ekibi']);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->deleteJson(route('api.tenants.units.destroy', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]));

        $response->assertNoContent();

        $this->assertDatabaseMissing('units', [
            'id' => $unit->getKey(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'unit.deleted',
            'auditable_type' => Unit::class,
            'auditable_id' => $unit->getKey(),
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_destroy_blocks_unit_with_active_task(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mus']);
        $unit = Unit::factory()->for($tenant)->create(['slug' => 'lojistik']);
        $incident = Incident::factory()->for($tenant)->create();

        Task::factory()->for($tenant)->for($incident)->create([
            'assigned_unit_id' => $unit->getKey(),
            'status' => Task::STATUS_ASSIGNED,
        ]);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->deleteJson(route('api.tenants.units.destroy', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]));

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Aktif görevi bulunan birim silinemez.');

        $this->assertDatabaseHas('units', [
            'id' => $unit->getKey(),
        ]);
    }

    public function test_destroy_blocks_unit_with_users(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'adana']);
        $unit = Unit::factory()->for($tenant)->create(['slug' => 'lojistik-2']);

        User::factory()->for($tenant)->for($unit)->create();

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->deleteJson(route('api.tenants.units.destroy', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]));

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Kullanıcısı bulunan birim silinemez.');

        $this->assertDatabaseHas('units', [
            'id' => $unit->getKey(),
        ]);
    }

    public function test_destroy_blocks_unit_from_other_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'edirne']);
        $otherTenant = Tenant::factory()->create(['slug' => 'kirklareli']);
        $unit = Unit::factory()->for($otherTenant)->create(['slug' => 'arama']);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->deleteJson(route('api.tenants.units.destroy', [
                'tenant' => $tenant->slug,
                'unit' => $unit->slug,
            ]));

        $response->assertNotFound();

        $this->assertDatabaseHas('units', [
            'id' => $unit->getKey(),
            'tenant_id' => $otherTenant->getKey(),
        ]);
    }
}

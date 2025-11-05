<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuditLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_audit_logs_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ankara']);
        $otherTenant = Tenant::factory()->create();
        $user = User::factory()->for($tenant)->create();

        $matching = AuditLog::factory()
            ->for($tenant)
            ->create([
                'user_id' => $user->id,
                'event' => 'unit.updated',
                'auditable_type' => Unit::class,
                'auditable_id' => 55,
                'payload' => [
                    'changes' => ['name' => 'Güncel İsim'],
                ],
            ]);

        AuditLog::factory()->for($otherTenant)->create();

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.audit-logs.index', ['tenant' => $tenant->slug]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 1)
                    ->where('data.0.id', $matching->id)
                    ->where('data.0.event', 'unit.updated')
                    ->where('data.0.auditable_id', 55)
                    ->where('data.0.user.id', $user->id)
                    ->where('data.0.payload.changes.name', 'Güncel İsim')
                    ->etc()
            );
    }

    public function test_index_supports_event_and_type_filters(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);

        $first = AuditLog::factory()->for($tenant)->create([
            'event' => 'unit.created',
            'auditable_type' => Unit::class,
        ]);
        AuditLog::factory()->for($tenant)->create([
            'event' => 'user.updated',
            'auditable_type' => User::class,
        ]);

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.audit-logs.index', [
                'tenant' => $tenant->slug,
                'event' => ['unit.created'],
                'auditable_type' => Unit::class,
            ]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 1)
                    ->where('data.0.id', $first->id)
                    ->where('data.0.event', 'unit.created')
                    ->etc()
            );
    }

    public function test_index_filters_by_since_and_has_user(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bursa']);
        $user = User::factory()->for($tenant)->create();

        $recent = AuditLog::factory()->for($tenant)->create([
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        AuditLog::factory()->for($tenant)->create([
            'user_id' => null,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        $since = Carbon::now()->subHour()->toIso8601String();

        $response = $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.audit-logs.index', [
                'tenant' => $tenant->slug,
                'since' => $since,
                'has_user' => 'true',
            ]));

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data', 1)
                    ->where('data.0.id', $recent->id)
                    ->etc()
            );
    }

    public function test_index_validates_numeric_auditable_id(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'samsun']);

        $this->withHeaders(['X-Tenant' => $tenant->slug])
            ->getJson(route('api.tenants.audit-logs.index', [
                'tenant' => $tenant->slug,
                'auditable_id' => 'invalid',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['auditable_id']);
    }
}

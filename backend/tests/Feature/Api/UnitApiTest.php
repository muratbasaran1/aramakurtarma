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
}

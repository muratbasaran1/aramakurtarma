<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpsCenterSummaryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_dashboard_summary_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'istanbul']);
        $unit = Unit::factory()->for($tenant)->create(['type' => 'rescue']);
        User::factory()->count(2)->for($tenant)->for($unit)->create();

        $incident = Incident::factory()->for($tenant)->create([
            'code' => 'INC-100',
            'status' => 'active',
            'priority' => 'high',
            'started_at' => now()->subHour(),
        ]);

        Task::factory()
            ->for($tenant)
            ->for($incident)
            ->for($unit, 'assignedUnit')
            ->create([
                'status' => 'in_progress',
                'planned_start_at' => now()->subHours(2),
                'requires_double_confirmation' => true,
            ]);

        Inventory::factory()->for($tenant)->create([
            'status' => 'active',
        ]);
        Inventory::factory()->for($tenant)->create([
            'status' => 'service',
        ]);

        $otherTenant = Tenant::factory()->create(['slug' => 'ankara']);
        Incident::factory()->for($otherTenant)->create();

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/opscenter/summary');

        $response->assertOk()
            ->assertJsonStructure([
                'generated_at',
                'tenant' => ['id', 'name', 'slug', 'timezone'],
                'counts' => ['incidents', 'tasks', 'inventory'],
                'recent' => ['incidents', 'tasks'],
                'units',
            ])
            ->assertJsonPath('tenant.slug', 'istanbul')
            ->assertJsonPath('counts.incidents.active', 1)
            ->assertJsonPath('counts.tasks.in_progress', 1)
            ->assertJsonPath('counts.inventory.active', 1)
            ->assertJsonPath('counts.inventory.service', 1)
            ->assertJsonCount(1, 'recent.incidents')
            ->assertJsonPath('recent.incidents.0.code', 'INC-100')
            ->assertJsonCount(1, 'recent.tasks')
            ->assertJsonPath('recent.tasks.0.assigned_unit.name', $unit->name)
            ->assertJsonCount(1, 'units')
            ->assertJsonPath('units.0.users_count', 2);
    }
}

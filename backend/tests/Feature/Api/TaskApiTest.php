<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tasks_for_the_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'antalya']);
        $incident = Incident::factory()->for($tenant)->create();
        $otherTenant = Tenant::factory()->create(['slug' => 'rize']);
        $otherIncident = Incident::factory()->for($otherTenant)->create();

        Task::factory()->for($incident)->count(2)->create([
            'status' => 'in_progress',
        ]);

        Task::factory()->for($otherIncident)->create([
            'status' => 'in_progress',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tasks');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_filters_tasks_by_status_and_confirmation(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'erzurum']);
        $incident = Incident::factory()->for($tenant)->create();

        Task::factory()->for($incident)->create([
            'status' => 'assigned',
            'requires_double_confirmation' => true,
        ]);

        Task::factory()->for($incident)->create([
            'status' => 'done',
            'requires_double_confirmation' => false,
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tasks?status=done&requires_confirmation=false');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'done')
            ->assertJsonPath('data.0.requires_double_confirmation', false);
    }

    public function test_it_returns_single_task_with_relationships(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'trabzon']);
        $unit = Unit::factory()->for($tenant)->create();
        $assignee = User::factory()->for($tenant)->create([
            'unit_id' => $unit->id,
        ]);
        $incident = Incident::factory()->for($tenant)->create([
            'code' => 'INC-900',
        ]);

        $task = Task::factory()->for($incident)->create([
            'assigned_unit_id' => $unit->id,
            'assigned_to' => $assignee->id,
            'status' => 'assigned',
        ]);

        self::assertSame($tenant->id, $task->tenant_id);
        self::assertNotNull(Task::forTenant($tenant)->find($task->id));

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tasks/'.$task->id);

        $response->assertOk()
            ->assertJsonPath('data.incident.code', 'INC-900')
            ->assertJsonPath('data.assigned_unit.name', $unit->name)
            ->assertJsonPath('data.assignee.email', $assignee->email);
    }

    public function test_it_returns_404_for_task_of_another_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ordu']);
        $otherTenant = Tenant::factory()->create(['slug' => 'giresun']);
        $incident = Incident::factory()->for($otherTenant)->create();
        $task = Task::factory()->for($incident)->create();

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tasks/'.$task->id);

        $response->assertNotFound();
    }

    public function test_it_creates_task_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mersin']);
        $incident = Incident::factory()->for($tenant)->create();
        $unit = Unit::factory()->for($tenant)->create();
        $assignee = User::factory()->for($tenant)->create([
            'unit_id' => $unit->getKey(),
        ]);

        $plannedStart = Carbon::now()->addHour()->toISOString();

        $payload = [
            'incident_id' => $incident->id,
            'assigned_unit_id' => $unit->id,
            'assigned_to' => $assignee->id,
            'status' => 'assigned',
            'planned_start_at' => $plannedStart,
            'requires_double_confirmation' => true,
            'route' => [
                'type' => 'LineString',
                'coordinates' => [
                    [30.1, 40.2],
                    [30.2, 40.3],
                ],
            ],
            'context' => [
                'notes' => 'Hazırlık tamamlandı.',
            ],
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tasks', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'assigned')
            ->assertJsonPath('data.assigned_unit.id', $unit->id)
            ->assertJsonPath('data.assignee.email', $assignee->email)
            ->assertJsonPath('data.route.type', 'LineString')
            ->assertJsonPath('data.context.notes', 'Hazırlık tamamlandı.');

        $this->assertDatabaseHas('tasks', [
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_unit_id' => $unit->id,
            'assigned_to' => $assignee->id,
            'status' => 'assigned',
        ]);
    }

    public function test_it_updates_task_status_with_completion_fields(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bursa']);
        $incident = Incident::factory()->for($tenant)->create();
        $task = Task::factory()->for($incident)->create([
            'status' => 'in_progress',
            'requires_double_confirmation' => true,
            'planned_start_at' => Carbon::now()->subHours(2),
        ]);

        $completedAt = Carbon::now()->subHour()->toIso8601String();

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/tasks/'.$task->id, [
            'status' => 'done',
            'completed_at' => $completedAt,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'done')
            ->assertJsonPath('data.completed_at', $completedAt);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    public function test_it_rejects_verified_status_without_double_confirmation(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'kars']);
        $incident = Incident::factory()->for($tenant)->create();
        $task = Task::factory()->for($incident)->create([
            'status' => 'done',
            'completed_at' => Carbon::now()->subHours(1),
            'requires_double_confirmation' => false,
        ]);

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/tasks/'.$task->id, [
            'status' => 'verified',
            'verified_at' => Carbon::now()->toISOString(),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['requires_double_confirmation']);
    }
}

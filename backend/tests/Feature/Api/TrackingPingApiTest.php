<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TrackingPing;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingPingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tracking_pings_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ankara']);
        $otherTenant = Tenant::factory()->create(['slug' => 'izmir']);

        $user = User::factory()->for($tenant)->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_to' => $user->id,
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        TrackingPing::factory()->count(2)->state(new Sequence(
            ['tenant_id' => $tenant->id, 'user_id' => $user->id, 'task_id' => $task->id, 'captured_at' => now()->subMinutes(5)],
            ['tenant_id' => $tenant->id, 'user_id' => $user->id, 'task_id' => $task->id, 'captured_at' => now()->subMinutes(3)],
        ))->create();

        TrackingPing::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tracking/pings');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user_id', $user->id);
    }

    public function test_it_lists_latest_tracking_ping_per_user(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'istanbul']);

        $users = User::factory()->count(2)->for($tenant)->create();
        $firstUser = $users->first();
        $secondUser = $users->last();

        TrackingPing::factory()->count(2)->state(new Sequence(
            ['tenant_id' => $tenant->id, 'user_id' => $firstUser->id, 'captured_at' => now()->subMinutes(10)],
            ['tenant_id' => $tenant->id, 'user_id' => $firstUser->id, 'captured_at' => now()->subMinutes(2)],
        ))->create();

        $secondUserPing = TrackingPing::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $secondUser->id,
            'captured_at' => now()->subMinute(),
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tracking/pings/latest');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user_id', $secondUser->id)
            ->assertJsonPath('data.1.user_id', $firstUser->id)
            ->assertJsonPath('data.0.id', $secondUserPing->id);
    }

    public function test_it_filters_latest_tracking_ping_by_user(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'gaziantep']);

        $users = User::factory()->count(2)->for($tenant)->create();
        $filteredUser = $users->first();
        $otherUser = $users->last();

        TrackingPing::factory()->count(2)->state(new Sequence(
            ['tenant_id' => $tenant->id, 'user_id' => $filteredUser->id, 'captured_at' => now()->subMinutes(5)],
            ['tenant_id' => $tenant->id, 'user_id' => $filteredUser->id, 'captured_at' => now()->subMinute()],
        ))->create();

        TrackingPing::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $otherUser->id,
            'captured_at' => now()->subMinutes(3),
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/tracking/pings/latest?user_id='.$filteredUser->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $filteredUser->id);
    }

    public function test_it_creates_tracking_ping_and_records_audit(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'antalya']);
        $unit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($unit, 'unit')->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_to' => $user->id,
            'assigned_unit_id' => $unit->id,
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        $payload = [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 41.0,
            'longitude' => 29.0,
            'speed' => 0.4,
            'heading' => 120,
            'captured_at' => now()->toISOString(),
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tracking/pings', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.task_id', $task->id);

        $this->assertDatabaseHas('tracking_pings', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'task_id' => $task->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'tracking.ping_recorded',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_it_triggers_no_motion_alert_when_stationary_for_threshold(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bursa']);
        $unit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($unit, 'unit')->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_to' => $user->id,
            'assigned_unit_id' => $unit->id,
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        TrackingPing::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 41.001,
            'longitude' => 29.001,
            'speed' => 2.5,
            'captured_at' => now()->subSeconds(300),
        ]);

        TrackingPing::factory()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 41.001,
            'longitude' => 29.001,
            'speed' => 0.0,
            'captured_at' => now()->subSeconds(150),
        ]);

        $payload = [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 41.001,
            'longitude' => 29.001,
            'speed' => 0.0,
            'captured_at' => now()->toISOString(),
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tracking/pings', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'tracking.no_motion',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_it_rejects_ping_when_task_assigned_to_different_user(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'trabzon']);
        $unit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($unit, 'unit')->create();
        $otherUser = User::factory()->for($tenant)->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_to' => $otherUser->id,
            'assigned_unit_id' => $unit->id,
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        $payload = [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 40.0,
            'longitude' => 30.0,
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tracking/pings', $payload);

        $response->assertStatus(422)->assertJsonValidationErrors('task_id');
    }

    public function test_it_rejects_ping_when_task_not_active(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'samsun']);
        $unit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($unit, 'unit')->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_to' => $user->id,
            'assigned_unit_id' => $unit->id,
            'status' => Task::STATUS_DONE,
        ]);

        $payload = [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 40.0,
            'longitude' => 30.0,
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tracking/pings', $payload);

        $response->assertStatus(422)->assertJsonValidationErrors('task_id');
    }

    public function test_it_rejects_ping_when_unit_does_not_match_task_assignment(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'maras']);
        $assignedUnit = Unit::factory()->for($tenant)->create();
        $userUnit = Unit::factory()->for($tenant)->create();
        $user = User::factory()->for($tenant)->for($userUnit, 'unit')->create();
        $incident = Incident::factory()->create(['tenant_id' => $tenant->id]);

        $task = Task::factory()->create([
            'tenant_id' => $tenant->id,
            'incident_id' => $incident->id,
            'assigned_unit_id' => $assignedUnit->id,
            'status' => Task::STATUS_ASSIGNED,
        ]);

        $payload = [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'latitude' => 40.0,
            'longitude' => 30.0,
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/tracking/pings', $payload);

        $response->assertStatus(422)->assertJsonValidationErrors('task_id');
    }
}

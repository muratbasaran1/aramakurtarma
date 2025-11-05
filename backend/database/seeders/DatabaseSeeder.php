<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TrackingPing;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::factory()
            ->count(2)
            ->create()
            ->each(function (Tenant $tenant): void {
                $units = Unit::factory()
                    ->count(3)
                    ->for($tenant)
                    ->create();

                $users = $units->flatMap(function (Unit $unit) use ($tenant) {
                    return User::factory()
                        ->count(2)
                        ->for($tenant)
                        ->for($unit, 'unit')
                        ->create();
                });

                Inventory::factory()
                    ->count(5)
                    ->for($tenant)
                    ->create();

                $incidents = Incident::factory()
                    ->count(2)
                    ->for($tenant)
                    ->state(fn () => [
                        'status' => 'active',
                        'priority' => 'high',
                        'started_at' => now()->subHours(2),
                    ])
                    ->create();

                $incidents->each(function (Incident $incident) use ($tenant, $units, $users): void {
                    $tasks = Task::factory()
                    Task::factory()
                        ->count(3)
                        ->for($tenant)
                        ->for($incident)
                        ->state(function () use ($units, $users) {
                            $assignedUnit = $units->random();
                            $assignees = $users->where('unit_id', $assignedUnit->id);
                            $assignee = $assignees->isNotEmpty()
                                ? $assignees->random()
                                : $users->random();

                            return [
                                'status' => 'assigned',
                                'planned_start_at' => now()->addHour(),
                                'assigned_unit_id' => $assignedUnit->id,
                                'assigned_to' => $assignee->id,
                            ];
                        })
                        ->create();

                    $tasks->each(function (Task $task) use ($tenant, $users): void {
                        $assignee = $users->firstWhere('id', $task->assigned_to) ?? $users->random();

                        TrackingPing::factory()
                            ->count(2)
                            ->state(fn () => ['tenant_id' => $tenant->id])
                            ->for($assignee, 'user')
                            ->for($task)
                            ->sequence(
                                fn (int $sequence) => [
                                    'captured_at' => now()->subMinutes(10 - ($sequence * 3)),
                                    'speed' => $sequence === 0 ? 1.5 : 0.0,
                                ]
                            )
                            ->create();
                    });
                });
            });
    }
}

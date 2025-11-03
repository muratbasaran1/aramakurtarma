<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenant = Tenant::factory();
        $incidentFactory = Incident::factory()->for($tenant);
        $unitFactory = Unit::factory()->for($tenant);
        $assigneeFactory = User::factory()
            ->for($tenant)
            ->for($unitFactory, 'unit');

        return [
            'tenant_id' => $tenant,
            'incident_id' => $incidentFactory,
            'assigned_unit_id' => $unitFactory,
            'assigned_to' => $assigneeFactory,
            'status' => fake()->randomElement(['planned', 'assigned', 'in_progress', 'done', 'verified']),
            'requires_double_confirmation' => true,
            'planned_start_at' => now()->addHours(fake()->numberBetween(1, 12)),
            'completed_at' => null,
            'verified_at' => null,
            'context' => [
                'notes' => fake()->sentence(),
            ],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Task $task): void {
            $this->synchroniseTenant($task);
        })->afterCreating(function (Task $task): void {
            $this->synchroniseTenant($task, true);
        });
    }

    private function synchroniseTenant(Task $task, bool $persist = false): void
    {
        $incident = $task->relationLoaded('incident')
            ? $task->getRelation('incident')
            : $task->incident;

        if ($incident !== null && $task->tenant_id !== $incident->tenant_id) {
            $task->tenant()->associate($incident->tenant);

            if ($persist) {
                $task->saveQuietly();
            }
        }
    }
}

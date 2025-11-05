<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\Tenant;
use App\Models\TrackingPing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TrackingPing>
 */
class TrackingPingFactory extends Factory
{
    protected $model = TrackingPing::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenant = Tenant::factory();
        $userFactory = User::factory()->for($tenant);
        $taskFactory = Task::factory()->for($tenant);

        return [
            'tenant_id' => $tenant,
            'user_id' => $userFactory,
            'task_id' => $taskFactory,
            'latitude' => fake()->latitude(36.0, 42.0),
            'longitude' => fake()->longitude(26.0, 45.0),
            'speed' => fake()->randomFloat(2, 0, 20),
            'heading' => fake()->numberBetween(0, 359),
            'accuracy' => fake()->randomFloat(1, 2, 15),
            'captured_at' => Carbon::now()->subSeconds(fake()->numberBetween(0, 300)),
            'context' => [
                'battery' => fake()->numberBetween(15, 100),
            ],
        ];
    }
}

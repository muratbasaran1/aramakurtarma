<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = 'INC-' . fake()->unique()->numerify('####');

        return [
            'tenant_id' => Tenant::factory(),
            'code' => $code,
            'title' => fake()->sentence(3),
            'status' => fake()->randomElement(['open', 'active', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'context' => [
                'description' => fake()->paragraph(),
            ],
            'started_at' => now()->subHours(fake()->numberBetween(1, 12)),
            'closed_at' => null,
        ];
    }
}

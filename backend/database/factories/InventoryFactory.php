<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'code' => 'INV-' . fake()->unique()->numerify('#####'),
            'name' => fake()->words(3, true),
            'status' => fake()->randomElement(['active', 'service', 'retired']),
            'last_service_at' => now()->subDays(fake()->numberBetween(1, 60)),
            'attributes' => [
                'category' => fake()->randomElement(['vehicle', 'equipment', 'supply']),
            ],
        ];
    }
}

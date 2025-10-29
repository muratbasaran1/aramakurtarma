<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->jobTitle();

        return [
            'tenant_id' => Tenant::factory(),
            'name' => $name,
            'slug' => Str::slug($name . '-' . fake()->unique()->numerify('##')),
            'type' => fake()->randomElement(['command', 'logistics', 'medical', 'search-and-rescue']),
            'metadata' => [
                'capacity' => fake()->numberBetween(5, 25),
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'slug' => Str::slug($name . '-' . fake()->unique()->numerify('###')),
            'name' => $name,
            'timezone' => fake()->timezone(),
            'settings' => [
                'language' => fake()->randomElement(['tr', 'en']),
            ],
        ];
    }
}

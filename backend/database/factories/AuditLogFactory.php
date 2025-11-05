<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'user_id' => null,
            'event' => $this->faker->randomElement([
                'incident.created',
                'incident.updated',
                'task.updated',
                'unit.created',
                'user.updated',
            ]),
            'auditable_type' => Unit::class,
            'auditable_id' => 1,
            'payload' => [
                'changes' => [
                    'name' => $this->faker->words(3, true),
                ],
            ],
        ];
    }
}

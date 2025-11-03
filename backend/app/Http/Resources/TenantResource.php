<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tenant
 */
class TenantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->resource;

        return [
            'id' => $tenant->getKey(),
            'slug' => $tenant->slug,
            'name' => $tenant->name,
            'timezone' => $tenant->timezone,
            'settings' => $tenant->settings ?? [],
            'created_at' => $tenant->created_at?->toIso8601String(),
            'updated_at' => $tenant->updated_at?->toIso8601String(),
            'counts' => [
                'incidents' => [
                    'total' => $this->countAttribute('incidents_count'),
                    'open' => $this->countAttribute('incidents_open_count'),
                    'closed' => $this->countAttribute('incidents_closed_count'),
                ],
                'tasks' => [
                    'total' => $this->countAttribute('tasks_count'),
                    'active' => $this->countAttribute('tasks_active_count'),
                    'verified' => $this->countAttribute('tasks_verified_count'),
                ],
                'units' => [
                    'total' => $this->countAttribute('units_count'),
                ],
                'users' => [
                    'total' => $this->countAttribute('users_count'),
                    'active' => $this->countAttribute('users_active_count'),
                ],
                'inventories' => [
                    'total' => $this->countAttribute('inventories_count'),
                    'active' => $this->countAttribute('inventories_active_count'),
                    'service' => $this->countAttribute('inventories_service_count'),
                ],
            ],
        ];
    }

    private function countAttribute(string $key): int
    {
        $value = $this->resource->getAttribute($key);

        return is_numeric($value) ? (int) $value : 0;
    }
}

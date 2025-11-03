<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Inventory $inventory */
        $inventory = $this->resource;

        return [
            'id' => $inventory->id,
            'code' => $inventory->code,
            'name' => $inventory->name,
            'status' => $inventory->status,
            'last_service_at' => $inventory->last_service_at?->toIso8601String(),
            'attributes' => $inventory->getAttribute('attributes'),
            'created_at' => $inventory->created_at?->toIso8601String(),
            'updated_at' => $inventory->updated_at?->toIso8601String(),
        ];
    }
}

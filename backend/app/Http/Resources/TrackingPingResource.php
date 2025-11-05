<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TrackingPing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TrackingPing
 */
class TrackingPingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var TrackingPing $ping */
        $ping = $this->resource;

        return [
            'id' => $ping->getKey(),
            'user_id' => $ping->user_id,
            'task_id' => $ping->task_id,
            'latitude' => $ping->latitude,
            'longitude' => $ping->longitude,
            'speed' => $ping->speed,
            'heading' => $ping->heading,
            'accuracy' => $ping->accuracy,
            'captured_at' => $ping->captured_at?->toIso8601String(),
            'context' => $ping->context ?? [],
        ];
    }
}

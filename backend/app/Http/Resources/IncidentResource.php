<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Incident $incident */
        $incident = $this->resource;
        $tasksCount = $incident->getAttribute('tasks_count');
        $tasks = null;

        if (method_exists($incident, 'relationLoaded') && $incident->relationLoaded('tasks')) {
            $tasks = TaskResource::collection($incident->getRelationValue('tasks'))
                ->resolve();
        }

        return [
            'id' => $incident->id,
            'code' => $incident->code,
            'title' => $incident->title,
            'status' => $incident->status,
            'priority' => $incident->priority,
            'impact_area' => $incident->impact_area,
            'started_at' => $incident->started_at?->toIso8601String(),
            'closed_at' => $incident->closed_at?->toIso8601String(),
            'context' => $incident->context,
            'tasks_count' => $this->when(
                $tasksCount !== null,
                static fn (): int => (int) $tasksCount
            ),
            'tasks' => $this->when(
                $tasks !== null,
                fn () => $tasks
            ),
            'created_at' => $incident->created_at?->toIso8601String(),
            'updated_at' => $incident->updated_at?->toIso8601String(),
        ];
    }
}

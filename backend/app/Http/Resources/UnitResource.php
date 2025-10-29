<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class UnitResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Unit $unit */
        $unit = $this->resource;

        /** @var Collection<int, \App\Models\User>|null $usersRelation */
        $usersRelation = $unit->relationLoaded('users')
            ? $unit->getRelationValue('users')
            : null;

        /** @var Collection<int, \App\Models\Task>|null $tasksRelation */
        $tasksRelation = $unit->relationLoaded('tasks')
            ? $unit->getRelationValue('tasks')
            : null;

        $usersPayload = null;

        if ($usersRelation instanceof Collection) {
            $usersPayload = $usersRelation->map(static function ($user): array {
                /** @var \App\Models\User $user */

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                ];
            })->all();
        }

        $recentTasksPayload = null;

        if ($tasksRelation instanceof Collection) {
            $recentTasksPayload = $tasksRelation->map(static function ($task): array {
                /** @var \App\Models\Task $task */
                $incident = $task->relationLoaded('incident') ? $task->getRelationValue('incident') : null;

                return [
                    'id' => $task->id,
                    'incident_id' => $task->incident_id,
                    'status' => $task->status,
                    'requires_double_confirmation' => $task->requires_double_confirmation,
                    'planned_start_at' => $task->planned_start_at?->toIso8601String(),
                    'completed_at' => $task->completed_at?->toIso8601String(),
                    'incident' => $incident === null ? null : [
                        'id' => $incident->id,
                        'code' => $incident->code,
                        'title' => $incident->title,
                        'status' => $incident->status,
                        'priority' => $incident->priority,
                    ],
                ];
            })->all();
        }

        return [
            'id' => $unit->id,
            'tenant_id' => $unit->tenant_id,
            'name' => $unit->name,
            'slug' => $unit->slug,
            'type' => $unit->type,
            'metadata' => $unit->metadata,
            'users_count' => $unit->users_count ?? null,
            'tasks_count' => $unit->tasks_count ?? null,
            'active_tasks_count' => $unit->active_tasks_count ?? null,
            'users' => $this->when($usersPayload !== null, $usersPayload),
            'recent_tasks' => $this->when($recentTasksPayload !== null, $recentTasksPayload),
            'created_at' => $unit->created_at?->toIso8601String(),
            'updated_at' => $unit->updated_at?->toIso8601String(),
        ];
    }
}

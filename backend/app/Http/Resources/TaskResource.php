<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Task $task */
        $task = $this->resource;
        $incidentRelation = null;
        $assignedUnitRelation = null;
        $assigneeRelation = null;

        if (method_exists($task, 'relationLoaded')) {
            if ($task->relationLoaded('incident')) {
                $incidentRelation = $task->getRelationValue('incident');
            }

            if ($task->relationLoaded('assignedUnit')) {
                $assignedUnitRelation = $task->getRelationValue('assignedUnit');
            }

            if ($task->relationLoaded('assignee')) {
                $assigneeRelation = $task->getRelationValue('assignee');
            }
        }

        return [
            'id' => $task->id,
            'incident_id' => $task->incident_id,
            'incident' => $this->when(
                $incidentRelation !== null,
                function () use ($incidentRelation): array {
                    return [
                        'id' => $incidentRelation?->id,
                        'code' => $incidentRelation?->code,
                        'title' => $incidentRelation?->title,
                        'status' => $incidentRelation?->status,
                        'priority' => $incidentRelation?->priority,
                    ];
                }
            ),
            'assigned_unit' => $this->when(
                $assignedUnitRelation !== null,
                function () use ($assignedUnitRelation): array {
                    return [
                        'id' => $assignedUnitRelation?->id,
                        'name' => $assignedUnitRelation?->name,
                    ];
                }
            ),
            'assignee' => $this->when(
                $assigneeRelation !== null,
                function () use ($assigneeRelation): array {
                    return [
                        'id' => $assigneeRelation?->id,
                        'name' => $assigneeRelation?->name,
                        'email' => $assigneeRelation?->email,
                    ];
                }
            ),
            'status' => $task->status,
            'route' => $task->route,
            'requires_double_confirmation' => $task->requires_double_confirmation,
            'planned_start_at' => $task->planned_start_at?->toIso8601String(),
            'completed_at' => $task->completed_at?->toIso8601String(),
            'verified_at' => $task->verified_at?->toIso8601String(),
            'context' => $task->context,
            'created_at' => $task->created_at?->toIso8601String(),
            'updated_at' => $task->updated_at?->toIso8601String(),
        ];
    }
}

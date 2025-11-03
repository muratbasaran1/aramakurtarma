<?php

declare(strict_types=1);

namespace App\OpsCenter;

use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class OpsCenterSummary
{
    /**
     * @param array<string, int> $incidentCounts
     * @param array<string, int> $taskCounts
     * @param array<string, int> $inventoryCounts
     * @param Collection<int, Incident> $recentIncidents
     * @param Collection<int, Task> $recentTasks
     * @param Collection<int, Unit> $units
     */
    private function __construct(
        private readonly array $incidentCounts,
        private readonly array $taskCounts,
        private readonly array $inventoryCounts,
        private readonly Collection $recentIncidents,
        private readonly Collection $recentTasks,
        private readonly Collection $units,
    ) {
    }

    public static function forTenant(Tenant $tenant): self
    {
        $incidentCounts = self::groupCounts(
            Incident::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

        $taskCounts = self::groupCounts(
            Task::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

        $inventoryCounts = self::groupCounts(
            Inventory::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

        /** @var Collection<int, Incident> $recentIncidents */
        $recentIncidents = Incident::forTenantQuery($tenant)
            ->withCount('tasks')
            ->orderByDesc('started_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get([
                'id',
                'code',
                'title',
                'status',
                'priority',
                'started_at',
                'closed_at',
            ]);

        /** @var Collection<int, Task> $recentTasks */
        $recentTasks = Task::forTenantQuery($tenant)
            ->with([
                'incident:id,code,title',
                'assignedUnit:id,name',
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get([
                'id',
                'incident_id',
                'assigned_unit_id',
                'status',
                'planned_start_at',
                'completed_at',
                'verified_at',
                'created_at',
                'updated_at',
            ]);

        /** @var Collection<int, Unit> $units */
        $units = Unit::forTenantQuery($tenant)
            ->withCount([
                'users',
                'tasks as active_tasks_count' => static function (Builder $query): void {
                    $query->whereIn('status', [
                        Task::STATUS_ASSIGNED,
                        Task::STATUS_IN_PROGRESS,
                    ]);
                },
            ])
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'type']);

        return new self(
            $incidentCounts,
            $taskCounts,
            $inventoryCounts,
            $recentIncidents,
            $recentTasks,
            $units,
        );
    }

    /**
     * @return array{
     *     incidentCounts: array<string, int>,
     *     taskCounts: array<string, int>,
     *     inventoryCounts: array<string, int>,
     *     recentIncidents: Collection<int, Incident>,
     *     recentTasks: Collection<int, Task>,
     *     units: Collection<int, Unit>
     * }
     */
    public function toViewData(): array
    {
        return [
            'incidentCounts' => $this->incidentCounts,
            'taskCounts' => $this->taskCounts,
            'inventoryCounts' => $this->inventoryCounts,
            'recentIncidents' => $this->recentIncidents,
            'recentTasks' => $this->recentTasks,
            'units' => $this->units,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiArray(Tenant $tenant): array
    {
        return [
            'generated_at' => Carbon::now()->toIso8601String(),
            'tenant' => [
                'id' => $tenant->getKey(),
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'timezone' => $tenant->timezone,
            ],
            'counts' => [
                'incidents' => $this->incidentCounts,
                'tasks' => $this->taskCounts,
                'inventory' => $this->inventoryCounts,
            ],
            'recent' => [
                'incidents' => $this->recentIncidents
                    ->map(static function (Incident $incident): array {
                        $tasksCount = (int) $incident->getAttribute('tasks_count');

                        return [
                            'id' => $incident->getKey(),
                            'code' => $incident->code,
                            'title' => $incident->title,
                            'status' => $incident->status,
                            'priority' => $incident->priority,
                            'started_at' => $incident->started_at?->toIso8601String(),
                            'closed_at' => $incident->closed_at?->toIso8601String(),
                            'tasks_count' => $tasksCount,
                        ];
                    })
                    ->values()
                    ->all(),
                'tasks' => $this->recentTasks
                    ->map(static function (Task $task): array {
                        $incidentRelation = $task->relationLoaded('incident')
                            ? $task->getRelation('incident')
                            : null;
                        $assignedUnitRelation = $task->relationLoaded('assignedUnit')
                            ? $task->getRelation('assignedUnit')
                            : null;

                        $incidentData = $incidentRelation instanceof Incident
                            ? [
                                'id' => $incidentRelation->getKey(),
                                'code' => $incidentRelation->code,
                                'title' => $incidentRelation->title,
                            ]
                            : null;

                        $assignedUnitData = $assignedUnitRelation instanceof Unit
                            ? [
                                'id' => $assignedUnitRelation->getKey(),
                                'name' => $assignedUnitRelation->name,
                            ]
                            : null;

                        return [
                            'id' => $task->getKey(),
                            'status' => $task->status,
                            'planned_start_at' => $task->planned_start_at?->toIso8601String(),
                            'completed_at' => $task->completed_at?->toIso8601String(),
                            'verified_at' => $task->verified_at?->toIso8601String(),
                            'updated_at' => $task->updated_at?->toIso8601String(),
                            'incident' => $incidentData,
                            'assigned_unit' => $assignedUnitData,
                        ];
                    })
                    ->values()
                    ->all(),
            ],
            'units' => $this->units
                ->map(static function (Unit $unit): array {
                    $usersCount = (int) $unit->getAttribute('users_count');
                    $activeTasksCount = (int) $unit->getAttribute('active_tasks_count');

                    return [
                        'id' => $unit->getKey(),
                        'name' => $unit->name,
                        'slug' => $unit->slug,
                        'type' => $unit->type,
                        'users_count' => $usersCount,
                        'active_tasks_count' => $activeTasksCount,
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @param Collection<string, int|string> $counts
     * @return array<string, int>
     */
    private static function groupCounts(Collection $counts): array
    {
        return $counts
            ->mapWithKeys(static fn ($value, string $key): array => [$key => (int) $value])
            ->toArray();
    }
}

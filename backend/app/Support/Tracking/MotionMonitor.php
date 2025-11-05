<?php

declare(strict_types=1);

namespace App\Support\Tracking;

use App\Models\Task;
use App\Models\TrackingPing;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Collection;

class MotionMonitor
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly int $noMotionThresholdSeconds = 120,
        private readonly float $distanceThresholdMeters = 5.0,
        private readonly float $speedThresholdMeters = 0.3
    ) {
    }

    public function evaluate(TrackingPing $ping): void
    {
        $task = $ping->getRelationValue('task');

        if (! $task instanceof Task || $task->status !== Task::STATUS_IN_PROGRESS) {
            return;
        }

        $previousPings = $this->previousPings($ping);

        if ($previousPings->isEmpty()) {
            return;
        }

        /** @var TrackingPing $previous */
        $previous = $previousPings->first();

        if (! $ping->wasStationaryComparedTo($previous, $this->distanceThresholdMeters, $this->speedThresholdMeters)) {
            return;
        }

        if ($ping->captured_at === null || $previous->captured_at === null) {
            return;
        }

        $duration = $ping->captured_at->diffInSeconds($previous->captured_at, true);

        if ($duration < $this->noMotionThresholdSeconds) {
            return;
        }

        /** @var TrackingPing|null $beforePrevious */
        $beforePrevious = $previousPings->get(1);

        if (
            $beforePrevious !== null
            && $previous->wasStationaryComparedTo(
                $beforePrevious,
                $this->distanceThresholdMeters,
                $this->speedThresholdMeters
            )
        ) {
            return;
        }

        $this->auditLogger->record(
            'tracking.no_motion',
            $ping,
            [
                'tenant_id' => $ping->tenant_id,
                'changes' => [
                    'user_id' => $ping->user_id,
                    'task_id' => $ping->task_id,
                    'duration_seconds' => $duration,
                ],
                'meta' => [
                    'reference_ping_id' => $previous->getKey(),
                    'current_ping_id' => $ping->getKey(),
                ],
            ]
        );
    }

    /**
     * @return Collection<int, TrackingPing>
     */
    private function previousPings(TrackingPing $ping): Collection
    {
        $query = TrackingPing::query()
            ->where('tenant_id', $ping->tenant_id)
            ->where('user_id', $ping->user_id);

        if ($ping->task_id !== null) {
            $query->where('task_id', $ping->task_id);
        } else {
            $query->whereNull('task_id');
        }

        /** @var Collection<int, TrackingPing> $pings */
        $pings = $query
            ->where('captured_at', '<=', $ping->captured_at)
            ->where('id', '<>', $ping->getKey())
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        return $pings;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
=======
use App\Models\Tenant;
use App\OpsCenter\OpsCenterSummary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
use Illuminate\Support\Facades\Redirect;

class OpsCenterController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $tenants = Tenant::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        if ($tenants->isEmpty()) {
            return view('opscenter', [
                'tenants' => $tenants,
                'tenant' => null,
                'summary' => [
                    'incidentCounts' => [],
                    'taskCounts' => [],
                    'inventoryCounts' => [],
                    'recentIncidents' => collect(),
                    'recentTasks' => collect(),
                    'units' => collect(),
                ],
            ]);
        }

<<<<<<< HEAD
        $requestedSlug = $request->string('tenant')->toString();
=======
        $requestedSlug = (string) $request->query('tenant', '');
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
        $activeSlug = $requestedSlug !== '' ? $requestedSlug : (string) $tenants->first()->slug;

        $tenant = Tenant::query()
            ->where('slug', $activeSlug)
            ->first();

<<<<<<< HEAD
        if ($tenant === null) {
            return Redirect::route('opscenter', ['tenant' => $tenants->first()->slug]);
        }

        $incidentCounts = $this->groupCounts(
            Incident::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

        $taskCounts = $this->groupCounts(
            Task::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

        $inventoryCounts = $this->groupCounts(
            Inventory::forTenantQuery($tenant)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
        );

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

        $units = Unit::forTenantQuery($tenant)
            ->withCount([
                'users',
                'tasks as active_tasks_count' => fn ($query) => $query->whereIn('status', ['assigned', 'in_progress']),
            ])
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'type']);
=======
        if (! $tenant instanceof Tenant) {
            return Redirect::route('opscenter', ['tenant' => $tenants->first()->slug]);
        }

        $summary = OpsCenterSummary::forTenant($tenant)->toViewData();
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)

        return view('opscenter', [
            'tenants' => $tenants,
            'tenant' => $tenant,
<<<<<<< HEAD
            'summary' => [
                'incidentCounts' => $incidentCounts,
                'taskCounts' => $taskCounts,
                'inventoryCounts' => $inventoryCounts,
                'recentIncidents' => $recentIncidents,
                'recentTasks' => $recentTasks,
                'units' => $units,
            ],
        ]);
    }

    /**
     * @param Collection<string, int|string> $counts
     * @return array<string, int>
     */
    private function groupCounts(Collection $counts): array
    {
        return $counts
            ->mapWithKeys(fn ($value, string $key): array => [$key => (int) $value])
            ->toArray();
    }
=======
            'summary' => $summary,
        ]);
    }
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
}

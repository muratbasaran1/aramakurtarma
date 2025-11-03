<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\OpsCenter\OpsCenterSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TenantController extends Controller
{
    use InterpretsFilters;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Tenant::query()
            ->withCount($this->countDefinitions())
            ->orderBy('name');

        $slug = $request->query('slug');

        if (\is_string($slug) && $slug !== '') {
            $query->where('slug', $slug);
        }

        $search = $request->query('search');

        if (\is_string($search) && $search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', $search . '%');
            });
        }

        $perPage = $this->extractPerPage($request);

        return TenantResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(Request $request, Tenant $tenant): TenantResource
    {
        $tenant->loadCount($this->countDefinitions());

        $resource = TenantResource::make($tenant);

        if ($request->boolean('include_summary', true)) {
            $summary = OpsCenterSummary::forTenant($tenant)->toApiArray($tenant);

            $resource->additional([
                'summary' => $summary,
            ]);
        }

        return $resource;
    }

    /**
     * @return array<int|string, mixed>
     */
    private function countDefinitions(): array
    {
        return [
            'incidents',
            'incidents as incidents_open_count' => static function (Builder $query): void {
                $query->whereIn('status', [Incident::STATUS_OPEN, Incident::STATUS_ACTIVE]);
            },
            'incidents as incidents_closed_count' => static function (Builder $query): void {
                $query->where('status', Incident::STATUS_CLOSED);
            },
            'tasks',
            'tasks as tasks_active_count' => static function (Builder $query): void {
                $query->whereIn('status', [Task::STATUS_ASSIGNED, Task::STATUS_IN_PROGRESS]);
            },
            'tasks as tasks_verified_count' => static function (Builder $query): void {
                $query->where('status', Task::STATUS_VERIFIED);
            },
            'units',
            'users',
            'users as users_active_count' => static function (Builder $query): void {
                $query->where('status', User::STATUS_ACTIVE);
            },
            'inventories',
            'inventories as inventories_active_count' => static function (Builder $query): void {
                $query->where('status', Inventory::STATUS_ACTIVE);
            },
            'inventories as inventories_service_count' => static function (Builder $query): void {
                $query->where('status', Inventory::STATUS_SERVICE);
            },
        ];
    }
}

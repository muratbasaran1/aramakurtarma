<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
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
                $query->whereIn('status', ['open', 'active']);
            },
            'incidents as incidents_closed_count' => static function (Builder $query): void {
                $query->where('status', 'closed');
            },
            'tasks',
            'tasks as tasks_active_count' => static function (Builder $query): void {
                $query->whereIn('status', ['assigned', 'in_progress']);
            },
            'tasks as tasks_verified_count' => static function (Builder $query): void {
                $query->where('status', 'verified');
            },
            'units',
            'users',
            'users as users_active_count' => static function (Builder $query): void {
                $query->where('status', 'active');
            },
            'inventories',
            'inventories as inventories_active_count' => static function (Builder $query): void {
                $query->where('status', 'active');
            },
            'inventories as inventories_service_count' => static function (Builder $query): void {
                $query->where('status', 'service');
            },
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tenants\StoreTenantRequest;
use App\Http\Requests\Api\Tenants\UpdateTenantRequest;
use App\Http\Resources\TenantResource;
use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\OpsCenter\OpsCenterSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

use function abort_if;

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

    public function store(StoreTenantRequest $request): JsonResponse
    {
        /** @var Tenant $tenant */
        $tenant = Tenant::query()->create($request->validated());

        return $this->makeTenantResource($request, $tenant)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Tenant $tenant): TenantResource
    {
        return $this->makeTenantResource($request, $tenant);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): TenantResource
    {
        $validated = $request->validated();

        if ($validated !== []) {
            $tenant->fill($validated);
            $tenant->save();
        }

        return $this->makeTenantResource($request, $tenant);
    }

    public function destroy(Tenant $tenant): Response
    {
        abort_if($tenant->units()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Aktif birimleri bulunan tenant silinemez.');
        abort_if($tenant->users()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Kullanıcıları bulunan tenant silinemez.');
        abort_if($tenant->incidents()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Olay kayıtları bulunan tenant silinemez.');
        abort_if($tenant->tasks()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Görev kayıtları bulunan tenant silinemez.');
        abort_if($tenant->inventories()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Envanter kayıtları bulunan tenant silinemez.');

        $tenant->delete();

        return response()->noContent();
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

    private function makeTenantResource(Request $request, Tenant $tenant): TenantResource
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
}

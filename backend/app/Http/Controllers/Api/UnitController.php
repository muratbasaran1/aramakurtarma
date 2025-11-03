<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Http\Requests\Api\Units\StoreUnitRequest;
use App\Http\Requests\Api\Units\UpdateUnitRequest;
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
use App\Http\Resources\UnitResource;
use App\Models\Tenant;
use App\Models\Unit;
use App\Tenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;
=======
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

use function abort_if;
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)

class UnitController extends Controller
{
    use InterpretsFilters;

<<<<<<< HEAD
    /**
     * @var list<string>
     */
    private const TYPES = ['command', 'logistics', 'medical', 'search-and-rescue'];

=======
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = Unit::forTenantQuery($tenant)
            ->withCount([
                'users',
                'tasks',
                'tasks as active_tasks_count' => static function (Builder $builder): void {
                    $builder->whereIn('status', ['planned', 'assigned', 'in_progress']);
                },
            ])
            ->orderBy('name');

<<<<<<< HEAD
        $types = $this->extractListFilter($request, 'type', self::TYPES);
=======
        $types = $this->extractListFilter($request, 'type', Unit::TYPES);
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)

        if ($types !== []) {
            $query->whereIn('type', $types);
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

        return UnitResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $tenant, string $unit): UnitResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

<<<<<<< HEAD
        $model = Unit::forTenantQuery($contextTenant)
            ->where(static function (Builder $builder) use ($unit): void {
                $builder->whereKey($unit);

                if (! ctype_digit($unit)) {
                    $builder->orWhere('slug', $unit);
                }
            })
=======
        $model = $this->resolveUnit($contextTenant, $unit);

        return new UnitResource($this->loadUnitRelations($model));
    }

    public function store(StoreUnitRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $contextTenant->getKey();

        $unit = Unit::query()->create($validated);
        /** @var Unit $unit */
        $unit = $unit;
        $unit->refresh();
        $unit = $this->loadUnitRelations($unit);

        return (new UnitResource($unit))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateUnitRequest $request, string $tenant, string $unit): UnitResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $model = $request->resolvedUnit();

        $validated = $request->validated();

        $model->fill($validated);
        $model->save();
        $model->refresh();

        return new UnitResource($this->loadUnitRelations($model));
    }

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }

    private function resolveUnit(Tenant $tenant, string $identifier): Unit
    {
        $unit = Unit::findForTenantByIdentifier($tenant, $identifier);

        abort_if($unit === null, Response::HTTP_NOT_FOUND, 'Birim bulunamadı.');

        if (! $unit instanceof Unit) {
            throw new RuntimeException('Beklenmedik birim modeli alındı.');
        }

        return $unit;
    }

    private function loadUnitRelations(Unit $unit): Unit
    {
        /** @var Unit $fresh */
        $fresh = Unit::query()
            ->whereKey($unit->getKey())
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
            ->withCount([
                'users',
                'tasks',
                'tasks as active_tasks_count' => static function (Builder $builder): void {
                    $builder->whereIn('status', ['planned', 'assigned', 'in_progress']);
                },
            ])
            ->with([
                'users' => static function ($builder): void {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\User> $builder */
                    $builder
                        ->select(['id', 'unit_id', 'name', 'email', 'role', 'status'])
                        ->orderBy('name');
                },
                'tasks' => static function ($builder): void {
                    /** @var \Illuminate\Database\Eloquent\Builder<\App\Models\Task> $builder */
                    $builder
                        ->with(['incident:id,code,title,status,priority'])
                        ->select([
                            'id',
                            'assigned_unit_id',
                            'incident_id',
                            'status',
                            'requires_double_confirmation',
                            'planned_start_at',
                            'completed_at',
                        ])
                        ->orderByDesc('planned_start_at')
                        ->orderByDesc('id')
                        ->limit(10);
                },
            ])
            ->firstOrFail();

<<<<<<< HEAD
        return new UnitResource($model);
    }

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
=======
        return $fresh;
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
    }
}

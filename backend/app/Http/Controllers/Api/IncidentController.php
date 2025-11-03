<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Incidents\StoreIncidentRequest;
use App\Http\Requests\Api\Incidents\UpdateIncidentRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends Controller
{
    use InterpretsFilters;

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = Incident::forTenantQuery($tenant)
            ->withCount('tasks')
            ->orderByDesc('started_at')
            ->orderByDesc('id');

        $statuses = $this->extractListFilter($request, 'status', Incident::STATUSES);

        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        $priorities = $this->extractListFilter($request, 'priority', Incident::PRIORITIES);

        if ($priorities !== []) {
            $query->whereIn('priority', $priorities);
        }

        $code = $request->query('code');

        if (\is_string($code) && $code !== '') {
            $query->where('code', 'like', $code . '%');
        }

        $search = $request->query('search');

        if (\is_string($search) && $search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', $search . '%');
            });
        }

        $perPage = $this->extractPerPage($request);

        return IncidentResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $tenant, string $incident): IncidentResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $model = Incident::forTenantQuery($contextTenant)
            ->with([
                'tasks' => function (Relation $taskQuery): void {
                    $taskQuery
                        ->with(['assignedUnit', 'assignee'])
                        ->orderByDesc('planned_start_at')
                        ->orderByDesc('id');
                },
            ])
            ->withCount('tasks')
            ->findOrFail($incident);

        return new IncidentResource($model);
    }

    public function store(StoreIncidentRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $contextTenant->id;

        $validated['status'] = $validated['status'] ?? 'open';
        $validated['priority'] = $validated['priority'] ?? 'medium';

        $impactArea = $validated['impact_area'] ?? null;

        if ($impactArea === null) {
            unset($validated['impact_area']);
        } else {
            $validated['impact_area'] = $this->prepareImpactArea($impactArea);
        }

        $incident = Incident::query()->create($validated);
        $incident->refresh();

        return (new IncidentResource($incident))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateIncidentRequest $request, string $tenant, Incident $incident): IncidentResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        if ($incident->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();

        if (\array_key_exists('impact_area', $validated)) {
            $impactArea = $validated['impact_area'];

            if ($impactArea === null) {
                $validated['impact_area'] = null;
            } else {
                $validated['impact_area'] = $this->prepareImpactArea($impactArea);
            }
        }

        $incident->fill($validated);
        $incident->save();
        $incident->refresh();

        return new IncidentResource($incident);
    }

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }

    /**
     * @param array<string, mixed> $impactArea
     */
    private function prepareImpactArea(array $impactArea): array|Expression
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $impactArea;
        }

        try {
            $geoJson = json_encode($impactArea, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Impact alanı GeoJSON formatında olmalıdır.', 0, $exception);
        }

        return DB::raw(\sprintf("ST_GeomFromGeoJSON('%s')", $geoJson));
    }
}

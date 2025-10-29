<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tasks\StoreTaskRequest;
use App\Http\Requests\Api\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    use InterpretsFilters;

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = Task::forTenantQuery($tenant)
            ->with(['incident:id,code,title,status', 'assignedUnit:id,name', 'assignee:id,name,email,unit_id'])
            ->orderByDesc('planned_start_at')
            ->orderByDesc('id');

        $statuses = $this->extractListFilter($request, 'status', Task::STATUSES);

        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        $incidentId = $request->query('incident_id');

        if ($incidentId !== null && $incidentId !== '') {
            $query->where('incident_id', (int) $incidentId);
        }

        $requiresConfirmation = $this->extractBoolean($request, 'requires_confirmation');

        if ($requiresConfirmation !== null) {
            $query->where('requires_double_confirmation', $requiresConfirmation);
        }

        $perPage = $this->extractPerPage($request);

        return TaskResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $tenant, string $task): TaskResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $model = Task::forTenantQuery($contextTenant)
            ->with([
                'incident:id,tenant_id,code,title,status,priority',
                'assignedUnit:id,name',
                'assignee:id,name,email,unit_id',
            ])
            ->findOrFail($task);

        return new TaskResource($model);
    }

    public function store(StoreTaskRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();

        $validated['tenant_id'] = $contextTenant->getKey();
        $validated['status'] = $validated['status'] ?? 'planned';

        if (\array_key_exists('route', $validated)) {
            $route = $validated['route'];

            $validated['route'] = $route === null
                ? null
                : $this->prepareRoute($route);
        }

        $task = Task::query()->create($validated);
        $task->refresh();
        $task->load([
            'incident:id,tenant_id,code,title,status,priority',
            'assignedUnit:id,name',
            'assignee:id,name,email,unit_id',
        ]);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTaskRequest $request, string $tenant, Task $task): TaskResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        if ($task->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();

        if (\array_key_exists('route', $validated)) {
            $route = $validated['route'];

            $validated['route'] = $route === null
                ? null
                : $this->prepareRoute($route);
        }

        $task->fill($validated);
        $task->save();
        $task->refresh();
        $task->load([
            'incident:id,tenant_id,code,title,status,priority',
            'assignedUnit:id,name',
            'assignee:id,name,email,unit_id',
        ]);

        return new TaskResource($task);
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
     * @param array<string, mixed> $route
     */
    private function prepareRoute(array $route): array|ExpressionContract
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $route;
        }

        try {
            $geoJson = json_encode($route, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Rota GeoJSON formatında olmalıdır.', 0, $exception);
        }

        return DB::raw(\sprintf("ST_GeomFromGeoJSON('%s')", $geoJson));
    }
}

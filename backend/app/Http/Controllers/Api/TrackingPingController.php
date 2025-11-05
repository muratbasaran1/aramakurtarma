<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tracking\StoreTrackingPingRequest;
use App\Http\Resources\TrackingPingResource;
use App\Models\Tenant;
use App\Models\TrackingPing;
use App\Support\Audit\AuditLogger;
use App\Support\Tracking\MotionMonitor;
use App\Tenant\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TrackingPingController extends Controller
{
    use InterpretsFilters;

    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly AuditLogger $auditLogger,
        private readonly MotionMonitor $motionMonitor
    ) {
    }

    public function index(Request $request, string $tenant): AnonymousResourceCollection
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $query = TrackingPing::forTenantQuery($contextTenant)
            ->with([
                'task:id,tenant_id,code,status',
                'user:id,tenant_id,name,role,unit_id',
            ])
            ->orderByDesc('captured_at')
            ->orderByDesc('id');

        $userId = $request->query('user_id');

        if ($userId !== null) {
            $query->where('user_id', (int) $userId);
        }

        $taskId = $request->query('task_id');

        if ($taskId !== null) {
            $query->where('task_id', (int) $taskId);
        }

        $since = $request->query('since');

        if ($since !== null) {
            $query->where('captured_at', '>=', $this->parseDateFilter($since, 'since'));
        }

        $until = $request->query('until');

        if ($until !== null) {
            $query->where('captured_at', '<=', $this->parseDateFilter($until, 'until'));
        }

        $perPage = $this->extractPerPage($request);

        return TrackingPingResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function latest(Request $request, string $tenant): AnonymousResourceCollection
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $query = TrackingPing::forTenantQuery($contextTenant)
            ->with([
                'task:id,tenant_id,code,status',
                'user:id,tenant_id,name,role,unit_id',
            ])
            ->orderByDesc('captured_at')
            ->orderByDesc('id');

        $userId = $request->query('user_id');

        if ($userId !== null) {
            $query->where('user_id', (int) $userId);
        }

        $taskId = $request->query('task_id');

        if ($taskId !== null) {
            $query->where('task_id', (int) $taskId);
        }

        $since = $request->query('since');

        if ($since !== null) {
            $query->where('captured_at', '>=', $this->parseDateFilter($since, 'since'));
        }

        $pings = $query->get()->unique('user_id')->values();

        $limit = $request->query('limit');

        if ($limit !== null) {
            $limit = max((int) $limit, 0);

            if ($limit > 0) {
                $pings = $pings->take($limit)->values();
            }
        }

        return TrackingPingResource::collection($pings);
    }

    public function store(StoreTrackingPingRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $contextTenant->getKey();

        /** @var TrackingPing $ping */
        $ping = TrackingPing::query()->create($validated);
        $ping->load([
            'task:id,tenant_id,status',
            'user:id,tenant_id',
        ]);

        $this->auditLogger->record('tracking.ping_recorded', $ping, [
            'changes' => [
                'user_id' => $ping->user_id,
                'task_id' => $ping->task_id,
                'captured_at' => $ping->captured_at?->toIso8601String(),
            ],
        ]);

        $this->motionMonitor->evaluate($ping);

        return (new TrackingPingResource($ping))
            ->response()
            ->setStatusCode(201);
    }

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }

    private function parseDateFilter(mixed $value, string $field): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (! \is_string($value)) {
            throw ValidationException::withMessages([
                $field => \sprintf('%s değeri ISO-8601 biçiminde olmalıdır.', $field),
            ]);
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                $field => \sprintf('%s değeri ISO-8601 biçiminde olmalıdır.', $field),
            ]);
        }
    }
}

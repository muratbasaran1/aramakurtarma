<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Inventories\StoreInventoryRequest;
use App\Http\Requests\Api\Inventories\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    use InterpretsFilters;

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = Inventory::forTenantQuery($tenant)
            ->orderBy('code');

        $statuses = $this->extractListFilter($request, 'status', Inventory::STATUSES);

        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        $code = $request->query('code');

        if (\is_string($code) && $code !== '') {
            $query->where('code', 'like', $code . '%');
        }

        $search = $request->query('search');

        if (\is_string($search) && $search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $perPage = $this->extractPerPage($request);

        return InventoryResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $tenant, Inventory $inventory): InventoryResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        if ($inventory->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return new InventoryResource($inventory);
    }

    public function store(StoreInventoryRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $contextTenant->getKey();
        $validated['status'] = $validated['status'] ?? Inventory::STATUS_ACTIVE;

        $inventory = Inventory::query()->create($validated);
        $inventory->refresh();

        return (new InventoryResource($inventory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateInventoryRequest $request, string $tenant, Inventory $inventory): InventoryResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        if ($inventory->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();

        $inventory->fill($validated);
        $inventory->save();
        $inventory->refresh();

        return new InventoryResource($inventory);
    }

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }
}

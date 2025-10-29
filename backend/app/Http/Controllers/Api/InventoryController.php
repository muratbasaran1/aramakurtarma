<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class InventoryController extends Controller
{
    use InterpretsFilters;

    /**
     * @var list<string>
     */
    private const STATUSES = ['active', 'service', 'retired'];

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = Inventory::forTenantQuery($tenant)
            ->orderBy('code');

        $statuses = $this->extractListFilter($request, 'status', self::STATUSES);

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

    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Tenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class UserController extends Controller
{
    use InterpretsFilters;

    /**
     * @var list<string>
     */
    private const STATUSES = ['active', 'inactive', 'suspended'];

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tenant = $this->tenant();

        $query = User::forTenantQuery($tenant)
            ->with('unit:id,name')
            ->orderBy('name');

        $statuses = $this->extractListFilter($request, 'status', self::STATUSES);

        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }

        $role = $request->query('role');

        if (\is_string($role) && $role !== '') {
            $query->where('role', $role);
        }

        $unitId = $request->query('unit_id');

        if ($unitId !== null && $unitId !== '') {
            $query->where('unit_id', (int) $unitId);
        }

        $search = $request->query('search');

        if (\is_string($search) && $search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', $search . '%')
                    ->orWhere('phone', 'like', $search . '%');
            });
        }

        $perPage = $this->extractPerPage($request);

        return UserResource::collection(
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

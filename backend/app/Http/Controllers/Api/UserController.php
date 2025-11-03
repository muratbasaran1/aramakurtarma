<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
<<<<<<< HEAD
=======
use App\Http\Requests\Api\Users\StoreUserRequest;
use App\Http\Requests\Api\Users\UpdateUserRequest;
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User;
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
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)

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

<<<<<<< HEAD
=======
    public function show(string $tenant, User $user): UserResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant || $user->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $user->loadMissing('unit:id,name');

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request, string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $contextTenant->getKey();
        $validated['status'] = $validated['status'] ?? 'active';

        $user = User::query()->create($validated);
        $user->load('unit:id,name');

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, string $tenant, User $user): UserResource
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant || $user->tenant_id !== $contextTenant->getKey()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();

        $user->fill($validated);
        $user->save();
        $user->refresh();
        $user->load('unit:id,name');

        return new UserResource($user);
    }

>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
    private function tenant(): Tenant
    {
        $tenant = $this->tenantContext->tenant();

        if ($tenant === null) {
            throw new RuntimeException('Aktif tenant bağlamı bulunamadı.');
        }

        return $tenant;
    }
}

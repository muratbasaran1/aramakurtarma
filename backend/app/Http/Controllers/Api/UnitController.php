<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Tenant;
use App\Models\Unit;
use App\Tenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class UnitController extends Controller
{
    use InterpretsFilters;

    /**
     * @var list<string>
     */
    private const TYPES = ['command', 'logistics', 'medical', 'search-and-rescue'];

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

        $types = $this->extractListFilter($request, 'type', self::TYPES);

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

        $model = Unit::forTenantQuery($contextTenant)
            ->where(static function (Builder $builder) use ($unit): void {
                $builder->whereKey($unit);

                if (! ctype_digit($unit)) {
                    $builder->orWhere('slug', $unit);
                }
            })
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

        return new UnitResource($model);
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

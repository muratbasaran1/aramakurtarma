<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InterpretsFilters;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AuditLogController extends Controller
{
    use InterpretsFilters;

    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function index(Request $request, string $tenant): AnonymousResourceCollection
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $query = AuditLog::query()
            ->where('tenant_id', $contextTenant->getKey())
            ->with([
                'user' => static function ($builder): void {
                    /** @var Builder<\App\Models\User> $builder */
                    $builder->select(['id', 'name', 'email', 'role', 'status', 'unit_id']);
                },
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $events = $this->extractStringList($request, 'event');

        if ($events !== []) {
            $query->whereIn('event', $events);
        }

        $auditableTypes = $this->extractStringList($request, 'auditable_type');

        if ($auditableTypes !== []) {
            $query->whereIn('auditable_type', $auditableTypes);
        }

        $auditableId = $request->query('auditable_id');

        if ($auditableId !== null) {
            if (! is_numeric($auditableId)) {
                throw ValidationException::withMessages([
                    'auditable_id' => 'auditable_id değeri sayısal olmalıdır.',
                ]);
            }

            $query->where('auditable_id', (int) $auditableId);
        }

        $hasUser = $this->extractBoolean($request, 'has_user');

        if ($hasUser !== null) {
            if ($hasUser) {
                $query->whereNotNull('user_id');
            } else {
                $query->whereNull('user_id');
            }
        }

        $since = $request->query('since');

        if ($since !== null) {
            $query->where('created_at', '>=', $this->parseDateFilter($since, 'since'));
        }

        $until = $request->query('until');

        if ($until !== null) {
            $query->where('created_at', '<=', $this->parseDateFilter($until, 'until'));
        }

        $perPage = $this->extractPerPage($request);

        return AuditLogResource::collection(
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
                $field => \sprintf('%s değeri ISO-8601 tarih biçiminde olmalıdır.', $field),
            ]);
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                $field => \sprintf('%s değeri ISO-8601 tarih biçiminde olmalıdır.', $field),
            ]);
        }
    }
}

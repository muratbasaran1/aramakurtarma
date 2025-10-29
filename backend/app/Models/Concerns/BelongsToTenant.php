<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use function is_numeric;

/**
 * @mixin Model
 * @method static Builder<static> forTenantQuery(Tenant|int|string $tenant)
 * @method static Builder|static forTenant(Tenant|int|string $tenant)
 * @method Builder forTenant(Tenant|int|string $tenant)
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);
            $tenantId = $context->tenantId();

            if ($tenantId === null) {
                return;
            }

            $builder->where(
                \sprintf('%s.tenant_id', $builder->getModel()->getTable()),
                $tenantId
            );
        });

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);
            $tenantId = $context->tenantId();

            if ($tenantId !== null && $model->getAttribute('tenant_id') === null) {
                $model->setAttribute('tenant_id', $tenantId);
            }
        });
    }

    public static function forTenantQuery(Tenant|int|string $tenant): Builder
    {
        $builder = static::query()->withoutGlobalScope('tenant');

        return static::applyTenantConstraint($builder, $tenant);
    }

    public function scopeForTenant(Builder $builder, Tenant|int|string $tenant): Builder
    {
        $scopedBuilder = $builder->withoutGlobalScope('tenant');

        return static::applyTenantConstraint($scopedBuilder, $tenant);
    }

    protected static function applyTenantConstraint(Builder $builder, Tenant|int|string $tenant): Builder
    {
        $tenantId = static::resolveTenantId($tenant);

        if ($tenantId === null) {
            return $builder->whereKey(-1);
        }

        return $builder->where(
            \sprintf('%s.tenant_id', $builder->getModel()->getTable()),
            $tenantId
        );
    }

    protected static function resolveTenantId(Tenant|int|string $tenant): ?int
    {
        return match (true) {
            $tenant instanceof Tenant => (int) $tenant->getKey(),
            is_numeric($tenant) => (int) $tenant,
            default => Tenant::query()
                ->where('slug', $tenant)
                ->value('id'),
        };
    }
}

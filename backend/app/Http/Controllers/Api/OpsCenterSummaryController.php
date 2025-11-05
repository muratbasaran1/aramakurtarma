<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\OpsCenter\OpsCenterSummary;
use App\Tenant\TenantContext;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class OpsCenterSummaryController extends Controller
{
    public function __construct(private readonly TenantContext $tenantContext)
    {
    }

    public function show(string $tenant): JsonResponse
    {
        $contextTenant = $this->tenant();

        if ($contextTenant->slug !== $tenant) {
            throw new RuntimeException('İstek bağlamı ile rota tenant bilgisi uyuşmuyor.');
        }

        $summary = OpsCenterSummary::forTenant($contextTenant);

        return response()->json($summary->toApiArray($contextTenant));
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

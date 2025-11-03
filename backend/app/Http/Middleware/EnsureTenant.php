<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Tenant\TenantContext;
use App\Tenant\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnsureTenant
{
    public function __construct(
        private readonly TenantResolver $tenantResolver,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $tenant = $this->tenantResolver->resolve($request);

        if ($tenant === null) {
            /** @var SymfonyResponse $response */
            $response = new JsonResponse([
                'message' => 'Tenant bilgisi bulunamadı. `X-Tenant` başlığı veya rota parametresi ile tenant belirtin.',
            ], SymfonyResponse::HTTP_BAD_REQUEST);

            return $response;
        }

        $this->tenantContext->setTenant($tenant);

        try {
            /** @var SymfonyResponse $response */
            $response = $next($request);
        } finally {
            $this->tenantContext->clear();
        }

        return $response;
    }
}

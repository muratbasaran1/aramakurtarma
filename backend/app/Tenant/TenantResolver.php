<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantResolver
{
    public function resolve(Request $request): ?Tenant
    {
        $routeTenant = $request->route('tenant');

        if ($routeTenant instanceof Tenant) {
            return $routeTenant;
        }

        if (\is_string($routeTenant) && $routeTenant !== '') {
            $resolved = $this->findByIdentifier($routeTenant);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        $header = $request->header('X-Tenant');

        if (\is_string($header) && $header !== '') {
            $resolved = $this->findByIdentifier($header);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        $query = $request->query('tenant');

        if (\is_string($query) && $query !== '') {
            $resolved = $this->findByIdentifier($query);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    private function findByIdentifier(string $identifier): ?Tenant
    {
        if (ctype_digit($identifier)) {
            /** @var Tenant|null $tenant */
            $tenant = Tenant::query()->find((int) $identifier);

            return $tenant;
        }

        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->where('slug', $identifier)
            ->first();

        return $tenant;
    }
}

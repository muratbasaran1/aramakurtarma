<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\OpsCenter\OpsCenterSummary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class OpsCenterController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $tenants = Tenant::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        if ($tenants->isEmpty()) {
            return view('opscenter', [
                'tenants' => $tenants,
                'tenant' => null,
                'summary' => [
                    'incidentCounts' => [],
                    'taskCounts' => [],
                    'inventoryCounts' => [],
                    'recentIncidents' => collect(),
                    'recentTasks' => collect(),
                    'units' => collect(),
                ],
            ]);
        }

        $requestedSlug = (string) $request->query('tenant', '');
        $activeSlug = $requestedSlug !== '' ? $requestedSlug : (string) $tenants->first()->slug;

        $tenant = Tenant::query()
            ->where('slug', $activeSlug)
            ->first();

        if (! $tenant instanceof Tenant) {
            return Redirect::route('opscenter', ['tenant' => $tenants->first()->slug]);
        }

        $summary = OpsCenterSummary::forTenant($tenant)->toViewData();

        return view('opscenter', [
            'tenants' => $tenants,
            'tenant' => $tenant,
            'summary' => $summary,
        ]);
    }
}

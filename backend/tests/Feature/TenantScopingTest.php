<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Tenant;
use App\Tenant\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_queries_by_tenant_context(): void
    {
        $tenantA = Tenant::factory()->create(['slug' => 'istanbul']);
        $tenantB = Tenant::factory()->create(['slug' => 'ankara']);

        Incident::factory()->count(2)->for($tenantA)->create();
        Incident::factory()->count(1)->for($tenantB)->create();

        $context = app(TenantContext::class);

        $context->setTenant($tenantA);
        $this->assertSame(2, Incident::count());

        $context->setTenant($tenantB);
        $this->assertSame(1, Incident::count());

        $context->clear();
        $this->assertSame(3, Incident::withoutGlobalScope('tenant')->count());
    }

    public function test_it_sets_tenant_id_during_creation_when_context_present(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);
        $context = app(TenantContext::class);
        $context->setTenant($tenant);

        $incident = Incident::create([
            'code' => 'INC-001',
            'title' => 'Deprem Tatbikatı',
        ]);

        $this->assertSame($tenant->id, $incident->tenant_id);
        $this->assertSame('izmir', $incident->tenant->slug);

        $context->clear();
    }
}

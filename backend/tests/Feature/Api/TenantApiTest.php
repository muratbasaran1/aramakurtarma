<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TenantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tenants_with_counts(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'ankara',
            'name' => 'Ankara AFAD',
        ]);

        $unit = Unit::factory()->for($tenant)->create();
        $activeUser = User::factory()->for($tenant)->for($unit, 'unit')->create([
            'status' => 'active',
        ]);
        User::factory()->for($tenant)->for($unit, 'unit')->create([
            'status' => 'suspended',
        ]);

        $openIncident = Incident::factory()->for($tenant)->create([
            'status' => 'open',
        ]);
        $closedIncident = Incident::factory()->for($tenant)->create([
            'status' => 'closed',
        ]);

        Task::factory()->for($tenant)->for($openIncident, 'incident')->for($unit, 'assignedUnit')->for($activeUser, 'assignee')->create([
            'status' => 'assigned',
        ]);
        Task::factory()->for($tenant)->for($openIncident, 'incident')->for($unit, 'assignedUnit')->for($activeUser, 'assignee')->create([
            'status' => 'in_progress',
        ]);
        Task::factory()->for($tenant)->for($closedIncident, 'incident')->for($unit, 'assignedUnit')->for($activeUser, 'assignee')->create([
            'status' => 'verified',
            'completed_at' => now()->subHour(),
            'verified_at' => now()->subMinutes(30),
        ]);

        Inventory::factory()->for($tenant)->create([
            'status' => 'active',
        ]);
        Inventory::factory()->for($tenant)->create([
            'status' => 'service',
        ]);

        Tenant::factory()->create();

        $response = $this->getJson('/api/tenants');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('data', 2)
                    ->hasAll(['links', 'meta'])
            );

        $tenantEntry = collect($response->json('data'))
            ->firstWhere('slug', 'ankara');

        $this->assertNotNull($tenantEntry, 'Tenant listesinde ankara bulunamadı.');

        $this->assertSame(2, $tenantEntry['counts']['incidents']['total']);
        $this->assertSame(1, $tenantEntry['counts']['incidents']['open']);
        $this->assertSame(1, $tenantEntry['counts']['incidents']['closed']);
        $this->assertSame(3, $tenantEntry['counts']['tasks']['total']);
        $this->assertSame(2, $tenantEntry['counts']['tasks']['active']);
        $this->assertSame(1, $tenantEntry['counts']['tasks']['verified']);
        $this->assertSame(1, $tenantEntry['counts']['units']['total']);
        $this->assertSame(2, $tenantEntry['counts']['users']['total']);
        $this->assertSame(1, $tenantEntry['counts']['users']['active']);
        $this->assertSame(2, $tenantEntry['counts']['inventories']['total']);
        $this->assertSame(1, $tenantEntry['counts']['inventories']['active']);
        $this->assertSame(1, $tenantEntry['counts']['inventories']['service']);
    }

    public function test_it_filters_tenants_by_search(): void
    {
        Tenant::factory()->create([
            'slug' => 'istanbul',
            'name' => 'İstanbul',
        ]);
        Tenant::factory()->create([
            'slug' => 'ankara',
            'name' => 'Ankara',
        ]);

        $response = $this->getJson('/api/tenants?search=anka');

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('data', 1)
                    ->where('data.0.slug', 'ankara')
                    ->hasAll(['links', 'meta'])
            );

        $response = $this->getJson('/api/tenants?slug=istanbul');
        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->has('data', 1)
                    ->where('data.0.slug', 'istanbul')
                    ->hasAll(['links', 'meta'])
            );
    }

    public function test_it_returns_tenant_detail_with_summary(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'ankara',
        ]);

        $unit = Unit::factory()->for($tenant)->create();
        $incident = Incident::factory()->for($tenant)->create([
            'status' => 'active',
        ]);
        Task::factory()->for($tenant)->for($incident, 'incident')->for($unit, 'assignedUnit')->create([
            'status' => 'assigned',
        ]);
        Inventory::factory()->for($tenant)->create([
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/tenants/ankara');

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json): AssertableJson => $json
                    ->where('data.slug', 'ankara')
                    ->has('data.counts')
                    ->has('summary')
                    ->where('summary.tenant.slug', 'ankara')
                    ->has('summary.counts.incidents')
            );

        $summary = $response->json('summary');
        $this->assertNotNull($summary);
        $this->assertSame('ankara', $summary['tenant']['slug']);
        $this->assertArrayHasKey('incidents', $summary['counts']);
        $this->assertArrayHasKey('tasks', $summary['counts']);
        $this->assertArrayHasKey('inventory', $summary['counts']);
    }

    public function test_it_allows_disabling_summary(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'ankara',
        ]);

        $response = $this->getJson('/api/tenants/ankara?include_summary=0');

        $response
            ->assertOk()
            ->assertJsonMissingPath('summary');
    }
}

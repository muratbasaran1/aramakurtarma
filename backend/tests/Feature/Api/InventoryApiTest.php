<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Inventory;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_inventory_items_for_the_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'van']);
        $otherTenant = Tenant::factory()->create(['slug' => 'kars']);

        Inventory::factory()->count(2)->for($tenant)->create([
            'status' => 'active',
        ]);

        Inventory::factory()->for($otherTenant)->create([
            'status' => 'retired',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/inventories');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_it_filters_inventory_by_status(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mardin']);

        Inventory::factory()->for($tenant)->create([
            'code' => 'EQP-1',
            'status' => 'service',
        ]);

        Inventory::factory()->for($tenant)->create([
            'code' => 'EQP-2',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/inventories?status=service');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'EQP-1');
    }

    public function test_it_returns_inventory_details_when_scoped_to_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'erzincan']);
        $inventory = Inventory::factory()->for($tenant)->create([
            'code' => 'KNT-42',
            'status' => 'service',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/inventories/'.$inventory->getKey());

        $response->assertOk()
            ->assertJsonPath('data.code', 'KNT-42')
            ->assertJsonPath('data.status', 'service');
    }

    public function test_it_hides_inventory_from_other_tenants(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bartin']);
        $otherTenant = Tenant::factory()->create(['slug' => 'ardahan']);

        $inventory = Inventory::factory()->for($otherTenant)->create();

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/inventories/'.$inventory->getKey());

        $response->assertNotFound();
    }

    public function test_it_creates_inventory_item_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mus']);

        $payload = [
            'code' => 'EQP-44',
            'name' => 'Kesici Hidrolik Seti',
            'status' => 'service',
            'last_service_at' => Carbon::now()->subDays(3)->toIso8601String(),
            'attributes' => [
                'category' => 'equipment',
                'serial' => 'ABC-42',
            ],
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/inventories', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.code', 'EQP-44')
            ->assertJsonPath('data.status', 'service')
            ->assertJsonPath('data.attributes.serial', 'ABC-42');

        $this->assertDatabaseHas('inventories', [
            'tenant_id' => $tenant->getKey(),
            'code' => 'EQP-44',
        ]);
    }

    public function test_it_defaults_status_to_active_when_omitted(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bitlis']);

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/inventories', [
            'code' => 'KIT-07',
            'name' => 'İlk Yardım Çantası',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'active');
    }

    public function test_it_prevents_duplicate_inventory_code_within_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'nigde']);
        Inventory::factory()->for($tenant)->create([
            'code' => 'STO-12',
        ]);

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/inventories', [
            'code' => 'STO-12',
            'name' => 'Dalgıç Pompası',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['code']);
    }

    public function test_it_updates_inventory_item(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'rize']);
        $inventory = Inventory::factory()->for($tenant)->create([
            'status' => 'active',
        ]);

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/inventories/'.$inventory->getKey(), [
            'status' => 'retired',
            'last_service_at' => Carbon::now()->subYear()->toIso8601String(),
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'retired');

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->getKey(),
            'status' => 'retired',
        ]);
    }

    public function test_it_blocks_updates_for_inventory_from_another_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'usak']);
        $otherTenant = Tenant::factory()->create(['slug' => 'ordu']);

        $inventory = Inventory::factory()->for($otherTenant)->create();

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/inventories/'.$inventory->getKey(), [
            'status' => 'service',
        ]);

        $response->assertNotFound();
    }

    public function test_it_rejects_future_service_dates(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'siirt']);
        $inventory = Inventory::factory()->for($tenant)->create();

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/inventories/'.$inventory->getKey(), [
            'last_service_at' => Carbon::now()->addDay()->toIso8601String(),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['last_service_at']);
    }
}

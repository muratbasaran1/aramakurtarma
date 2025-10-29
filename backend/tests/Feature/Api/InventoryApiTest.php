<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Inventory;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

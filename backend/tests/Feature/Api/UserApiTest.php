<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_users_for_the_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'balikesir']);
        $otherTenant = Tenant::factory()->create(['slug' => 'tekirdag']);

        User::factory()->count(3)->for($tenant)->create();
        User::factory()->for($otherTenant)->create();

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_it_filters_users_by_status_and_role(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'edirne']);

        User::factory()->for($tenant)->create([
            'name' => 'Aktif Kullanıcı',
            'status' => 'active',
            'role' => 'operator',
        ]);

        User::factory()->for($tenant)->create([
            'name' => 'Pasif Kullanıcı',
            'status' => 'inactive',
            'role' => 'viewer',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users?status=active&role=operator');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.role', 'operator');
    }

    public function test_it_filters_users_by_unit_and_search(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'canakkale']);
        $unit = Unit::factory()->for($tenant)->create(['name' => 'Saha Ekibi']);
        $otherUnit = Unit::factory()->for($tenant)->create(['name' => 'Lojistik']);

        User::factory()->for($tenant)->create([
            'unit_id' => $unit->id,
            'name' => 'Saha Lideri',
            'email' => 'lider@example.org',
        ]);

        User::factory()->for($tenant)->create([
            'unit_id' => $otherUnit->id,
            'name' => 'Lojistik Üyesi',
            'email' => 'lojistik@example.org',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/users?unit_id='.$unit->id.'&search=lider');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'lider@example.org');
    }
}

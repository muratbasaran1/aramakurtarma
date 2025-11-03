<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpsCenterDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_handles_empty_state(): void
    {
        $response = $this->get(route('opscenter'));

        $response->assertOk();
        $response->assertSee('Kayıtlı tenant bulunamadı', false);
    }

    public function test_dashboard_renders_with_summary_information(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'ankara-afad',
            'name' => 'Ankara AFAD',
        ]);

        $unit = Unit::factory()->for($tenant)->create([
            'name' => 'Arama Ekibi',
            'type' => 'field',
        ]);

        User::factory()->for($tenant)->for($unit, 'unit')->create();

        $incident = Incident::factory()->for($tenant)->create([
            'status' => 'active',
            'priority' => 'high',
            'started_at' => now()->subHour(),
        ]);

        Task::factory()
            ->for($tenant)
            ->for($incident)
            ->for($unit, 'assignedUnit')
            ->state([
                'status' => 'assigned',
                'planned_start_at' => now()->addHour(),
            ])
            ->create();

        Inventory::factory()->for($tenant)->create([
            'status' => 'active',
        ]);

        $response = $this->get(route('opscenter', ['tenant' => $tenant->slug]));

        $response->assertOk();
        $response->assertSee('OpsCenter Paneli', false);
        $response->assertSee($tenant->name, false);
        $response->assertSee($incident->code, false);
        $response->assertSee('Arama Ekibi', false);
    }

    public function test_dashboard_redirects_to_first_tenant_when_unknown_slug_requested(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'izmir',
            'name' => 'İzmir',
        ]);

        $response = $this->get('/opscenter?tenant=unknown');

        $response->assertRedirect(route('opscenter', ['tenant' => $tenant->slug]));
    }
}

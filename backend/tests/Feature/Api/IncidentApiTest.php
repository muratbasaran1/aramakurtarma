<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Incident;
use App\Models\Task;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_incidents_for_the_given_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'istanbul']);
        $otherTenant = Tenant::factory()->create(['slug' => 'ankara']);

        Incident::factory()->count(2)->state(fn (array $attributes): array => [
            'status' => 'active',
        ])->for($tenant)->create();

        Incident::factory()->for($otherTenant)->create([
            'status' => 'closed',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/incidents');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_it_filters_incidents_by_status_and_priority(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);

        Incident::factory()->for($tenant)->create([
            'code' => 'INC-001',
            'status' => 'active',
            'priority' => 'high',
        ]);

        Incident::factory()->for($tenant)->create([
            'code' => 'INC-002',
            'status' => 'closed',
            'priority' => 'medium',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/incidents?status=closed&priority=medium');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'INC-002');
    }

    public function test_it_returns_incident_details_with_tasks(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'bursa']);

        $incident = Incident::factory()->for($tenant)->create([
            'code' => 'INC-100',
        ]);

        Task::factory()->count(2)->for($tenant)->for($incident)->create([
            'status' => 'assigned',
        ]);

        $response = $this->getJson('/api/tenants/'.$tenant->slug.'/incidents/'.$incident->id);

        $response->assertOk()
            ->assertJsonPath('data.code', 'INC-100')
            ->assertJsonCount(2, 'data.tasks');
    }

    public function test_it_returns_400_when_tenant_not_found(): void
    {
        $response = $this->getJson('/api/tenants/bilinmeyen/incidents');

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Tenant bilgisi bulunamadı. `X-Tenant` başlığı veya rota parametresi ile tenant belirtin.');
    }

    public function test_it_creates_incident_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'izmir']);

        $payload = [
            'code' => 'INC-900',
            'title' => 'Deprem acil durum',
            'status' => 'active',
            'priority' => 'high',
            'started_at' => now()->toISOString(),
            'impact_area' => [
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [29.0, 41.0],
                        [29.2, 41.0],
                        [29.2, 41.2],
                        [29.0, 41.2],
                        [29.0, 41.0],
                    ],
                ],
            ],
            'context' => [
                'description' => 'İlk saha raporu',
            ],
        ];

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/incidents', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.code', 'INC-900')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.impact_area.type', 'Polygon')
            ->assertJsonMissingPath('data.tasks');

        $this->assertDatabaseHas('incidents', [
            'tenant_id' => $tenant->id,
            'code' => 'INC-900',
            'status' => 'active',
        ]);
    }

    public function test_it_enforces_unique_incident_codes_per_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'hatay']);

        Incident::factory()->for($tenant)->create([
            'code' => 'INC-1234',
        ]);

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/incidents', [
            'code' => 'INC-1234',
            'title' => 'Çakışan kayıt',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_it_requires_closed_at_for_closed_status(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'antalya']);

        $response = $this->postJson('/api/tenants/'.$tenant->slug.'/incidents', [
            'code' => 'INC-8888',
            'title' => 'Kapatılan olay',
            'status' => 'closed',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['closed_at']);
    }

    public function test_it_updates_incident_for_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'ordu']);
        $incident = Incident::factory()->for($tenant)->create([
            'code' => 'INC-2000',
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $payload = [
            'title' => 'Güncellenen başlık',
            'status' => 'active',
            'priority' => 'high',
            'context' => [
                'description' => 'Saha ekibi yönlendirildi.',
            ],
        ];

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/incidents/'.$incident->getKey(), $payload);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Güncellenen başlık')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.context.description', 'Saha ekibi yönlendirildi.');

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->getKey(),
            'tenant_id' => $tenant->getKey(),
            'status' => 'active',
            'priority' => 'high',
        ]);
    }

    public function test_it_validates_unique_code_during_update(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'mugla']);

        $incident = Incident::factory()->for($tenant)->create([
            'code' => 'INC-UNI-1',
        ]);

        Incident::factory()->for($tenant)->create([
            'code' => 'INC-UNI-2',
        ]);

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/incidents/'.$incident->getKey(), [
            'code' => 'INC-UNI-2',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_it_forbids_updating_incident_of_another_tenant(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'van']);
        $otherTenant = Tenant::factory()->create(['slug' => 'sirnak']);

        $incident = Incident::factory()->for($otherTenant)->create([
            'code' => 'INC-REMOTE',
        ]);

        $response = $this->patchJson('/api/tenants/'.$tenant->slug.'/incidents/'.$incident->getKey(), [
            'status' => 'active',
        ]);

        $response->assertNotFound();

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->getKey(),
            'tenant_id' => $otherTenant->getKey(),
            'status' => $incident->status,
        ]);
    }
}

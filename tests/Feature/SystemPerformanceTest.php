<?php

namespace Tests\Feature;

use App\Models\PerformanceMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Super Admin']);
    }

    protected function createSuperAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        return $admin;
    }

    public function test_guest_is_redirected_from_performance_console(): void
    {
        $response = $this->get('/system-performance');
        $response->assertRedirect('/login');
    }

    public function test_super_admin_can_access_performance_console(): void
    {
        $admin = $this->createSuperAdmin();
        $response = $this->actingAs($admin)->get('/system-performance');

        $response->assertStatus(200);
        $response->assertSee(__('messages.perf_live_telemetry'));
        $response->assertSee('id="chartLatencyTrend"', false);
        $response->assertSee('id="chartStatusCodes"', false);
        $response->assertSee('id="itam-hud-pill"', false);
        $response->assertSee('id="itam-hud-drawer"', false);

        // Check injected headers
        $this->assertTrue($response->headers->has('X-ITAM-Response-Time'));
        $this->assertTrue($response->headers->has('X-ITAM-Query-Count'));
        $this->assertTrue($response->headers->has('X-ITAM-Memory-Peak'));
    }

    public function test_super_admin_can_fetch_api_stats(): void
    {
        $admin = $this->createSuperAdmin();
        $response = $this->actingAs($admin)->getJson('/system-performance/api-stats?range=24h');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalRequests',
            'avgDuration',
            'avgQueryTime',
            'avgQueryCount',
            'peakMemory',
            'slowRequestsCount',
            'slowQueriesCount',
            'reliabilityScore',
            'serverEnv',
            'selectedRange',
        ]);
    }

    public function test_telemetry_is_recorded_and_can_be_cleared(): void
    {
        $admin = $this->createSuperAdmin();

        // Perform requests to generate metrics
        $this->actingAs($admin)->get('/system-performance');

        // Test clear logs endpoint
        $response = $this->actingAs($admin)->deleteJson('/system-performance/clear');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, PerformanceMetric::count());
    }

    public function test_mini_hud_is_rendered_for_super_admin_on_other_pages(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get('/roles');
        $response->assertStatus(200);
        $response->assertSee('id="itam-hud-pill"', false);
        $response->assertSee('id="itam-hud-drawer"', false);
        $response->assertSee('id="itam-telemetry-data"', false);
    }
}


<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\TraccarRequestLog;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraccarDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_traccar_dashboard_displays_device_status(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $driver = User::factory()->create([
            'name' => 'Driver Traccar',
            'role' => 'driver',
            'device_id' => 'gerobak-kopi-01',
        ]);

        $unit = Unit::query()->create([
            'name' => 'Gerobak Kopi 01',
            'code' => 'GRBK-01',
            'status' => 'ready',
        ]);

        \App\Models\DriverUnitAssignment::query()->create([
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'assigned_at' => now(),
            'status' => 'active',
        ]);

        Location::query()->create([
            'device_id' => 'gerobak-kopi-01',
            'latitude' => -0.4853219,
            'longitude' => 117.1485458,
            'battery_level' => 88,
            'event_type' => 'heartbeat',
            'recorded_at' => now(),
        ]);

        TraccarRequestLog::query()->create([
            'method' => 'POST',
            'path' => 'api/location',
            'content_type' => 'application/json',
            'raw_body' => '{"location":{"coords":{"latitude":-0.4853219,"longitude":117.1485458}}}',
            'processed' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.traccar'));

        $response->assertOk()
            ->assertSee('Monitoring Traccar')
            ->assertSee('Gerobak Kopi 01')
            ->assertSee('88%')
            ->assertSee('Request Mentah Traccar');
    }
}

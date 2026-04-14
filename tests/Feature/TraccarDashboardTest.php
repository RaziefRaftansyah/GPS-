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

    public function test_owner_can_create_traccar_monitoring_location(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $response = $this->actingAs($owner)->post(route('dashboard.traccar.locations.store'), [
            'device_id' => 'manual-device-01',
            'latitude' => '-5.1476650',
            'longitude' => '119.4327320',
            'accuracy' => '12.50',
            'speed' => '3.25',
            'heading' => '180',
            'altitude' => '15.40',
            'battery_level' => '92',
            'is_charging' => '1',
            'is_moving' => '0',
            'activity' => 'idle',
            'event_type' => 'manual-check',
            'recorded_at' => '2026-04-14 08:15:00',
        ]);

        $response->assertRedirect(route('dashboard.traccar'));
        $response->assertSessionHas('dashboard_status', 'Data monitoring Traccar berhasil ditambahkan.');

        $location = Location::query()->latest('id')->first();

        $this->assertNotNull($location);
        $this->assertSame('manual-device-01', $location->device_id);
        $this->assertSame('manual-check', $location->event_type);
        $this->assertSame(92.0, $location->battery_level);
        $this->assertTrue($location->is_charging);
        $this->assertFalse($location->is_moving);
    }

    public function test_owner_can_update_traccar_monitoring_location(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $location = Location::query()->create([
            'device_id' => 'device-awal',
            'latitude' => -5.1,
            'longitude' => 119.4,
            'recorded_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($owner)->patch(route('dashboard.traccar.locations.update', $location), [
            'device_id' => 'device-baru',
            'latitude' => '-5.2000000',
            'longitude' => '119.5000000',
            'accuracy' => '8.00',
            'speed' => '4.50',
            'heading' => '270',
            'altitude' => '20.25',
            'battery_level' => '75',
            'is_charging' => '0',
            'is_moving' => '1',
            'activity' => 'driving',
            'event_type' => 'heartbeat',
            'recorded_at' => '2026-04-14 10:30:00',
        ]);

        $response->assertRedirect(route('dashboard.traccar'));
        $response->assertSessionHas('dashboard_status', 'Data monitoring Traccar berhasil diperbarui.');

        $location->refresh();

        $this->assertSame('device-baru', $location->device_id);
        $this->assertSame(-5.2, $location->latitude);
        $this->assertSame(119.5, $location->longitude);
        $this->assertSame('heartbeat', $location->event_type);
        $this->assertFalse($location->is_charging);
        $this->assertTrue($location->is_moving);
    }

    public function test_owner_can_delete_traccar_monitoring_location(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $location = Location::query()->create([
            'device_id' => 'device-hapus',
            'latitude' => -5.1,
            'longitude' => 119.4,
            'recorded_at' => now(),
        ]);

        $response = $this->actingAs($owner)->delete(route('dashboard.traccar.locations.destroy', $location));

        $response->assertRedirect(route('dashboard.traccar'));
        $response->assertSessionHas('dashboard_status', 'Data monitoring Traccar berhasil dihapus.');
        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }

    public function test_non_owner_is_redirected_from_traccar_dashboard_to_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'viewer',
            'email' => 'viewer@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.traccar'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('profile_notice', 'Halaman Monitoring Traccar hanya bisa diakses oleh owner.');
    }
}

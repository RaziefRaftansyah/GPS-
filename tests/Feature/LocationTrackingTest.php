<?php

namespace Tests\Feature;

use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracker_page_is_accessible(): void
    {
        $this->get(route('tracker.index'))
            ->assertOk()
            ->assertSee('Kopling terdekat langsung terlihat.');
    }

    public function test_api_location_stores_latitude_and_longitude(): void
    {
        $this->postJson(route('api.location.store'), [
            'latitude' => -5.147665,
            'longitude' => 119.432732,
        ])->assertCreated()
            ->assertJsonPath('location.latitude', -5.147665)
            ->assertJsonPath('location.longitude', 119.432732);

        $this->assertDatabaseHas('locations', [
            'latitude' => -5.1476650,
            'longitude' => 119.4327320,
        ]);
    }

    public function test_api_location_requires_valid_coordinates(): void
    {
        $this->postJson(route('api.location.store'), [
            'latitude' => 200,
            'longitude' => 500,
        ])->assertUnprocessable();
    }

    public function test_api_location_invalid_payload_does_not_redirect(): void
    {
        $this->post(route('api.location.store'), [
            'id' => 'gerobak-kopi-01',
        ])->assertStatus(422)
            ->assertHeader('content-type', 'application/json');
    }

    public function test_api_location_accepts_traccar_json_format(): void
    {
        $this->postJson(route('api.location.store'), [
            'device_id' => 'gerobak-kopi-01',
            'location' => [
                'timestamp' => '2026-04-08T05:00:00.000Z',
                'coords' => [
                    'latitude' => -5.147665,
                    'longitude' => 119.432732,
                    'accuracy' => 8.5,
                    'speed' => 2.1,
                    'heading' => 180,
                    'altitude' => 12,
                ],
                'is_moving' => true,
                'event' => 'motionchange',
                'battery' => [
                    'level' => 0.85,
                    'is_charging' => false,
                ],
                'activity' => [
                    'type' => 'in_vehicle',
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('location.device_id', 'gerobak-kopi-01')
            ->assertJsonPath('location.battery_level', 85);

        $this->assertDatabaseHas('locations', [
            'device_id' => 'gerobak-kopi-01',
            'event_type' => 'motionchange',
            'activity' => 'in_vehicle',
        ]);
    }

    public function test_api_location_accepts_traccar_negative_speed_and_heading_as_unknown(): void
    {
        $this->postJson(route('api.location.store'), [
            'device_id' => '39667554',
            'location' => [
                'timestamp' => '2026-04-08T05:55:28.526Z',
                'coords' => [
                    'latitude' => -0.48522,
                    'longitude' => 117.1486976,
                    'accuracy' => 100,
                    'speed' => -1,
                    'heading' => -1,
                    'altitude' => 69.1,
                ],
                'is_moving' => false,
                'event' => 'providerchange',
                'battery' => [
                    'level' => 0.4,
                    'is_charging' => false,
                ],
                'activity' => [
                    'type' => 'still',
                ],
            ],
        ])->assertCreated()
            ->assertJsonPath('location.device_id', '39667554')
            ->assertJsonPath('location.speed', null)
            ->assertJsonPath('location.heading', null);
    }

    public function test_api_location_latest_returns_recent_locations(): void
    {
        $driver = User::factory()->create([
            'name' => 'Driver Atlas',
            'role' => 'driver',
            'device_id' => 'gerobak-kopi-01',
        ]);

        $unit = Unit::query()->create([
            'name' => 'Gerobak Atlas',
            'code' => 'GRBK-ATLAS',
            'status' => 'ready',
        ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'assigned_at' => now(),
            'checked_in_at' => now(),
            'status' => 'active',
        ]);

        Location::query()->create([
            'device_id' => 'gerobak-kopi-01',
            'latitude' => -5.147665,
            'longitude' => 119.432732,
            'recorded_at' => now(),
        ]);

        $this->getJson(route('api.location.latest'))
            ->assertOk()
            ->assertJsonPath('latest.device_id', 'gerobak-kopi-01')
            ->assertJsonPath('latest.unit_name', 'Gerobak Atlas')
            ->assertJsonPath('latest.driver_name', 'Driver Atlas')
            ->assertJsonPath('active_unit_count', 1)
            ->assertJsonCount(1, 'active_units')
            ->assertJsonCount(1, 'locations');
    }

    public function test_api_location_latest_returns_multiple_active_unit_markers(): void
    {
        $driverOne = User::factory()->create([
            'name' => 'Driver A',
            'role' => 'driver',
            'device_id' => 'driver-a',
        ]);

        $driverTwo = User::factory()->create([
            'name' => 'Driver B',
            'role' => 'driver',
            'device_id' => 'driver-b',
        ]);

        $unitOne = Unit::query()->create([
            'name' => 'Gerobak A',
            'code' => 'GA-1',
            'status' => 'ready',
        ]);

        $unitTwo = Unit::query()->create([
            'name' => 'Gerobak B',
            'code' => 'GB-1',
            'status' => 'ready',
        ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driverOne->id,
            'unit_id' => $unitOne->id,
            'assigned_at' => now(),
            'checked_in_at' => now(),
            'status' => 'active',
        ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driverTwo->id,
            'unit_id' => $unitTwo->id,
            'assigned_at' => now(),
            'checked_in_at' => now(),
            'status' => 'active',
        ]);

        Location::query()->create([
            'device_id' => 'driver-a',
            'latitude' => -5.1,
            'longitude' => 119.4,
            'recorded_at' => now()->subMinute(),
        ]);

        Location::query()->create([
            'device_id' => 'driver-b',
            'latitude' => -5.2,
            'longitude' => 119.5,
            'recorded_at' => now(),
        ]);

        $this->getJson(route('api.location.latest'))
            ->assertOk()
            ->assertJsonPath('active_unit_count', 2)
            ->assertJsonCount(2, 'active_units');
    }

    public function test_browser_test_endpoint_returns_urls(): void
    {
        $this->getJson(route('api.location.test'))
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('your_ip', '172.16.239.62');
    }

    public function test_browser_test_send_can_store_location_with_query_parameters(): void
    {
        $this->getJson(route('api.location.test-send', [
            'id' => 'gerobak-kopi-01',
            'lat' => -5.147665,
            'lon' => 119.432732,
            'batt' => 88,
        ]))->assertCreated()
            ->assertJsonPath('location.device_id', 'gerobak-kopi-01');
    }
}

<?php

namespace Tests\Feature;

use App\Models\DriverUnitAssignment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_active_users(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Kopi Keliling',
            'email' => 'admin@kopikeliling.com',
            'role' => 'owner',
        ]);

        $customer = User::factory()->create([
            'name' => 'Aulia Coffee',
            'email' => 'aulia@example.com',
        ]);

        DB::table('sessions')->insert([
            [
                'id' => 'admin-session',
                'user_id' => $admin->id,
                'ip_address' => '172.16.239.62',
                'user_agent' => 'Safari on macOS',
                'payload' => 'payload',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'customer-session',
                'user_id' => $customer->id,
                'ip_address' => '172.16.239.132',
                'user_agent' => 'Chrome on Android',
                'payload' => 'payload',
                'last_activity' => now()->subMinute()->timestamp,
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Pantau user yang sedang login')
            ->assertSee('Aulia Coffee')
            ->assertSee('2 user online');
    }

    public function test_admin_can_kick_active_user(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@kopikeliling.com',
            'role' => 'owner',
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer Kick',
        ]);

        DB::table('sessions')->insert([
            'id' => 'customer-session',
            'user_id' => $customer->id,
            'ip_address' => '172.16.239.132',
            'user_agent' => 'Chrome on Android',
            'payload' => 'payload',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->actingAs($admin)->post(route('dashboard.users.kick', $customer));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('sessions', [
            'id' => 'customer-session',
        ]);
    }

    public function test_non_admin_user_is_redirected_to_profile_page(): void
    {
        $user = User::factory()->create([
            'email' => 'pelanggan@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_owner_can_assign_driver_to_unit(): void
    {
        $owner = User::factory()->create([
            'email' => 'admin@kopikeliling.com',
            'role' => 'owner',
        ]);

        $driver = User::factory()->create([
            'role' => 'driver',
            'device_id' => 'hp-driver-sunset',
        ]);

        $unit = Unit::query()->create([
            'name' => 'Gerobak Sunset',
            'code' => 'GRBK-01',
            'status' => 'ready',
        ]);

        $this->actingAs($owner)
            ->post(route('dashboard.assignments.store'), [
                'driver_id' => $driver->id,
                'unit_id' => $unit->id,
                'notes' => 'Shift sore',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('driver_unit_assignments', [
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'status' => 'active',
        ]);
    }

    public function test_driver_sees_driver_dashboard(): void
    {
        $driver = User::factory()->create([
            'name' => 'Driver Senja',
            'role' => 'driver',
            'device_id' => 'hp-driver-senja',
        ]);

        $unit = Unit::query()->create([
            'name' => 'Gerobak Senja',
            'code' => 'GRBK-09',
            'status' => 'ready',
        ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'assigned_at' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($driver)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Driver')
            ->assertSee('Gerobak Senja');
    }
}

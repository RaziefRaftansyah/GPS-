<?php

namespace Tests\Feature;

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
}

<?php

namespace Tests\Feature;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_user_profile_and_purchase_history(): void
    {
        $user = User::factory()->create([
            'name' => 'Aulia Coffee',
            'email' => 'aulia@example.com',
        ]);

        Purchase::query()->create([
            'user_id' => $user->id,
            'menu_name' => 'Brown Sugar Latte',
            'quantity' => 2,
            'total_price' => 36000,
            'status' => 'Selesai',
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Profil dan histori pembelian kopi')
            ->assertSee('Aulia Coffee')
            ->assertSee('Brown Sugar Latte');
    }
}

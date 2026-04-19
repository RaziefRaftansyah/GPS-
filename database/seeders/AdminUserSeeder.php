<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the owner account that can access the admin dashboard.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@kopikeliling.com'],
            [
                'name' => 'Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'owner',
                'device_id' => null,
            ],
        );
    }
}

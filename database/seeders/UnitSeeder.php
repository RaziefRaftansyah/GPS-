<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Seed the current unit catalog from production-like dashboard data.
     */
    public function run(): void
    {
        DB::table('units')->upsert([
            [
                'name' => 'This',
                'code' => 'GRBK-00',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:30:36',
                'updated_at' => '2026-04-23 09:30:36',
            ],
            [
                'name' => 'That',
                'code' => 'GRBK-01',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:30:48',
                'updated_at' => '2026-04-23 09:30:48',
            ],
            [
                'name' => 'There',
                'code' => 'GRBK-02',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:31:01',
                'updated_at' => '2026-04-23 09:31:01',
            ],
            [
                'name' => 'Those',
                'code' => 'GRBK-03',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:31:15',
                'updated_at' => '2026-04-23 09:31:15',
            ],
            [
                'name' => 'These',
                'code' => 'GRBK-04',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:31:32',
                'updated_at' => '2026-04-23 09:31:32',
            ],
            [
                'name' => 'Them',
                'code' => 'GRBK-05',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:31:52',
                'updated_at' => '2026-04-23 09:31:52',
            ],
            [
                'name' => 'They',
                'code' => 'GRBK-06',
                'device_id' => null,
                'status' => 'ready',
                'notes' => null,
                'created_at' => '2026-04-23 09:32:11',
                'updated_at' => '2026-04-23 09:32:11',
            ],
        ], ['code'], [
            'name',
            'device_id',
            'status',
            'notes',
            'created_at',
            'updated_at',
        ]);
    }
}

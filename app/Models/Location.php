<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'altitude',
        'battery_level',
        'is_charging',
        'is_moving',
        'activity',
        'event_type',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'device_id' => 'string',
            'latitude' => 'float',
            'longitude' => 'float',
            'accuracy' => 'float',
            'speed' => 'float',
            'heading' => 'float',
            'altitude' => 'float',
            'battery_level' => 'float',
            'is_charging' => 'boolean',
            'is_moving' => 'boolean',
            'activity' => 'string',
            'event_type' => 'string',
            'recorded_at' => 'datetime',
        ];
    }
}

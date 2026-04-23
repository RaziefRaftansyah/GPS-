<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'description',
        'image_path',
        'tags',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function selectedByDrivers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'driver_menu_selections', 'menu_id', 'user_id')
            ->withTimestamps();
    }
}

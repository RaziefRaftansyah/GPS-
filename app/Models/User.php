<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_active',
        'role',
        'device_id',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'device_id' => 'string',
            'password' => 'hashed',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function selectedMenus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'driver_menu_selections', 'user_id', 'menu_id')
            ->withTimestamps();
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverUnitAssignment::class, 'driver_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(DriverAttendanceLog::class, 'user_id');
    }

    public function activeDriverAssignment(): HasOne
    {
        return $this->hasOne(DriverUnitAssignment::class, 'driver_id')
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latestOfMany('assigned_at');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        $path = (string) ($this->profile_photo_path ?? '');

        if (blank($path)) {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return url('/'.ltrim($path, '/'));
        }

        return Storage::url($path);
    }
}

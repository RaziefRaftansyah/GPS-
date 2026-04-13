<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
            'password' => 'hashed',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverUnitAssignment::class, 'driver_id');
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
}

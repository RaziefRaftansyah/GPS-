<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverAttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_unit_assignment_id',
        'unit_name',
        'clocked_in_at',
        'clocked_out_at',
    ];

    protected function casts(): array
    {
        return [
            'clocked_in_at' => 'datetime',
            'clocked_out_at' => 'datetime',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(DriverUnitAssignment::class, 'driver_unit_assignment_id');
    }
}

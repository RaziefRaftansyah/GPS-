<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_attendance_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_unit_assignment_id')->nullable()->constrained('driver_unit_assignments')->nullOnDelete();
            $table->string('unit_name', 255)->nullable();
            $table->timestamp('clocked_in_at')->index();
            $table->timestamp('clocked_out_at')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'clocked_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_attendance_logs');
    }
};

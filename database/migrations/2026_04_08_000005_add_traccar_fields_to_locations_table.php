<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->string('device_id')->nullable()->after('id')->index();
            $table->decimal('accuracy', 10, 2)->nullable()->after('longitude');
            $table->decimal('speed', 10, 2)->nullable()->after('accuracy');
            $table->decimal('heading', 10, 2)->nullable()->after('speed');
            $table->decimal('altitude', 10, 2)->nullable()->after('heading');
            $table->decimal('battery_level', 5, 2)->nullable()->after('altitude');
            $table->boolean('is_charging')->nullable()->after('battery_level');
            $table->boolean('is_moving')->nullable()->after('is_charging');
            $table->string('activity', 100)->nullable()->after('is_moving');
            $table->string('event_type', 100)->nullable()->after('activity');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->dropColumn([
                'device_id',
                'accuracy',
                'speed',
                'heading',
                'altitude',
                'battery_level',
                'is_charging',
                'is_moving',
                'activity',
                'event_type',
            ]);
        });
    }
};

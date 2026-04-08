<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traccar_request_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('method', 20);
            $table->string('path');
            $table->string('content_type')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('headers')->nullable();
            $table->json('query_payload')->nullable();
            $table->json('form_payload')->nullable();
            $table->json('json_payload')->nullable();
            $table->json('normalized_payload')->nullable();
            $table->longText('raw_body')->nullable();
            $table->boolean('processed')->default(false)->index();
            $table->text('error_message')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traccar_request_logs');
    }
};

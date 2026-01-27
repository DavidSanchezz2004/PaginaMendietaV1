<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->cascadeOnDelete();
            $table->string('device_id', 120);         // generado por la app (UUID)
            $table->string('device_name', 120)->nullable();
            $table->enum('status', ['active','blocked'])->default('active');
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['app_user_id', 'device_id']);
            $table->index(['device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_devices');
    }
};
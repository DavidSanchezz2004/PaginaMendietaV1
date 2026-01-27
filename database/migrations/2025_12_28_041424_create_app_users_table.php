<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // o email si quieres
            $table->string('password');
            $table->enum('status', ['activo','inactivo'])->default('activo');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};

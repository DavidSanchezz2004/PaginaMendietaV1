<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_mfas', function ($table) {
    $table->id();
    $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

    $table->text('totp_secret')->nullable();         // guardado ENCRIPTADO
    $table->longText('recovery_codes')->nullable();
    $table->boolean('enabled')->default(false);
    $table->timestamp('confirmed_at')->nullable();   // cuando activó
    $table->timestamp('last_verified_at')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mfas');
    }
};

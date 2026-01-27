<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('country', 80)->nullable();        // Perú
            $table->string('city', 80)->nullable();           // Lima
            $table->string('postal_code', 20)->nullable();    // opcional

            $table->enum('document_type', ['dni', 'ruc'])->nullable();
            $table->string('document_number', 20)->nullable();

            $table->string('phone', 30)->nullable();
            $table->text('bio')->nullable();

            $table->timestamps();

            $table->index(['country', 'city']);
            $table->index(['document_type', 'document_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

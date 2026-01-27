<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // SUNAT
            $table->string('ruc', 11)->unique();
            $table->string('razon_social');
            $table->string('direccion_fiscal')->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('ubigeo', 10)->nullable();

            $table->string('sunat_estado', 50)->nullable();
            $table->string('sunat_condicion', 50)->nullable();

            // Interno
            $table->enum('estado_interno', ['Activo', 'Pendiente', 'Inactivo'])
                ->default('Activo');

            $table->text('notas_internas')->nullable();

            // Asignación a usuario interno
            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Respaldo SUNAT
            $table->json('sunat_raw')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

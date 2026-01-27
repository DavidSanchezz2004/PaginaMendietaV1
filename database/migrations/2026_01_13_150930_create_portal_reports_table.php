<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->string('titulo', 190);
            $table->unsignedTinyInteger('periodo_mes')->nullable();   // 1-12
            $table->unsignedSmallInteger('periodo_anio')->nullable(); // 2024, 2025...

            $table->enum('estado', ['borrador', 'publicado'])->default('borrador');

            // link público publish-to-web (solo admin)
            $table->text('powerbi_url_actual');

            // metadata opcional
            $table->text('nota_interna')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['company_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_reports');
    }
};

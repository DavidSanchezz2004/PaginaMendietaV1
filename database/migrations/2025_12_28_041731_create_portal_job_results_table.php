<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_job_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_job_id')->constrained('portal_jobs')->cascadeOnDelete();

            $table->boolean('ok')->default(false);
            $table->json('data')->nullable();
            $table->json('evidences')->nullable(); // paths

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_job_results');
    }
};

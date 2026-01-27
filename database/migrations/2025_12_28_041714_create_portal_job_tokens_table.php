<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_job_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_job_id')->constrained('portal_jobs')->cascadeOnDelete();

            $table->string('token_hash', 64); // sha256
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();

            $table->timestamps();

            $table->index(['portal_job_id','expires_at']);
            $table->unique(['token_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_job_tokens');
    }
};

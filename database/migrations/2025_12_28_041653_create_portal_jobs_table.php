<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();

            $table->foreignId('app_user_id')->constrained('app_users')->cascadeOnDelete();
            $table->string('device_id', 120);

            $table->string('portal', 20);
            $table->string('action', 80);

            $table->enum('status', ['pending','running','done','failed','canceled'])->default('pending');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['app_user_id','device_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_jobs');
    }
};

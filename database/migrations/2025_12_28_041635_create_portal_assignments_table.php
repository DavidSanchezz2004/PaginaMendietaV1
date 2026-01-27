<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();
            $table->foreignId('app_user_id')->constrained('app_users')->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['portal_account_id', 'app_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_assignments');
    }
};

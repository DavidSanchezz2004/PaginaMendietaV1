<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();

            $table->text('username_enc')->nullable();
            $table->text('password_enc');          // obligatorio
            $table->text('extra_enc')->nullable(); // json cifrado

            $table->timestamp('rotated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['portal_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_credentials');
    }
};

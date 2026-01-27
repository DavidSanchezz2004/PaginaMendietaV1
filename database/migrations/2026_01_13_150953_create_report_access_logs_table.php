<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_access_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('reporte_id')->nullable();

            $table->boolean('ok')->default(false);
            $table->string('reason', 120)->nullable();

            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('accessed_at')->useCurrent();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('reporte_id')->references('id')->on('portal_reports')->nullOnDelete();

            $table->index(['company_id', 'reporte_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_access_logs');
    }
};

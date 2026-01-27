<?php

// database/migrations/xxxx_xx_xx_create_portal_report_access_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('portal_report_access_logs', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->unsignedBigInteger('company_id')->nullable();
      $table->unsignedBigInteger('reporte_id');
      $table->boolean('ok')->default(false);
      $table->string('reason', 80)->nullable();
      $table->string('ip_address', 64)->nullable();
      $table->string('user_agent', 500)->nullable();
      $table->timestamps();

      $table->index(['company_id', 'reporte_id']);
      $table->index(['user_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('portal_report_access_logs');
  }
};

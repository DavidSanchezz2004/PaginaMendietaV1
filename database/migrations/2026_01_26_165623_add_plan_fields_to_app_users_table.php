<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('app_users', function (Blueprint $table) {
      $table->string('type', 20)->default('equipo')->after('password'); // equipo|cliente
      $table->string('plan', 30)->default('starter')->after('type');    // starter|oro|pro|empresa
      $table->unsignedSmallInteger('max_companies')->nullable()->after('plan'); // override
      $table->string('subscription_status', 20)->default('active')->after('status'); // active|overdue|suspended

      $table->index(['type']);
      $table->index(['plan']);
      $table->index(['subscription_status']);
    });
  }

  public function down(): void
  {
    Schema::table('app_users', function (Blueprint $table) {
      $table->dropIndex(['type']);
      $table->dropIndex(['plan']);
      $table->dropIndex(['subscription_status']);
      $table->dropColumn(['type','plan','max_companies','subscription_status']);
    });
  }
};

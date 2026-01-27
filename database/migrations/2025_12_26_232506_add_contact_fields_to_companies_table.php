<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('correo_principal')->nullable()->after('razon_social');
            $table->string('telefono', 50)->nullable()->after('correo_principal');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['correo_principal', 'telefono']);
        });
    }
};

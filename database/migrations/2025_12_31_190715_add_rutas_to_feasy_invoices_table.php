<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feasy_invoices', function (Blueprint $table) {

            // Ya existen en tu create_feasy_invoices, NO los vuelvas a crear:
            // $table->string('ruta_xml')->nullable()->after('nombre_archivo_xml');
            // $table->string('ruta_cdr')->nullable()->after('ruta_xml');
            // $table->string('ruta_pdf')->nullable()->after('ruta_cdr');

            // ✅ Solo agrega lo nuevo
            if (!Schema::hasColumn('feasy_invoices', 'consultado_at')) {
                $table->timestamp('consultado_at')->nullable()->after('ruta_pdf');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feasy_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('feasy_invoices', 'consultado_at')) {
                $table->dropColumn('consultado_at');
            }
        });
    }
};

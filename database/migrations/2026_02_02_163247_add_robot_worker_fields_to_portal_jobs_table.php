<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            // ✅ Información del worker usado para esta sesión
            $table->string('robot_worker_base_url', 255)->nullable()->after('status');
            $table->string('robot_worker_viewer_url', 255)->nullable()->after('robot_worker_base_url');
            
            // ✅ Session ID del robot (para referenciar en llamadas posteriores)
            $table->string('robot_session_id', 100)->nullable()->after('robot_worker_viewer_url');
            
            // Índice para buscar por session_id
            $table->index('robot_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            $table->dropIndex(['robot_session_id']);
            $table->dropColumn(['robot_worker_base_url', 'robot_worker_viewer_url', 'robot_session_id']);
        });
    }
};

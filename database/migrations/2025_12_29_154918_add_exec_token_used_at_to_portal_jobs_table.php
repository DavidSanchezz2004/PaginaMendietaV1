<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {

            // token “single-use”
            if (!Schema::hasColumn('portal_jobs', 'exec_token_used_at')) {
                $table->timestamp('exec_token_used_at')->nullable()->after('exec_token_expires_at');
            }

            // (opcional) si no existieran en tu tabla, agrega también:
            if (!Schema::hasColumn('portal_jobs', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('exec_token_used_at');
            }

            if (!Schema::hasColumn('portal_jobs', 'finished_at')) {
                $table->timestamp('finished_at')->nullable()->after('started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('portal_jobs', 'exec_token_used_at')) {
                $table->dropColumn('exec_token_used_at');
            }
            // no tiro started_at/finished_at en down por seguridad (si ya existían antes)
        });
    }
};

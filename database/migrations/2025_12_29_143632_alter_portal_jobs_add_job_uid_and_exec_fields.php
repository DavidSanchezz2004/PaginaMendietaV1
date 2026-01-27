<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            // Si no existe job_uid, lo agregamos
            if (!Schema::hasColumn('portal_jobs', 'job_uid')) {
                $table->string('job_uid', 40)->unique()->after('id');
            }

            if (!Schema::hasColumn('portal_jobs', 'device_id')) {
                $table->string('device_id', 80)->nullable()->index()->after('app_user_id');
            }

            if (!Schema::hasColumn('portal_jobs', 'status')) {
                $table->string('status', 20)->default('pending')->index()->after('device_id');
            }

            if (!Schema::hasColumn('portal_jobs', 'exec_token_hash')) {
                $table->string('exec_token_hash', 255)->nullable()->after('status');
            }

            if (!Schema::hasColumn('portal_jobs', 'exec_token_expires_at')) {
                $table->timestamp('exec_token_expires_at')->nullable()->after('exec_token_hash');
            }

            if (!Schema::hasColumn('portal_jobs', 'meta')) {
                $table->json('meta')->nullable()->after('exec_token_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            // En down, solo dropeamos si existen
            if (Schema::hasColumn('portal_jobs', 'meta')) $table->dropColumn('meta');
            if (Schema::hasColumn('portal_jobs', 'exec_token_expires_at')) $table->dropColumn('exec_token_expires_at');
            if (Schema::hasColumn('portal_jobs', 'exec_token_hash')) $table->dropColumn('exec_token_hash');
            if (Schema::hasColumn('portal_jobs', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('portal_jobs', 'device_id')) $table->dropColumn('device_id');

            // job_uid tiene unique index, por eso drop primero el índice si Laravel lo creó
            if (Schema::hasColumn('portal_jobs', 'job_uid')) {
                $table->dropUnique(['job_uid']);
                $table->dropColumn('job_uid');
            }
        });
    }
};

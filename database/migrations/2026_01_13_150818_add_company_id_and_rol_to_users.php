<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rol')) {
                $table->string('rol', 40)->default('cliente')->after('email');
            }

            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('rol');
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
                $table->index(['company_id', 'rol']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropIndex(['company_id', 'rol']);
                $table->dropColumn('company_id');
            }

            if (Schema::hasColumn('users', 'rol')) {
                $table->dropColumn('rol');
            }
        });
    }
};
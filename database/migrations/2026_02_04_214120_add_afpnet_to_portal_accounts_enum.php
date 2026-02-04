<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE portal_accounts MODIFY COLUMN portal ENUM('sunat','sunafil','afpnet') DEFAULT 'sunat'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE portal_accounts MODIFY COLUMN portal ENUM('sunat','sunafil','afp') DEFAULT 'sunat'");
    }
};
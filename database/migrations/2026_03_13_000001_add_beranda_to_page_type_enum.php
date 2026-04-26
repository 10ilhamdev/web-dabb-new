<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite cannot run MODIFY COLUMN ENUM
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Modify enum to include 'beranda'
        DB::statement("ALTER TABLE features MODIFY COLUMN page_type ENUM('none', 'beranda', 'onsite', 'real', '3d', 'book') DEFAULT 'none'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE features MODIFY COLUMN page_type ENUM('none', 'onsite', 'real', '3d', 'book') DEFAULT 'none'");
    }
};

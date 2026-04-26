<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL: change enum to add 'slideshow'
        DB::statement("ALTER TABLE features MODIFY COLUMN page_type ENUM('none','beranda','onsite','real','3d','book','slideshow') DEFAULT 'none'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE features MODIFY COLUMN page_type ENUM('none','beranda','onsite','real','3d','book') DEFAULT 'none'");
    }
};

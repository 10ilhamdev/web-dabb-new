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
        Schema::table('visit_registrations', function (Blueprint $table) {
            $table->index('created_at', 'idx_visit_registrations_created_at');
        });

        Schema::table('page_views', function (Blueprint $table) {
            $table->index('created_at', 'idx_page_views_created_at');
        });

        Schema::table('archival_consultations', function (Blueprint $table) {
            $table->index('created_at', 'idx_archival_consultations_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archival_consultations', function (Blueprint $table) {
            $table->dropIndex('idx_archival_consultations_created_at');
        });

        Schema::table('page_views', function (Blueprint $table) {
            $table->dropIndex('idx_page_views_created_at');
        });

        Schema::table('visit_registrations', function (Blueprint $table) {
            $table->dropIndex('idx_visit_registrations_created_at');
        });
    }
};

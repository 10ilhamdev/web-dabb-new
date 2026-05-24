<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visit_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('institution')->nullable();
            $table->string('position')->nullable();
            $table->date('visit_date');
            $table->string('visit_time')->nullable(); // pagi / siang
            $table->integer('visitor_count')->default(1);
            $table->string('visit_purpose'); // edukasi / penelitian / kunker
            $table->string('surat_file')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('keterangan')->nullable();
            $table->json('form_data')->nullable(); // for dynamic fields
            $table->timestamps();
        });

        Schema::create('archival_consultations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('institution')->nullable();
            $table->string('email');
            $table->text('detail');
            $table->string('attachment')->nullable();
            $table->json('form_data')->nullable(); // for dynamic fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archival_consultations');
        Schema::dropIfExists('visit_registrations');
    }
};

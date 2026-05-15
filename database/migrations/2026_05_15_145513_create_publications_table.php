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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->string('type'); // pengumuman, berita, galeri
            $table->date('published_at')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->json('images')->nullable();
            $table->json('extra_data')->nullable(); // For PDF file path etc
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};

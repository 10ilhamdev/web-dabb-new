<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_slideshow_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('features')->onDelete('cascade');
            $table->foreignId('feature_page_id')
                ->nullable()
                ->constrained('feature_pages')
                ->onDelete('cascade');
            $table->enum('slide_type', ['hero', 'text', 'carousel', 'video', 'text_carousel'])->default('text');
            $table->string('title')->nullable();
            $table->string('title_en')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->json('images')->nullable();
            $table->json('image_urls')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();
            $table->json('carousel_video_urls')->nullable();
            $table->enum('layout', ['left', 'right', 'center'])->default('center');
            $table->string('bg_color')->nullable();
            $table->json('info_popup')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_slideshow_slides');
    }
};
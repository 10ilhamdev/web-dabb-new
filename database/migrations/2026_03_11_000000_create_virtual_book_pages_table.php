<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('cover_position')->nullable();
            $table->float('cover_scale', 8, 2)->default(1.00);
            $table->json('cover_texts')->nullable();
            $table->json('title_position')->nullable();
            $table->string('back_title')->nullable();
            $table->string('back_cover_image')->nullable();
            $table->json('back_cover_position')->nullable();
            $table->float('back_cover_scale', 8, 2)->default(1.00);
            $table->json('back_title_position')->nullable();
            $table->json('back_cover_texts')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('virtual_book_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('image')->nullable();
            $table->integer('image_height')->default(50)->comment('Image height percentage (10-100)');
            $table->string('image_fit_mode', 20)->default('contained');
            $table->json('images')->nullable();
            $table->json('image_positions')->nullable();
            $table->json('text_position')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('page_number')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->boolean('is_back_cover')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_book_pages');
        Schema::dropIfExists('books');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });

        Schema::create('feature_page_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_page_id');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->json('images')->nullable();
            $table->json('image_positions')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('feature_page_id')->references('id')->on('feature_pages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_page_sections');
        Schema::dropIfExists('feature_pages');
    }
};

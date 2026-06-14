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
        Schema::create('virtual_slideshow_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->integer('order')->default(0);
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->unique(['feature_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_slideshow_pages');
    }
};

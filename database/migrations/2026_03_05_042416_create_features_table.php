<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('type')->default('link'); // link or dropdown
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('path')->nullable();
            $table->integer('order')->default(0);
            $table->longText('content')->nullable();
            $table->longText('content_en')->nullable();
            $table->boolean('is_virtual_book')->default(false);
            $table->enum('virtual_room_type', ['none', 'real', '3d', 'book'])->default('none');
            $table->enum('page_type', ['none', 'beranda', 'onsite', 'real', '3d', 'book', 'slideshow', 'profile'])->default('none')->nullable();
            $table->string('book_cover')->nullable();
            $table->string('book_thumbnail')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('features')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};

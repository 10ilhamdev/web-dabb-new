<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual3d_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('virtual3d_room_id');
            $table->string('wall'); // 'front', 'back', 'left', 'right'
            $table->string('type'); // 'image', 'video'
            $table->string('file_path');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Positioning variables using percentages (from left and top) and size percentages
            $table->decimal('position_x', 5, 2)->default(50.00); // 0-100%
            $table->decimal('position_y', 5, 2)->default(50.00); // 0-100%
            $table->decimal('width', 5, 2)->default(30.00);      // 0-100%
            $table->decimal('height', 5, 2)->default(40.00);     // 0-100%

            $table->timestamps();

            $table->foreign('virtual3d_room_id', 'virtual3d_media_virtual3d_room_id_foreign')
                ->references('id')
                ->on('virtual3d_rooms')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual3d_media');
    }
};
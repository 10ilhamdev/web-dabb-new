<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_hotspots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('virtual_room_id');
            $table->unsignedBigInteger('target_room_id')->nullable();
            $table->double('yaw');
            $table->double('pitch');
            $table->string('text_tooltip', 255);
            $table->timestamps();

            $table->foreign('virtual_room_id', 'virtual_hotspots_virtual_room_id_foreign')
                ->references('id')
                ->on('virtual_rooms')
                ->onDelete('cascade');
            $table->foreign('target_room_id', 'virtual_hotspots_target_room_id_foreign')
                ->references('id')
                ->on('virtual_rooms')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_hotspots');
    }
};
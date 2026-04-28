<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual3d_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('wall_color')->default('#e5e7eb');
            $table->string('floor_color')->default('#8B7355');
            $table->string('ceiling_color')->default('#f5f5f5');
            $table->json('doors')->nullable(); // {"front":{...},"left":{...},"right":{...},"back":{...}}
            $table->string('door_link_type')->default('none'); // 'none', 'feature', 'room', 'url'
            $table->string('door_wall')->default('back');
            $table->string('door_target')->nullable();
            $table->string('door_label')->nullable();
            $table->timestamps();
        });

        // Migrate existing single-door data into the new JSON doors column
        $rooms = DB::table('virtual3d_rooms')->get();
        foreach ($rooms as $room) {
            $doorWall = $room->door_wall ?? 'back';
            $doors = [
                'front' => ['link_type' => 'none', 'target' => null, 'label' => null],
                'left'  => ['link_type' => 'none', 'target' => null, 'label' => null],
                'right' => ['link_type' => 'none', 'target' => null, 'label' => null],
                'back'  => ['link_type' => 'none', 'target' => null, 'label' => null],
            ];

            if ($room->door_link_type && $room->door_link_type !== 'none') {
                $doors[$doorWall] = [
                    'link_type' => $room->door_link_type,
                    'target'    => $room->door_target,
                    'label'     => $room->door_label,
                ];
            }

            DB::table('virtual3d_rooms')
                ->where('id', $room->id)
                ->update(['doors' => json_encode($doors)]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual3d_rooms');
    }
};

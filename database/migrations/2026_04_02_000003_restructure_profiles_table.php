<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create profiles and profile_sections tables matching dabb_backup.sql exactly.
     * Handles both fresh install and existing data migration from old denormalized schema.
     */
    public function up(): void
    {
        $hasOldProfiles = Schema::hasTable('profiles')
            && Schema::hasColumn('profiles', 'tugas_fungsi_title');

        if ($hasOldProfiles) {
            $oldProfiles = DB::table('profiles')->get();

            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('feature_id');
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->longText('description')->nullable();
                $table->longText('description_en')->nullable();
                $table->enum('type', ['default', 'sdm_chart', 'struktur_image', 'tugas_fungsi'])->default('default');
                $table->string('subtitle')->nullable();
                $table->string('subtitle_en')->nullable();
                $table->string('link_text')->nullable();
                $table->string('link_url')->nullable();
                $table->string('logo_path')->nullable();
                $table->json('chart_data')->nullable();
                $table->json('images')->nullable();
                $table->json('image_positions')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->foreign('feature_id', 'profiles_new_feature_id_foreign')
                    ->references('id')
                    ->on('features')
                    ->onDelete('cascade');
                $table->index('feature_id', 'profiles_new_feature_id_index');
                $table->index('order', 'profiles_new_order_index');
            });

            foreach ($oldProfiles as $oldProfile) {
                $type = 'default';
                $title = $oldProfile->title ?? null;
                $title_en = $oldProfile->title_en ?? null;
                $description = $oldProfile->description ?? null;
                $description_en = $oldProfile->description_en ?? null;
                $subtitle = null;
                $subtitle_en = null;
                $link_text = null;
                $link_url = null;
                $logo_path = null;
                $chart_data = null;
                $images = null;

                if (!empty($oldProfile->tugas_fungsi_title)) {
                    $type = 'tugas_fungsi';
                    $title = $oldProfile->tugas_fungsi_title;
                    $title_en = $oldProfile->tugas_fungsi_title_en;
                    $description = $oldProfile->tugas_fungsi_desc;
                    $link_text = $oldProfile->tugas_fungsi_link_text;
                    $link_url = $oldProfile->tugas_fungsi_link_url;
                    if ($oldProfile->tugas_fungsi_image) {
                        $images = json_encode([$oldProfile->tugas_fungsi_image]);
                    }
                } elseif (!empty($oldProfile->struktur_title)) {
                    $type = 'struktur_image';
                    $title = $oldProfile->struktur_title;
                    $title_en = $oldProfile->struktur_title_en;
                    $description = $oldProfile->struktur_desc;
                    $logo_path = $oldProfile->struktur_logo;
                    if ($oldProfile->struktur_image) {
                        $images = json_encode([$oldProfile->struktur_image]);
                    }
                } elseif (!empty($oldProfile->sdm_title)) {
                    $type = 'sdm_chart';
                    $title = $oldProfile->sdm_title;
                    $title_en = $oldProfile->sdm_title_en;
                    $description = $oldProfile->sdm_desc;
                    $subtitle = $oldProfile->sdm_subtitle;
                    $subtitle_en = $oldProfile->sdm_subtitle_en;
                    if (!empty($oldProfile->sdm_chart_data)) {
                        $chart_data = $oldProfile->sdm_chart_data;
                    }
                }

                DB::table('profiles')->insert([
                    'feature_id' => $oldProfile->feature_id,
                    'title' => $title ?? '',
                    'title_en' => $title_en,
                    'description' => $description,
                    'description_en' => $description_en,
                    'type' => $type,
                    'subtitle' => $subtitle,
                    'subtitle_en' => $subtitle_en,
                    'link_text' => $link_text,
                    'link_url' => $link_url,
                    'logo_path' => $logo_path,
                    'chart_data' => $chart_data,
                    'images' => $images,
                    'image_positions' => null,
                    'order' => 1,
                    'created_at' => $oldProfile->created_at ?? now(),
                    'updated_at' => $oldProfile->updated_at ?? now(),
                ]);
            }

            Schema::dropIfExists('profile_sections');
            Schema::dropIfExists('profiles');
        } else {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('feature_id');
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->longText('description')->nullable();
                $table->longText('description_en')->nullable();
                $table->enum('type', ['default', 'sdm_chart', 'struktur_image', 'tugas_fungsi'])->default('default');
                $table->string('subtitle')->nullable();
                $table->string('subtitle_en')->nullable();
                $table->string('link_text')->nullable();
                $table->string('link_url')->nullable();
                $table->string('logo_path')->nullable();
                $table->json('chart_data')->nullable();
                $table->json('images')->nullable();
                $table->json('image_positions')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->foreign('feature_id', 'profiles_new_feature_id_foreign')
                    ->references('id')
                    ->on('features')
                    ->onDelete('cascade');
                $table->index('feature_id', 'profiles_new_feature_id_index');
                $table->index('order', 'profiles_new_order_index');
            });

            Schema::dropIfExists('profile_sections');
        }

        Schema::create('profile_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('title')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->json('images')->nullable();
            $table->json('image_positions')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('profile_id', 'profile_sections_profile_id_foreign')
                ->references('id')
                ->on('profiles')
                ->onDelete('cascade');
            $table->index('profile_id', 'profile_sections_profile_id_index');
            $table->index('order', 'profile_sections_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_sections');
        Schema::dropIfExists('profiles');
    }
};
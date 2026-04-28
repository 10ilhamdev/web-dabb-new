<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_columns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('column_name', 100);
            $table->string('column_type', 50);
            $table->string('column_label', 100);
            $table->integer('column_length')->nullable();
            $table->boolean('is_nullable')->default(true);
            $table->boolean('has_gender')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_foreign')->default(false);
            $table->string('references_table', 100)->nullable();
            $table->string('references_column', 100)->nullable();
            $table->string('on_delete', 30)->nullable();
            $table->string('on_update', 30)->nullable();
            $table->boolean('is_unsigned')->default(false);
            $table->boolean('is_auto_increment')->default(false);
            $table->string('default_value', 255)->nullable();
            $table->text('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Enable has_gender for jenis_kelamin column in umum and pelajar_mahasiswa roles
        $genderRoleIds = DB::table('roles')
            ->whereIn('name', ['umum', 'pelajar_mahasiswa'])
            ->pluck('id');

        DB::table('role_columns')
            ->whereIn('role_id', $genderRoleIds)
            ->where('column_name', 'jenis_kelamin')
            ->update(['has_gender' => true]);
    }

    public function down(): void
    {
        Schema::dropIfExists('role_columns');
    }
};
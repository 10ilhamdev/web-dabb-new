<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('table_name')->nullable();
            $table->string('relation_name')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_registerable')->default(false);
            $table->string('badge_color', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default roles
        $now = now();
        DB::table('roles')->insert([
            ['name' => 'admin', 'label' => 'Administrator', 'table_name' => 'user_admins', 'relation_name' => 'userAdmin', 'is_system' => true, 'is_registerable' => false, 'badge_color' => 'red', 'description' => 'Akun administrator dengan akses penuh ke CMS.', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'pegawai', 'label' => 'Pegawai', 'table_name' => 'user_pegawais', 'relation_name' => 'userPegawai', 'is_system' => true, 'is_registerable' => false, 'badge_color' => 'yellow', 'description' => 'Akun pegawai ANRI.', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'umum', 'label' => 'Umum', 'table_name' => 'user_umums', 'relation_name' => 'userUmum', 'is_system' => true, 'is_registerable' => true, 'badge_color' => 'gray', 'description' => 'Pengunjung umum.', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'pelajar_mahasiswa', 'label' => 'Pelajar / Mahasiswa', 'table_name' => 'user_pelajars', 'relation_name' => 'userPelajar', 'is_system' => true, 'is_registerable' => true, 'badge_color' => 'blue', 'description' => 'Pelajar atau mahasiswa yang melakukan penelitian.', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'instansi_swasta', 'label' => 'Instansi / Swasta', 'table_name' => 'user_instansi_swasta', 'relation_name' => 'userInstansi', 'is_system' => true, 'is_registerable' => true, 'badge_color' => 'purple', 'description' => 'Perwakilan instansi atau swasta.', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
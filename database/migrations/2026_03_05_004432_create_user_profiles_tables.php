<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_umums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->binary('kartu_identitas')->nullable();
            $table->string('nomor_kartu_identitas', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_whatsapp', 20)->nullable();
            $table->enum('jenis_keperluan', ['Hanya Daftar Akun', 'Penelitian', 'Kunjungan']);
            $table->text('judul_keperluan');
            $table->timestamps();

            $table->unique('nomor_kartu_identitas', 'idx_user_umums_nomor_kartu_identitas');
            $table->unique('nomor_whatsapp', 'idx_user_umums_nomor_whatsapp');
            $table->foreign('user_id', 'fk_user_umums_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create('user_pelajars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->binary('kartu_identitas')->nullable();
            $table->string('nomor_kartu_identitas', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_whatsapp', 20)->nullable();
            $table->enum('jenis_keperluan', ['Hanya Daftar Akun', 'Penelitian', 'Kunjungan']);
            $table->text('judul_keperluan');
            $table->timestamps();

            $table->unique('nomor_kartu_identitas', 'idx_user_pelajars_nomor_kartu_identitas');
            $table->unique('nomor_whatsapp', 'idx_user_pelajars_nomor_whatsapp');
            $table->foreign('user_id', 'fk_user_pelajars_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });

        Schema::create('user_instansi_swasta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->binary('kartu_identitas')->nullable();
            $table->string('nomor_kartu_identitas', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_whatsapp', 20)->nullable();
            $table->enum('jenis_keperluan', ['Hanya Daftar Akun', 'Penelitian', 'Kunjungan']);
            $table->text('judul_keperluan');
            $table->timestamps();

            $table->unique('nomor_kartu_identitas', 'idx_user_instansi_swasta_nomor_kartu_identitas');
            $table->unique('nomor_whatsapp', 'idx_user_instansi_swasta_nomor_whatsapp');
            $table->foreign('user_id', 'fk_user_instansi_swasta_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_instansi_swasta');
        Schema::dropIfExists('user_pelajars');
        Schema::dropIfExists('user_umums');
    }
};

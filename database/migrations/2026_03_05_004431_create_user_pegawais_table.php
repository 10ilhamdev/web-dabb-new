<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pegawais', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('user_id');
            $table->string('nip', 18);
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->binary('kartu_identitas')->nullable();
            $table->string('nomor_kartu_identitas', 30)->nullable();
            $table->text('alamat');
            $table->string('nomor_whatsapp', 20);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('jabatan', ['Kepala ANRI', 'Sekretaris Utama', 'Deputi Bidang Pembinaan Kearsipan', 'Deputi Bidang Informasi dan Pengembangan Sistem Kearsipan', 'Deputi Bidang Konservasi Arsip', 'Direktur Kearsipan Pusat', 'Direktur Kearsipan Daerah I & II', 'Directeur SDM Kearsipan dan Sertifikasi', 'Arsiparis Ahli Pertama', 'Arsiparis Ahli Muda', 'Arsiparis Ahli Madya', 'Arsiparis Ahli Utama', 'Arsiparis Terampil', 'Arsiparis Mahir', 'Arsiparis Penyelia', 'Konservator Arsip', 'Restorator Arsip', 'Reprogrator Arsip', 'Agendaris', 'Protokol', 'Sekretaris Pimpinan', 'Bendahara Gaji']);
            $table->enum('pangkat_golongan', ['IV/e (Pembina Utama)', 'IV/d (Pembina Utama Madya)', 'IV/c (Pembina Utama Muda)', 'IV/b (Pembina Tingkat I)', 'IV/a (Pembina)', 'III/d (Penata Tingkat I)', 'III/c (Penata)', 'III/b (Penata Muda Tingkat I)', 'III/a (Penata Muda)', 'II/d (Pengatur Tingkat I)', 'II/c (Pengatur)', 'II/b (Pengatur Muda Tingkat I)', 'II/a (Pengatur Muda)', 'I/d (Juru Tingkat I)', 'I/c (Juru)', 'I/b (Juru Muda Tingkat I)', 'I/a (Juru Muda)']);
            $table->timestamps();

            $table->unique('nip', 'idx_user_pegawais_nip');
            $table->unique('nomor_kartu_identitas', 'idx_user_pegawais_nomor_kartu_identitas');
            $table->unique('nomor_whatsapp', 'idx_user_pegawais_nomor_whatsapp');
            $table->foreign('user_id', 'fk_user_pegawais_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pegawais');
    }
};

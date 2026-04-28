<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUmum extends Model
{
    protected $table = 'user_umums';

    protected $fillable = [
        'user_id',
        'id',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'kartu_identitas',
        'nomor_kartu_identitas',
        'alamat',
        'nomor_whatsapp',
        'jenis_keperluan',
        'judul_keperluan',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
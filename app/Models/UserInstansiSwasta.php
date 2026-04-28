<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInstansiSwasta extends Model
{
    protected $table = 'user_instansi_swasta';

    protected $fillable = [
        'user_id',
        'id',
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
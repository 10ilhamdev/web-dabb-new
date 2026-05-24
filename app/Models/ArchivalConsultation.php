<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivalConsultation extends Model
{
    protected $fillable = [
        'name',
        'institution',
        'email',
        'detail',
        'attachment',
        'form_data',
        'is_replied',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];
}

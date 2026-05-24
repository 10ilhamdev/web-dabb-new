<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'institution',
        'position',
        'visit_date',
        'visit_time',
        'visitor_count',
        'visit_purpose',
        'surat_file',
        'status',
        'keterangan',
        'form_data',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'form_data' => 'array',
    ];
}

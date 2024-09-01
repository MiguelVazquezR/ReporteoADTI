<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine',
        'main_email',
        'cco',
        'subject',
        'description',
        'date',
        'frecuency',
        'time',
    ];

    protected $casts = [
        'cco' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_name', // Nombre de dashboard de metabase
        'main_email',
        'cco',
        'subject',
        'description',
        'weekday',
        'frecuency',
        'time',
    ];

    protected $casts = [
        'cco' => 'array',
    ];
}

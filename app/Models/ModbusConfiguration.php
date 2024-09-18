<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModbusConfiguration extends Model
{
    use HasFactory;

    protected $fillable = ['host', 'port', 'machine', 'sampling_minutes'];
}

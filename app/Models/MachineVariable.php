<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_name',
        'variable_name',
        'variable_original_name',
        'variable_description',
        'variable_address',
    ];
}

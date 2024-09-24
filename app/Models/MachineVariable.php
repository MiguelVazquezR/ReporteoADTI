<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_name',
        'name',
        'description',
        'address',
        'type',
        'words',
        'filters',
        'is_active',
    ];
}

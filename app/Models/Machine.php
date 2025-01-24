<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_name',
        'image',
        'in_view',
    ];

    // relaciones 
    public function variables()
    {
        return $this->hasMany(MachineVariable::class);
    }
}

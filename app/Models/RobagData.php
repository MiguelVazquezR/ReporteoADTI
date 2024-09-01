<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RobagData extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Cargar las variables desde la base de datos
        $machineVariables = MachineVariable::where('machine_name', 'Robag')->get()->pluck('variable_original_name')->toArray();

        // Asignar los valores obtenidos al fillable
        $this->fillable = $machineVariables;
    }
}

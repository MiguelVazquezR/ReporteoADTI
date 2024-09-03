<?php

namespace App\Http\Controllers;

use App\Models\ModbusConfiguration;
use Illuminate\Http\Request;

class ModbusConfigurationController extends Controller
{
    public function update(Request $request, ModbusConfiguration $modbus_configuration)
    {
        // Validar los datos de entrada
        $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|numeric|min:1|max:65535',
            'machine' => 'required|string|min:1|max:255',
        ]);

        // Actualizar o crear la configuración
        $modbus_configuration->update($request->all());
    }

    public function show()
    {
        $config = ModbusConfiguration::first();
        return response()->json($config);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ModbusConfiguration;
use App\Services\ModbusService;
use Exception;
use Illuminate\Http\Request;

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Utils\Endian;

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

    public function readModbusData()
    {
        $modbuService = new ModbusService('Robag');
        $data = $modbuService->getMachineData();
        return $data;
    }
}

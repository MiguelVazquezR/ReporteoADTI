<?php

use App\Models\ModbusConfiguration;
use App\Models\RobagData;
use App\Services\ModbusService;
use Illuminate\Support\Facades\Schedule;

Schedule::command('reports:send-scheduled-emails')->everyTwoMinutes();

// leer datos de maquina 'Robag1' y guardar en BDD local
$samplingMinutes = ModbusConfiguration::first()->sampling_minutes;
if ($samplingMinutes == 1) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag1');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create([
                'data' => $data
            ]);
        }
    })->everyMinute();
} else if ($samplingMinutes == 2) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag1');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create([
                'data' => $data
            ]);
        }
    })->everyTwoMinutes();
} else if ($samplingMinutes == 5) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag1');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create([
                'data' => $data
            ]);
        }
    })->everyFiveMinutes();
} else if ($samplingMinutes == 10) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag1');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create([
                'data' => $data
            ]);
        }
    })->everyTenMinutes();
}

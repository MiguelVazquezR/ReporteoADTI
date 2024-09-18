<?php

use App\Models\ModbusConfiguration;
use App\Models\RobagData;
use App\Services\ModbusService;
use Illuminate\Support\Facades\Schedule;

Schedule::command('reports:send-scheduled-emails')->everyTwoMinutes();



// leer datos de maquina 'Robag' y guardar en BDD local
$samplingMinutes = ModbusConfiguration::first()->sampling_minutes;
if ($samplingMinutes == 1) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create($data);
        }
    })->everyMinute();
} else if ($samplingMinutes == 2) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create($data);
        }
    })->everyTwoMinutes();
} else if ($samplingMinutes == 5) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create($data);
        }
    })->everyFiveMinutes();
} else if ($samplingMinutes == 10) {
    Schedule::call(function () {
        $modbuService = new ModbusService('Robag');
        $data = $modbuService->getMachineData();
        if ($data) {
            RobagData::create($data);
        }
    })->everyTenMinutes();
}

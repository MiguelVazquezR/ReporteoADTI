<?php

use App\Models\RobagData;
use App\Services\ModbusService;
use Illuminate\Support\Facades\Schedule;

Schedule::command('reports:send-scheduled-emails')->everyTwoMinutes();

// leer datos de maquina 'Robag' cada 5 minutos y guardar en BDD local
Schedule::call(function () {
    $modbuService = new ModbusService('Robag');
    $data = $modbuService->getMachineData();
    if ($data) {
        RobagData::create($data);
    }
})->everyTwoMinutes();

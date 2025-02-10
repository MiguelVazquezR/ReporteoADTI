<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('reports:send-scheduled-emails')->everyThirtyMinutes();
Schedule::command('app:db-backup')->dailyAt('00:00');

// leer datos de maquina 'Robag1' y guardar en BDD local
// $samplingMinutes = ModbusConfiguration::first()->sampling_minutes;
// if ($samplingMinutes == 1) {
//     Schedule::call(function () {
//         $modbuService = new ModbusService('Robag1');
//         $data = $modbuService->getMachineData();
//         if ($data && !array_key_exists('error', $data)) {
//             RobagData::create([
//                 'data' => $data
//             ]);
//         }
//     })->everyMinute();
// } else if ($samplingMinutes == 2) {
//     Schedule::call(function () {
//         $modbuService = new ModbusService('Robag1');
//         $data = $modbuService->getMachineData();
//         if ($data && !array_key_exists('error', $data)) {
//             RobagData::create([
//                 'data' => $data
//             ]);
//         }
//     })->everyTwoMinutes();
// } else if ($samplingMinutes == 5) {
//     Schedule::call(function () {
//         $modbuService = new ModbusService('Robag1');
//         $data = $modbuService->getMachineData();
//         if ($data && !array_key_exists('error', $data)) {
//             RobagData::create([
//                 'data' => $data
//             ]);
//         }
//     })->everyFiveMinutes();
// } else if ($samplingMinutes == 10) {
//     Schedule::call(function () {
//         $modbuService = new ModbusService('Robag1');
//         $data = $modbuService->getMachineData();
//         if ($data && !array_key_exists('error', $data)) {
//             RobagData::create([
//                 'data' => $data
//             ]);
//         }
//     })->everyTenMinutes();
// }

<?php

use App\Http\Controllers\MachineVariableController;
use App\Http\Controllers\ModbusConfigurationController;
use App\Http\Controllers\RobagDataController;
use App\Http\Controllers\ScheduleEmailController;
use App\Models\ModbusConfiguration;
use App\Models\ScheduleEmail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $schedule_settings = ScheduleEmail::firstWhere('machine', 'Robag');
    $modbus_configurations = ModbusConfiguration::firstWhere('machine', 'Robag');

    return Inertia::render('Home/Home', [
        'schedule_settings' => $schedule_settings,
        'modbus_configurations' => $modbus_configurations,
        // 'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
        // 'today_data' => $today_data,
    ]);
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// ------- maquinas y sus variables rutas --------
Route::resource('machine-variables', MachineVariableController::class);
Route::post('machine-variables/massive-delete', [MachineVariableController::class, 'massiveDelete'])->name('machine-variables.massive-delete');
Route::get('machine-variables-get-variables/{machine}', [MachineVariableController::class, 'getVariables'])->name('machine-variables.get-variables');


//--------------- robag data routes ------------------
Route::get('robag-export-report', [RobagDataController::class, 'generateReport'])->name('robag.export-report');
Route::get('robag-get-variable-data', [RobagDataController::class, 'getVariableData'])->name('robag.get-variable-data');
Route::post('robag-get-data-by-date-range', [RobagDataController::class, 'getDataByDateRange'])->name('robag.get-data-by-date-range');
Route::post('robag-email-report', [RobagDataController::class, 'emailReport'])->name('robag.email-report');


// --------------- rutas de configuraciones de programacion de correo -------------------------
Route::resource('schedule-email-settings', ScheduleEmailController::class);


//--------------- rutas configuracon de modbus ----------------------
Route::resource('/modbus-configuration', ModbusConfigurationController::class);
Route::get('/modbus-configuration-test', [ModbusConfigurationController::class, 'readModbusData']);//**// PRUEBAS DE LECTURA */

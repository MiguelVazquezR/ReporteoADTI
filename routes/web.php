<?php

use App\Http\Controllers\MachineVariableController;
use App\Http\Controllers\ModbusConfigurationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\RobagDataController;
use App\Http\Controllers\ScheduleEmailController;
use App\Models\MachineVariable;
use App\Models\ModbusConfiguration;
use App\Models\ScheduleEmail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $schedule_settings = ScheduleEmail::firstWhere('machine', 'Robag1');
    $modbus_configurations = ModbusConfiguration::firstWhere('machine', 'Robag1');
    $variables = MachineVariable::where('machine_name', 'Robag1')->get();

    return Inertia::render('Home/Home', [
        'schedule_settings' => $schedule_settings,
        'modbus_configurations' => $modbus_configurations,
        'variables' => $variables,
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


//Creacion de pdf con Spatie Browsershot -----------
Route::get('/download-pdf', [PdfController::class, 'downloadPdf'])->name('download.pdf');
Route::post('/upload-pdf', [PdfController::class, 'uploadPdf'])->name('upload.pdf');
Route::post('/save-pdf', [PdfController::class, 'savePdf'])->name('save.pdf');


// ------- maquinas y sus variables rutas --------
Route::resource('machine-variables', MachineVariableController::class);
Route::post('machine-variables/massive-delete', [MachineVariableController::class, 'massiveDelete'])->name('machine-variables.massive-delete');
Route::get('machine-variables-get-variables/{machine}', [MachineVariableController::class, 'getVariables'])->name('machine-variables.get-variables');


//--------------- robag data routes ------------------
Route::get('robag-export-report', [RobagDataController::class, 'generateReport'])->name('robag.export-report');
Route::get('robag-get-variable-data', [RobagDataController::class, 'getVariableData'])->name('robag.get-variable-data');
Route::post('robag-get-data-by-date-range', [RobagDataController::class, 'getDataByDateRange'])->name('robag.get-data-by-date-range');
Route::post('robag-email-report', [RobagDataController::class, 'emailReport'])->name('robag.email-report');
Route::get('/robag-get-modbus-registers', [RobagDataController::class, 'getModbusRegisters'])->name('robag.get-modbus-registers');


// --------------- rutas de configuraciones de programacion de correo -------------------------
Route::resource('schedule-email-settings', ScheduleEmailController::class);


//--------------- rutas configuracon de modbus ----------------------
Route::resource('/modbus-configuration', ModbusConfigurationController::class);
// Route::get('/modbus-configuration-test', [ModbusConfigurationController::class, 'readModbusData']);//**// PRUEBAS DE LECTURA */


Route::get('/pdf-template', function () {
    $bpm = intval(request('bpm'));
    $dates = request('dates');
    $date = request('date');
    $timeSlots = request('timeSlots');
    $selectedVariables = request('selectedVariables') ?? [];
    // return compact('bpm', 'dates', 'date', 'timeSlots', 'selectedVariables'); 
    return inertia('Home/Template', compact('bpm', 'dates', 'date', 'timeSlots', 'selectedVariables'));
})->name('robag.pdf-template');


Route::get('/pdf-example', function () {
    return inertia('Home/ExamplePdf');
})->name('pdf.example');


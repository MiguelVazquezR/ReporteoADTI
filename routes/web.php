<?php

use App\Http\Controllers\MachineVariableController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineDataController;
use App\Http\Controllers\ModbusConfigurationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ScheduleEmailController;
use App\Http\Controllers\TutorialController;
use App\Models\Machine;
use App\Models\MachineVariable;
use App\Models\ModbusConfiguration;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $machineInView = Machine::firstWhere('in_view', true);
    $modbus_configurations = ModbusConfiguration::firstWhere('machine', 'Robag1');
    $variables = MachineVariable::where('machine_id', $machineInView?->id)->get();

    return Inertia::render('Home/Home', [
        'modbus_configurations' => $modbus_configurations,
        'variables' => $variables,
        'machines' => Machine::all(),
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
Route::get('/test-pdf', [PdfController::class, 'generateReportPDF']);
Route::get('/test-excel/{dashboardName}', [PdfController::class, 'generateReportExcel']);


// ------- maquinas y sus variables rutas --------
Route::resource('machine-variables', MachineVariableController::class);
Route::post('machine-variables/massive-delete', [MachineVariableController::class, 'massiveDelete'])->name('machine-variables.massive-delete');
Route::post('machine-variables/massive-toggle-status', [MachineVariableController::class, 'massiveToggleStatus'])->name('machine-variables.massive-toggle-status');
Route::get('machine-variables-get-variables', [MachineVariableController::class, 'getVariables'])->name('machine-variables.get-variables');


// ------- maquinas rutas --------
Route::resource('machines', MachineController::class);
Route::put('machines/update-in-view/{machine}', [MachineController::class, 'updateInView'])->name('machines.update-in-view');
Route::post('machines/update-with-media/{machine}', [MachineController::class, 'updateWithMedia'])->name('machines.update-with-media');


// ------- tutoriales rutas --------
Route::resource('tutorials', TutorialController::class);


//--------------- machines data routes ------------------
Route::post('machine-data-get-data-by-date-range', [MachineDataController::class, 'getDataByDateRange'])->name('machine-data.get-data-by-date-range');
Route::get('/machine-data-pdf-template', [MachineDataController::class, 'pdfTemplate'])->name('machine-data.pdf-template');
Route::post('machine-data-email-report', [MachineDataController::class, 'emailReport'])->name('machine-data.email-report');


// --------------- rutas de configuraciones de programacion de correo -------------------------
Route::resource('schedule-email-settings', ScheduleEmailController::class);
Route::post('schedule-email-settings/massive-delete', [ScheduleEmailController::class, 'massiveDelete'])->name('schedule-email-settings.massive-delete');
Route::post('schedule-email-settings/massive-update', [ScheduleEmailController::class, 'massiveUpdate'])->name('schedule-email-settings.massive-update');


//--------------- rutas configuracon de modbus ----------------------
Route::resource('/modbus-configuration', ModbusConfigurationController::class);


//// Ruta para ver reporte en navegador para desarrollo
// Route::get('/pdf-example', function () {
//     return inertia('Home/ExamplePdf');
// })->name('pdf.example');

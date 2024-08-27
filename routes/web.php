<?php

use App\Http\Controllers\MachineVariableController;
use App\Http\Controllers\RobagDataController;
use App\Models\RobagData;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // $today_data = RobagData::whereDate('created_at', today())->get();

    return Inertia::render('Home', [
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


//--------------- robag data routes ------------------
Route::post('robag-get-data-by-date-range', [RobagDataController::class, 'getDataByDateRange'])->name('robag.get-data-by-date-range');
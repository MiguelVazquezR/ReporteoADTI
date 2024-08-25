<?php

use App\Http\Controllers\MachineVariableController;
use App\Http\Controllers\RobagDataController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
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

// ------- Robag rutas --------
Route::resource('Robag', RobagDataController::class);


// ------- maquinas y sus variables rutas --------
Route::resource('machine-variables', MachineVariableController::class);
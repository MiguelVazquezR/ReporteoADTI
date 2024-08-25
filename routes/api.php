<?php

use App\Http\Controllers\RobagDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// robag api
Route::post('robag/store', [RobagDataController::class, 'store']);
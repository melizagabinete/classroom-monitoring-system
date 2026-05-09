<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [SensorController::class, 'dashboard']);

Route::get('/api/sensors', [SensorController::class, 'latest']);
Route::get('/api/history', [SensorController::class, 'history']);

Route::get('/download', [SensorController::class, 'download']);


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {


    Route::prefix('appointments')->group(function () {
        Route::post('/', [AppointmentController::class, 'store']);
    });


    Route::prefix('reports')->group(function () {
        Route::get('/internal', [AppointmentController::class, 'getInternalReports']);
    });




});


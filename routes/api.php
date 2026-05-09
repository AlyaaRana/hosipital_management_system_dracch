<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\FileController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Role Admin
        Route::middleware('role:admin')->group(function () {
            Route::get('/patients', [PatientController::class, 'index']);
            Route::get('/reports/export', [AppointmentController::class, 'exportReports']);
        });

        // Role Patient
        Route::middleware('role:patient')->group(function () {
            Route::post('/appointments', [AppointmentController::class, 'store']);
        });

        // Role Doctor
        Route::middleware('role:doctor')->group(function () {
            Route::post('/medical-records', [MedicalRecordController::class, 'store']);
        });

        // Shared Routes
        Route::get('/patients/{id}', [PatientController::class, 'show']);
        Route::put('/patients/{id}', [PatientController::class, 'update']);
        Route::get('/doctors', [DoctorController::class, 'index']);

        Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
        Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
        Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);

        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show']);

        // File Routes
        Route::post('/files/upload', [FileController::class, 'upload']);
        Route::get('/files/{id}', [FileController::class, 'show']);
        Route::delete('/files/{id}', [FileController::class, 'destroy']);
    });
});

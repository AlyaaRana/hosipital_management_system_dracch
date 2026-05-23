<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MedicalRecordExportController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::get('/patients', [PatientController::class, 'index']);
            Route::post('/patients', [PatientController::class, 'store']);
            Route::delete('/patients/{id}', [PatientController::class, 'destroy']);
            Route::post('/doctors', [DoctorController::class, 'store']);
            Route::put('/doctors/{id}', [DoctorController::class, 'update']);
            Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);
            Route::get('/reports/export', [AppointmentController::class, 'exportReports']);
            Route::get('/medical-records/export/pdf', [MedicalRecordExportController::class, 'exportPdf']);
            Route::get('/medical-records/export/csv', [MedicalRecordExportController::class, 'exportCsv']);
        });

        Route::middleware('role:patient')->group(function () {
            Route::post('/appointments', [AppointmentController::class, 'store']);
        });

        Route::middleware('role:doctor')->group(function () {
            Route::post('/medical-records', [MedicalRecordController::class, 'store']);
        });

        Route::middleware('role:admin,doctor')->group(function () {
            Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
        });

        Route::get('/patients/{id}', [PatientController::class, 'show']);
        Route::put('/patients/{id}', [PatientController::class, 'update']);

        Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
        Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);

        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show']);

        Route::get('/files/{id}', [FileController::class, 'show']);
        Route::delete('/files/{id}', [FileController::class, 'destroy']);
    });
});

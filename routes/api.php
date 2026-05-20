<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MedicalRecordExportController;

// Endpoint untuk upload
Route::post('/upload-document', [FileController::class, 'upload'])->middleware('auth');

// Endpoint untuk melihat/download file private
Route::get('/files/{id}', [FileController::class, 'show'])->middleware('auth');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
            Route::apiResource('patients', PatientController::class);
            Route::apiResource('doctors', DoctorController::class);
            
            Route::get('medical-records/export/pdf', [MedicalRecordExportController::class, 'exportPdf']);
            Route::get('medical-records/export/csv', [MedicalRecordExportController::class, 'exportCsv']);
        });
    });
});

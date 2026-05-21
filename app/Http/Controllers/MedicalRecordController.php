<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'diagnosis' => 'required|string',
            'prescription' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $doctor = Auth::user()->doctor;

        if (!$doctor || $doctor->id !== $appointment->doctor_id) {
            return response()->json(['message' => 'Access Denied: Anda tidak berwenang untuk rekam medis ini.'], 403);
        }

        $medicalRecord = MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'diagnosis' => $request->diagnosis,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Rekam medis berhasil disimpan',
            'data' => $medicalRecord
        ], 201);
    }

    public function show($id)
    {
        $medicalRecord = MedicalRecord::with(['appointment.patient.user', 'appointment.doctor.user'])->findOrFail($id);
        $user = Auth::user();
        $appointment = $medicalRecord->appointment;

        $isPatient = $user->role === 'patient' && $user->patient?->id === $appointment->patient_id;
        $isDoctor = $user->role === 'doctor' && $user->doctor?->id === $medicalRecord->doctor_id;

        if ($user->role !== 'admin' && !$isPatient && !$isDoctor) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return response()->json([
            'message' => 'Detail rekam medis ditemukan',
            'data' => $medicalRecord
        ], 200);
    }
}

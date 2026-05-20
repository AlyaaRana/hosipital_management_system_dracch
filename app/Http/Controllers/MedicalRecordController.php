<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

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
        $medicalRecord = MedicalRecord::with('appointment')->findOrFail($id);

        return response()->json([
            'message' => 'Detail rekam medis ditemukan',
            'data' => $medicalRecord
        ], 200);
    }
}

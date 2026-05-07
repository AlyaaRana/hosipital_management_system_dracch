<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'schedule_id' => 'required|exists:schedules,id',
            'appointment_date' => 'required|date|after_or_equal:today',
        ]);

        return DB::transaction(function () use ($request) {
            $schedule = Schedule::lockForUpdate()->findOrFail($request->schedule_id);

            if ($schedule->available_slots <= 0) {
                return response()->json(['message' => 'Maaf, kuota jadwal dokter ini sudah penuh.'], 422);
            }

            $isBooked = Appointment::where('patient_id', Auth::id())
                ->where('appointment_date', $request->appointment_date)
                ->where('schedule_id', $request->schedule_id)
                ->exists();

            if ($isBooked) {
                return response()->json(['message' => 'Kamu sudah memiliki janji temu di jadwal ini.'], 422);
            }

            $appointment = Appointment::create([
                'patient_id' => Auth::id(),
                'doctor_id' => $request->doctor_id,
                'schedule_id' => $request->schedule_id,
                'appointment_date' => $request->appointment_date,
                'status' => 'pending'
            ]);

            $schedule->decrement('available_slots');

            return response()->json([
                'message' => 'Janji temu berhasil dibuat!',
                'data' => $appointment
            ], 201);
        });
    }

    public function getInternalReports()
    {
        $report1 = DB::select("
            SELECT u.name as patient_name, a.appointment_date, d.specialization
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            JOIN doctors d ON a.doctor_id = d.id
        ");

        $report2 = DB::select("
            SELECT u.name as patient, d.specialization, mr.diagnosis, mr.prescription, mr.notes
            FROM medical_records mr
            JOIN appointments a ON mr.appointment_id = a.id
            JOIN users u ON a.patient_id = u.id
            JOIN doctors d ON a.doctor_id = d.id
        ");

        $report3 = DB::select("
            SELECT f.filename, f.fileable_type, u.name AS uploader_name, mr.diagnosis
            FROM files f
            JOIN users u ON f.uploaded_by = u.id
            JOIN medical_records mr ON f.fileable_id = mr.id
            WHERE f.fileable_type = 'App\\\\Models\\\\MedicalRecord'
        ");

        return response()->json([
            'status' => 'success',
            'reports' => [
                'patient_doctor_list' => $report1,
                'medical_history' => $report2,
                'file_audit_report' => $report3
            ]
        ]);
    }
}

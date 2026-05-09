<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
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

    public function exportReports()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

        return response()->json([
            'status' => 'success',
            'reports' => [
                'patient_doctor_list' => $report1,
                'medical_history' => $report2
            ]
        ]);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($id);

        if (Auth::user()->role !== 'admin' && Auth::id() !== $appointment->patient_id) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json(['message' => 'Status janji temu diupdate', 'data' => $appointment]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->status !== 'pending') {
            return response()->json(['message' => 'Janji temu yang sudah diproses tidak bisa dibatalkan'], 422);
        }

        $appointment->delete();
        return response()->json(['message' => 'Janji temu berhasil dibatalkan']);
    }
}

<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentStatusChanged;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $schedule = Schedule::lockForUpdate()->findOrFail($request->schedule_id);
            $patient = Auth::user()->patient;

            if (!$patient) {
                return response()->json([
                    'message' => 'Data pasien tidak ditemukan untuk pengguna ini.'
                ], 403);
            }

            if ($schedule->available_slots <= 0) {
                return response()->json(['message' => 'Maaf, kuota jadwal dokter ini sudah penuh.'], 422);
            }

            $isBooked = Appointment::where('patient_id', $patient->id)
                ->where('appointment_date', $request->appointment_date)
                ->where('schedule_id', $request->schedule_id)
                ->exists();

            if ($isBooked) {
                return response()->json(['message' => 'Kamu sudah memiliki janji temu di jadwal ini.'], 422);
            }

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id,
                'schedule_id' => $request->schedule_id,
                'appointment_date' => $request->appointment_date,
                'status' => 'pending',
                'complaint' => $request->complaint,
            ]);

            $schedule->decrement('available_slots');

            if ($appointment->patient?->user?->email) {
                Mail::to($appointment->patient->user->email)
                    ->send(new AppointmentConfirmation($appointment));
            }

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
            LEFT JOIN patients p ON a.patient_id = p.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN doctors d ON a.doctor_id = d.id
        ");

        $report2 = DB::select("
            SELECT u.name as patient, d.specialization, mr.diagnosis, mr.prescription, mr.notes
            FROM medical_records mr
            JOIN appointments a ON mr.appointment_id = a.id
            JOIN patients p ON a.patient_id = p.id
            JOIN users u ON p.user_id = u.id
            JOIN doctors d ON a.doctor_id = d.id
        ");

        $report3 = DB::select("
            SELECT d.id as doctor_id, u.name as doctor_name, s.day_of_week, s.start_time, s.end_time, s.available_slots
            FROM schedules s
            RIGHT JOIN doctors d ON s.doctor_id = d.id
            JOIN users u ON d.user_id = u.id
        ");

        return response()->json([
            'status' => 'success',
            'reports' => [
                'patient_doctor_list' => $report1,
                'medical_history' => $report2,
                'doctor_schedule_right_join' => $report3,
            ]
        ]);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['doctor.user', 'patient.user'])->findOrFail($id);
        $user = Auth::user();

        $isOwner = $user->role === 'patient' && $user->patient?->id === $appointment->patient_id;
        $isDoctor = $user->role === 'doctor' && $user->doctor?->id === $appointment->doctor_id;

        if ($user->role !== 'admin' && !$isOwner && !$isDoctor) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        return response()->json(['status' => 'success', 'data' => $appointment]);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'doctor' && $user->doctor?->id !== $appointment->doctor_id) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $appointment->status;
        $appointment->update(['status' => $request->status]);

        if ($oldStatus !== $appointment->status && $appointment->patient?->user?->email) {
            Mail::to($appointment->patient->user->email)
                ->send(new AppointmentStatusChanged($appointment));
        }

        return response()->json(['message' => 'Status janji temu diupdate', 'data' => $appointment]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = Auth::user();
        $isOwner = $user->role === 'patient' && $user->patient?->id === $appointment->patient_id;

        if ($user->role !== 'admin' && !$isOwner) {
            return response()->json(['message' => 'Access Denied'], 403);
        }

        if ($appointment->status !== 'pending') {
            return response()->json(['message' => 'Janji temu yang sudah diproses tidak bisa dibatalkan'], 422);
        }

        $appointment->delete();
        return response()->json(['message' => 'Janji temu berhasil dibatalkan']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User; // Atau model Patient Anda
use App\Notifications\AppointmentConfirmed;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
   
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'patient_id'       => 'required|exists:users,id',
            'doctor_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'notes'            => 'nullable|string',
        ]);

     
        $appointment = Appointment::create($validated);

      
        $patient = User::find($request->patient_id); 

       
        $patient->notify(new AppointmentConfirmed($appointment));

        return response()->json([
            'message' => 'Janji temu berhasil dibuat dan email konfirmasi telah dikirim.',
            'data'    => $appointment
        ], 201);
    }
}

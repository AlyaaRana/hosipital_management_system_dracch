<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'doctor' && $user->doctor && $user->doctor->id === $appointment->doctor_id) {
            return true;
        }

        if ($user->role === 'patient' && $user->patient && $user->patient->id === $appointment->patient_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'patient';
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'doctor' && $user->doctor && $user->doctor->id === $appointment->doctor_id) return true;

        if ($user->role === 'patient' && $user->patient && $user->patient->id === $appointment->patient_id) return true;
        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'patient' && $user->patient && $user->patient->id === $appointment->patient_id) return true;
        return false;
    }
}


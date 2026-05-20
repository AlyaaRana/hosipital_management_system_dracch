<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'doctor' && $user->doctor && $user->doctor->id === $medicalRecord->doctor_id) return true;


        if ($user->role === 'patient' && $user->patient && $medicalRecord->appointment && $medicalRecord->appointment->patient_id === $user->patient->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'doctor';
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->role === 'doctor' && $user->doctor && $user->doctor->id === $medicalRecord->doctor_id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->role === 'admin';
    }
}


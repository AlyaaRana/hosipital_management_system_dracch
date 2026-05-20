<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    public function view(User $user, File $file): bool
    {
        if ($user->role === 'admin') return true;


        if ($file->uploaded_by === $user->id) return true;


        if ($file->fileable_type === 'App\\Models\\MedicalRecord' && $file->fileable) {
            $mr = $file->fileable;
            if ($user->role === 'doctor' && $user->doctor && $user->doctor->id === $mr->doctor_id) return true;
            if ($user->role === 'patient' && $user->patient && $mr->appointment && $mr->appointment->patient_id === $user->patient->id) return true;
        }


        if ($file->fileable_type === 'App\\Models\\User' && $file->fileable) {
            if ($user->id === $file->fileable->id) return true;
        }

        return false;
    }

    public function delete(User $user, File $file): bool
    {
        if ($user->role === 'admin') return true;
        return $file->uploaded_by === $user->id;
    }
}


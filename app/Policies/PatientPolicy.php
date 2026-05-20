<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function view(User $user, Patient $patient): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'patient' && $user->patient && $user->patient->id === $patient->id) return true;
        return false;
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'patient' && $user->patient && $user->patient->id === $patient->id) return true;
        return false;
    }

    public function delete(User $user, Patient $patient): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'patient' && $user->patient && $user->patient->id === $patient->id) return true;
        return false;
    }
}

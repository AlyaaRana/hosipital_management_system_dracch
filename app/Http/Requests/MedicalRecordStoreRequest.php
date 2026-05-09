<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['doctor', 'admin']);
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'notes' => 'required|string',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|string',
        ];
    }
}

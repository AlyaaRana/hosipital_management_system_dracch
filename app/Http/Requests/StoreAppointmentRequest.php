<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $doctor_id
 * @property int $schedule_id
 * @property string $appointment_date
 */
class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'        => 'required|exists:doctors,id',
            'schedule_id'      => 'required|exists:schedules,id',
            'appointment_date' => 'required|date|after_or_equal:today',
        ];
    }
}

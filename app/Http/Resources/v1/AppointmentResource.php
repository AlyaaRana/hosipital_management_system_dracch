<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pasien' => $this->patient->user->name, // Nama pasien dari relasi user
            'dokter' => $this->doctor->user->name,   // Nama dokter dari relasi user
            'tanggal_janji' => $this->appointment_date,
            'status' => $this->status,
            'keluhan' => $this->complaint,
        ];
    }
}

<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'nama_pasien' => $this->user->name, // Relasi ke tabel user
            'nik' => $this->nik,
            'jenis_kelamin' => $this->gender,
            'tanggal_lahir' => $this->birth_date,
        ];
    }
}

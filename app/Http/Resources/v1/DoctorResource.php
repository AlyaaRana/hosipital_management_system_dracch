<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'nama_dokter' => $this->user->name ?? 'N/A',
            'spesialis' => $this->specialization,
            'kontak' => $this->phone,
            'foto' => $this->photo,
        ];
    }
}

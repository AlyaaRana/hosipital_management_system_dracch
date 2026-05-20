<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini biar aman

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', // Pastikan kolom foreign key ini terdaftar
        'diagnosis',
        'treatment'
    ];

    /**
     * Relasi ke model Appointment
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
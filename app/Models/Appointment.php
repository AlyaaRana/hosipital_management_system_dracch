<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    
    public function patient() {
        return $this->belongsTo(Patient::class);
    }
    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }
    public function medicalRecord() {
        return $this->hasOne(MedicalRecord::class);
    }
}

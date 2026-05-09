<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany; 

class MedicalRecord extends Model
{

    protected $fillable = [
        'patient_id',
        'diagnosis',
        'treatment'
    ];

   
    public function files(): MorphMany
    {
        // Pastikan model File berada di namespace App\Models
        return $this->morphMany(File::class, 'fileable');
    }
} 

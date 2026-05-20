<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalRecord; 
use App\Models\File;          

class MedicalRecordController extends Controller
{
    public function store(Request $request)
    {
       
        $record = MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'diagnosis'  => $request->diagnosis,
            'treatment'  => $request->treatment,
        ]);

        
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
             
                $path = $file->store('private/medical-records');

                $record->files()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'uploaded_by'   => auth()->id(),
                ]);
            }
        }

        return response()->json(['message' => 'Rekam medis berhasil disimpan'], 201);
    }
} 

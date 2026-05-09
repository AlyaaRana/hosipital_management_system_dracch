<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
public function store(Request $request) {
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'diagnosis' => 'required|string',
        'prescription' => 'required|string',
    ]);
    return response()->json(['message' => 'Rekam medis berhasil disimpan'], 201);
}

public function show($id) {
    return response()->json(['message' => 'Detail rekam medis ditemukan']);
}
}

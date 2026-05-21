<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\File;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
            'type' => 'required|string'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User tidak terautentikasi'], 401);
        }

        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'File tidak ditemukan'], 400);
        }

        $file = $request->file('file');
        $path = Storage::putFile('private/medical-files', $file);

        $fileable = $user->patient ?? $user->doctor ?? $user;

        $fileData = $fileable->files()->create([
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Upload berhasil!',
            'data' => $fileData
        ], 201);
    }

    public function show($id)
    {
        $fileRecord = File::findOrFail($id);
        $user = Auth::user();

        if (!$this->canAccessFile($user, $fileRecord)) {
            return response()->json(['message' => 'Unauthorized. Anda tidak memiliki akses ke file ini.'], 403);
        }

        if (!Storage::exists($fileRecord->path)) {
            return response()->json(['message' => 'File fisik tidak ditemukan di server'], 404);
        }

        return Storage::download($fileRecord->path, $fileRecord->filename);
    }

    public function destroy($id)
    {
        $fileRecord = File::findOrFail($id);
        $user = Auth::user();

        if (!$this->canAccessFile($user, $fileRecord) && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Anda tidak memiliki akses untuk menghapus file ini.'], 403);
        }

        $fileRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'File berhasil dihapus'], 200);
    }

    private function canAccessFile($user, File $fileRecord): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        $fileable = $fileRecord->fileable;

        if ($fileable instanceof User && $user->id === $fileable->id) {
            return true;
        }

        if ($fileable instanceof Patient && $user->patient?->id === $fileable->id) {
            return true;
        }

        if ($fileable instanceof Doctor && $user->doctor?->id === $fileable->id) {
            return true;
        }

        if ($fileable instanceof MedicalRecord) {
            $appointment = $fileable->appointment;
            return ($user->role === 'patient' && $user->patient?->id === $appointment->patient_id)
                || ($user->role === 'doctor' && $user->doctor?->id === $appointment->doctor_id);
        }

        return false;
    }
}

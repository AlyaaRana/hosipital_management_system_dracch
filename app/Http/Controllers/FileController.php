<?php

namespace App\Http\Controllers;

use App\Models\File;
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
        $path = $file->store('private/documents');

        $fileData = $user->files()->create([
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $fileRecord->fileable_id && $user->role !== 'admin') {
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

        if ($user->id !== $fileRecord->fileable_id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Anda tidak memiliki akses untuk menghapus file ini.'], 403);
        }

        $fileRecord->delete();

        return response()->json(['status' => 'success', 'message' => 'File berhasil dihapus'], 200);
    }
}

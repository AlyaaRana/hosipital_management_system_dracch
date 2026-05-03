public function upload(Request $request)
{
    // 1. Validasi (Maks 5MB & Mime-type)
    $request->validate([
        'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:5120', // 5120KB = 5MB
        'type' => 'required|string' // misal: 'profile' atau 'medical_doc'
    ]);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        
        // 2. Simpan ke folder private (storage/app/private/...)
        $path = $file->store('private/documents');

        // 3. Simpan data ke tabel polimorfik
        // Contoh untuk user yang sedang login
        $user = auth()->user();
        $user->files()->create([
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json(['message' => 'Upload berhasil!'], 201);
    }
}

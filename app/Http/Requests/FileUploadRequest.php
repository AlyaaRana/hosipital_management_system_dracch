<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
            'type' => 'required|string|in:profile,medical_doc,other',
            'fileable_id' => 'nullable|integer',
            'fileable_type' => 'nullable|string',
        ];
    }
}

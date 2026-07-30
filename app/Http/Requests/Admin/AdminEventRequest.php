<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_title' => 'required|min:3',
            'event_date' => 'required',
            'event_description' => 'nullable|string',
            'event_image' => 'nullable|image|file|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'event_title.required' => 'Nama event wajib diisi',
            'event_title.min' => 'Nama event minimal 3 karakter',
            'event_date.required' => 'Tanggal wajib diisi',
            'event_description.string' => 'Deskripsi harus berupa teks',
            'event_image.image' => 'File harus berupa gambar',
            'event_image.file' => 'File harus berupa file',
            'event_image.max' => 'Gambar maksimal 2 Mb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}

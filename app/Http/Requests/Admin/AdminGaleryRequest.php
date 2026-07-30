<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use RealRashid\SweetAlert\Facades\Alert;

class AdminGaleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image_title' => 'required|min:3',
            'galery_image' => 'nullable|image|file|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'image_title.required' => 'Judul galeri wajib diisi',
            'image_title.min' => 'Judul galeri minimal 3 karakter',
            'galery_image.image' => 'Foto wajib berjenis image',
            'galery_image.file' => 'Foto wajib berupa file',
            'galery_image.max' => 'Foto maksimal berukuran 2 Mb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $allErrors = implode('<br>', $validator->errors()->all());

        Alert::html('Validasi Gagal!', $allErrors, 'error');

        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}

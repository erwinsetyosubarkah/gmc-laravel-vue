<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use RealRashid\SweetAlert\Facades\Alert;

class AdminMyclientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|min:3',
            'company_name' => 'required|min:3',
            'client_address' => 'required',
            'client_image' => 'nullable|image|file|max:2048',
            'old_client_image' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Nama klien wajib diisi',
            'client_name.min' => 'Nama klien minimal 3 karakter',
            'company_name.required' => 'Nama perusahaan wajib diisi',
            'company_name.min' => 'Nama perusahaan minimal 3 karakter',
            'client_address.required' => 'Alamat wajib diisi',
            'client_image.image' => 'Foto wajib berjenis image',
            'client_image.file' => 'Foto wajib berupa file',
            'client_image.max' => 'Foto maksimal berukuran 2 Mb',
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

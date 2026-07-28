<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use RealRashid\SweetAlert\Facades\Alert;

class AdminProfileEditRequest extends FormRequest
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
            'id'    => 'required',
            'club_name' => 'required|min:5',
            'club_name_abbreviation' => 'required',
            'club_logo' => 'nullable|image|file|max:2048',
            'email' => 'nullable|email',
            'leader_name' => 'nullable|string',
            'leader_email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'short_description' => 'nullable|max:100',
            'old_club_logo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID wajib diisi',
            'club_name.required' => 'Nama club wajib diisi',
            'club_name.min' => 'Nama club minimal 5 karakter',
            'club_name_abbreviation.required' => 'Singkatan nama club wajib diisi',
            'club_logo.image' => 'Logo wajib berjenis image',
            'club_logo.file' => 'Logo wajib berupa file',
            'club_logo.max' => 'Logo maksimal berukuran 2 Mb',
            'email.email' => 'Format email tidak valid',
            'leader_email.email' => 'Format email ketua tidak valid',
            'short_description.max' => 'Deskripsi singkat maksimal 100 karakter',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // 1. Ambil semua error dan gabungkan kalimatnya
        $allErrors = implode('<br>', $validator->errors()->all());

        // 2. Set notifikasi SweetAlert ke Session
        Alert::html('Validasi Gagal!', $allErrors, 'error');

        // 3. Lanjutkan redirect back bawaan Laravel dengan membawa error instansi
        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }

}

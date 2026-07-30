<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user?->id;

        return [
            'name' => 'required|max:255',
            'username' => ['required', 'min:3', 'max:255', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'email:dns', Rule::unique('users')->ignore($userId)],
            'level' => 'required',
            'password' => $user ? 'nullable|min:5|max:255' : 'nullable|min:5|max:255',
            'password2' => $user ? 'nullable|min:5|max:255|same:password' : 'nullable|min:5|max:255|same:password',
            'photo' => 'nullable|image|file|max:2048',
            'old_photo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'username.required' => 'Username wajib diisi',
            'username.min' => 'Username minimal 3 karakter',
            'username.max' => 'Username maksimal 255 karakter',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'level.required' => 'Level wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 5 karakter',
            'password.max' => 'Password maksimal 255 karakter',
            'password2.required' => 'Konfirmasi password wajib diisi',
            'password2.same' => 'Password dan konfirmasi harus sama',
            'photo.image' => 'Foto wajib berjenis image',
            'photo.file' => 'Foto wajib berupa file',
            'photo.max' => 'Foto maksimal berukuran 2 Mb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $allErrors = implode('<br>', $validator->errors()->all());

        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 'error',
                    'title' => 'Validasi Gagal',
                    'message' => $allErrors,
                ], 422)
            );
        }

        Alert::html('Validasi Gagal!', $allErrors, 'error');

        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }
}

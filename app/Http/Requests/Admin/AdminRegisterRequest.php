<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use RealRashid\SweetAlert\Facades\Alert;

class AdminRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'username' => 'required|min:3|max:255|unique:users',
            'email' => 'required|email:dns|unique:users',
            'level' => 'required',
            'password' => 'required|min:5|max:255',
            'password2' => 'required|min:5|max:255|same:password',
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

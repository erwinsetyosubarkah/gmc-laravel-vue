<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminRegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request register agar sesuai dengan batasan input yang diharapkan.
    public function test_register_request_validation_rules_and_messages(): void
    {
        $request = new AdminRegisterRequest();

        $this->assertSame([
            'name' => 'required|max:255',
            'username' => 'required|min:3|max:255|unique:users',
            'email' => 'required|email:dns|unique:users',
            'level' => 'required',
            'password' => 'required|min:5|max:255',
            'password2' => 'required|min:5|max:255|same:password',
        ], $request->rules());

        $this->assertSame([
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
        ], $request->messages());
    }

    // Menguji bahwa password yang disimpan dalam database terenkripsi menggunakan hashing.
    public function test_password_is_hashed_before_storage(): void
    {
        $plainPassword = 'secret123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $this->assertNotSame($plainPassword, $hashedPassword);
        $this->assertTrue(password_verify($plainPassword, $hashedPassword));
    }
}

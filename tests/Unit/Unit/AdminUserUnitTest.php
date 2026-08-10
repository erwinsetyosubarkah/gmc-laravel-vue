<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request user agar sesuai dengan batasan input yang diharapkan.
    public function test_user_request_validation_rules_and_messages(): void
    {
        $request = new AdminUserRequest();

        $rules = $request->rules();

        $this->assertSame('required|max:255', $rules['name']);
        $this->assertSame('required', $rules['level']);
        $this->assertSame('nullable|min:5|max:255', $rules['password']);
        $this->assertSame('nullable|min:5|max:255|same:password', $rules['password2']);
        $this->assertSame('nullable|image|file|max:2048', $rules['photo']);
        $this->assertSame('nullable|string', $rules['old_photo']);

        $this->assertIsArray($rules['username']);
        $this->assertContains('required', $rules['username']);
        $this->assertContains('min:3', $rules['username']);
        $this->assertContains('max:255', $rules['username']);
        $this->assertTrue(collect($rules['username'])->contains(fn ($rule) => $rule instanceof \Illuminate\Validation\Rules\Unique));

        $this->assertIsArray($rules['email']);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email:dns', $rules['email']);
        $this->assertTrue(collect($rules['email'])->contains(fn ($rule) => $rule instanceof \Illuminate\Validation\Rules\Unique));

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
            'photo.image' => 'Foto wajib berjenis image',
            'photo.file' => 'Foto wajib berupa file',
            'photo.max' => 'Foto maksimal berukuran 2 Mb',
        ], $request->messages());
    }

    // Menguji bahwa request user mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_user_request_is_authorized(): void
    {
        $request = new AdminUserRequest();

        $this->assertTrue($request->authorize());
    }
}

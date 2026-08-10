<?php

namespace Tests\Unit;

use App\Http\Requests\Admin\AdminAuthRequest;
use Tests\TestCase;

class AdminAuthRequestTest extends TestCase
{
    // Menguji aturan validasi request login untuk username dan password.
    public function test_login_request_validation_rules(): void
    {
        $request = new AdminAuthRequest();

        $this->assertSame([
            'username' => 'required|min:5',
            'password' => 'required|min:5',
        ], $request->rules());

        $this->assertSame([
            'username.required' => 'Username wajib diisi',
            'username.min' => 'Username minimal 5 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 5 karakter',
        ], $request->messages());
    }
}

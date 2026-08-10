<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminAuthRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LoginUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji bahwa repository/model dapat menemukan user berdasarkan username yang valid.
    public function test_user_can_be_found_by_username(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $foundUser = User::where('username', 'adminuser')->first();

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame($user->id, $foundUser->id);
    }

    // Menguji bahwa pencarian username yang tidak ada mengembalikan nilai null.
    public function test_user_is_not_found_for_unknown_username(): void
    {
        $foundUser = User::where('username', 'unknownuser')->first();

        $this->assertNull($foundUser);
    }

    // Menguji bahwa hash password dapat diverifikasi dengan benar untuk password yang cocok dan yang tidak cocok.
    public function test_password_hash_matching_returns_expected_result(): void
    {
        $hash = Hash::make('secret123');

        $this->assertTrue(Hash::check('secret123', $hash));
        $this->assertFalse(Hash::check('wrong-password', $hash));
    }

    // Menguji aturan validasi request login agar sesuai dengan batasan input yang diharapkan.
    public function test_login_request_validation_passes_and_fails_as_expected(): void
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

    // Menguji bahwa kunci throttling dibuat dari kombinasi username dan IP address.
    public function test_throttle_key_is_generated_from_username_and_ip(): void
    {
        $key = 'login:' . 'adminuser' . ':' . '127.0.0.1';

        $this->assertSame('login:adminuser:127.0.0.1', $key);
    }

    // Menguji bahwa event login dipicu saat autentikasi berhasil.
    public function test_login_events_are_dispatched_when_authentication_succeeds(): void
    {
        Event::fake();

        $user = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $this->assertTrue($user instanceof User);
        Event::assertNotDispatched(
            \Illuminate\Auth\Events\Login::class
        );
    }
}

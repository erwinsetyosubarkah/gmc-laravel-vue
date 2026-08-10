<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario login berhasil saat username dan password valid.
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->post('/auth/login', [
            'username' => 'adminuser',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => 'Login Berhasil !',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    // Menguji skenario login gagal saat password salah.
    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->post('/auth/login', [
            'username' => 'adminuser',
            'password' => 'wrong-password',
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
            'message' => 'Login Gagal !',
        ]);

        $this->assertGuest();
    }

    // Menguji skenario login gagal saat username belum terdaftar.
    public function test_user_cannot_login_with_unregistered_username(): void
    {
        $response = $this->post('/auth/login', [
            'username' => 'unknownuser',
            'password' => 'randompass123',
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'error',
            'message' => 'Login Gagal !',
        ]);

        $this->assertGuest();
    }

    // Menguji validasi bahwa username wajib diisi saat login.
    public function test_username_is_required_for_login(): void
    {
        $response = $this->from('/auth/login')->post('/auth/login', [
            'username' => '',
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    // Menguji validasi bahwa password wajib diisi saat login.
    public function test_password_is_required_for_login(): void
    {
        $response = $this->from('/auth/login')->post('/auth/login', [
            'username' => 'adminuser',
            'password' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    // Menguji validasi bahwa semua field wajib diisi saat login.
    public function test_all_fields_are_required_for_login(): void
    {
        $response = $this->from('/auth/login')->post('/auth/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    // Menguji alur sesi bahwa user yang sudah login tidak boleh melihat halaman login lagi.
    public function test_authenticated_user_is_redirected_back_to_login_page(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $this->actingAs($user)->get('/auth/login')
            ->assertRedirect('/');
    }

    // Menguji edge case bahwa login tetap berhasil meskipun username menggunakan huruf besar/kecil berbeda.
    public function test_login_is_case_insensitive_for_username(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'username' => 'AdminUser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->post('/auth/login', [
            'username' => 'ADMINUSER',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    // Menguji edge case bahwa spasi di depan atau belakang username akan dihapus sebelum proses login.
    public function test_login_trims_extra_whitespace_from_username(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->post('/auth/login', [
            'username' => ' adminuser ',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }
}

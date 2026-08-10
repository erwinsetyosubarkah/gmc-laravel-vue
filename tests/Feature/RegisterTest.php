<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario pendaftaran berhasil saat semua data valid.
    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/auth/register', [
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pendaftaran Berhasil !',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'adminuser',
            'email' => 'admin@gmail.com',
        ]);

        $user = User::where('username', 'adminuser')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    // Menguji skenario pendaftaran gagal saat username sudah digunakan.
    public function test_user_cannot_register_with_duplicate_username(): void
    {
        User::create([
            'name' => 'Existing User',
            'username' => 'adminuser',
            'email' => 'existing@gmail.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->from('/auth/register')->post('/auth/register', [
            'name' => 'New User',
            'username' => 'adminuser',
            'email' => 'new@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username']);
    }

    // Menguji validasi bahwa password dan konfirmasi password harus sama.
    public function test_user_cannot_register_when_password_confirmation_does_not_match(): void
    {
        $response = $this->from('/auth/register')->post('/auth/register', [
            'name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'different123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password2']);
    }

    // Menguji validasi bahwa field wajib diisi saat pendaftaran.
    public function test_required_fields_are_needed_for_registration(): void
    {
        $response = $this->from('/auth/register')->post('/auth/register', [
            'name' => '',
            'username' => '',
            'email' => '',
            'level' => '',
            'password' => '',
            'password2' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'username', 'email', 'level', 'password', 'password2']);
    }

    // Menguji skenario pendaftaran gagal saat email sudah digunakan.
    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'username' => 'existinguser',
            'email' => 'existing@gmail.com',
            'password' => Hash::make('secret123'),
            'level' => 'admin',
        ]);

        $response = $this->from('/auth/register')->post('/auth/register', [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'existing@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }
}

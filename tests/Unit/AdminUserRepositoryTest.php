<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Admin\AdminUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_user_with_hashed_password(): void
    {
        $repository = new AdminUserRepository();
        $request = new Request();

        $result = $repository->store([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
        ], $request);

        $this->assertSame('success', $result['status']);
        $this->assertDatabaseHas('users', ['username' => 'testuser']);

        $user = User::where('username', 'testuser')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_it_uses_default_password_when_not_provided(): void
    {
        $repository = new AdminUserRepository();
        $request = new Request();

        $result = $repository->store([
            'name' => 'Default User',
            'username' => 'defaultuser',
            'email' => 'default@example.com',
            'level' => 'author',
        ], $request);

        $this->assertSame('success', $result['status']);

        $user = User::where('username', 'defaultuser')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('12345678', $user->password));
    }
}

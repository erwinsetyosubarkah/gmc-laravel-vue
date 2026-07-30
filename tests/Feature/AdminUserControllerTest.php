<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminUserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_json_users_payload(): void
    {
        User::factory()->create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@example.com',
            'level' => 'admin',
        ]);

        $controller = new AdminUserController();
        $response = $controller->all();

        $this->assertEquals(200, $response->getStatusCode());
        $payload = $response->getData(true);
        $this->assertSame('success', $payload['status']);
        $this->assertIsArray($payload['data']);
        $this->assertCount(1, $payload['data']);
    }
}

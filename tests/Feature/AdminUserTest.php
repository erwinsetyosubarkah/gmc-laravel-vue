<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan pengguna baru dengan data yang valid.
    public function test_admin_can_store_user_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserstore1',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/user', [
            'name' => 'Pengguna Baru',
            'username' => 'newuser1',
            'email' => 'newuser1@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data user berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser1',
            'email' => 'newuser1@gmail.com',
        ]);
    }

    // Menguji skenario penambahan pengguna gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_user_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserstore2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/user')
            ->actingAs($admin)
            ->post('/admin/user', [
                'name' => '',
                'username' => '',
                'email' => '',
                'level' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'username', 'email', 'level']);
    }

    // Menguji skenario admin dapat mengunggah foto pengguna saat menambahkan data user.
    public function test_admin_can_upload_user_photo_when_storing_user(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserstore3',
            'level' => 'admin',
        ]);

        Storage::fake();
        $photo = UploadedFile::fake()->image('user-photo.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/user', [
            'name' => 'Pengguna Dengan Foto',
            'username' => 'userwithphoto',
            'email' => 'userwithphoto@gmail.com',
            'level' => 'admin',
            'password' => 'secret123',
            'password2' => 'secret123',
            'photo' => $photo,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data user berhasil ditambah.',
        ]);

        $user = User::where('username', 'userwithphoto')->first();
        $this->assertNotNull($user->photo);
        $this->assertStringContainsString('post-images/user', $user->photo);
    }

    // Menguji skenario admin dapat mengubah data pengguna yang sudah ada.
    public function test_admin_can_edit_existing_user_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuseredit1',
            'level' => 'admin',
        ]);

        $user = User::factory()->create([
            'username' => 'olduser1',
            'email' => 'olduser1@example.com',
            'level' => 'user',
        ]);

        $response = $this->actingAs($admin)->post("/admin/user-edit/{$user->id}", [
            'name' => 'Pengguna Diperbarui',
            'username' => 'updateduser1',
            'email' => 'updateduser1@gmail.com',
            'level' => 'admin',
            'password' => 'newsecret123',
            'password2' => 'newsecret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data user berhasil diubah.',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'updateduser1',
            'email' => 'updateduser1@gmail.com',
            'level' => 'admin',
        ]);
    }

    // Menguji skenario penambahan pengguna gagal saat username sudah digunakan.
    public function test_admin_cannot_store_user_with_duplicate_username(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserduplicate1',
            'level' => 'admin',
        ]);

        User::factory()->create([
            'username' => 'takenusername',
            'email' => 'taken@example.com',
            'level' => 'user',
        ]);

        $response = $this->from('/admin/user')
            ->actingAs($admin)
            ->post('/admin/user', [
                'name' => 'Pengguna Duplikat',
                'username' => 'takenusername',
                'email' => 'newduplicate@example.com',
                'level' => 'admin',
                'password' => 'secret123',
                'password2' => 'secret123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username']);
    }

    // Menguji skenario penambahan pengguna gagal saat email sudah digunakan.
    public function test_admin_cannot_store_user_with_duplicate_email(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserduplicate2',
            'level' => 'admin',
        ]);

        User::factory()->create([
            'username' => 'existinguser',
            'email' => 'takenemail@example.com',
            'level' => 'user',
        ]);

        $response = $this->from('/admin/user')
            ->actingAs($admin)
            ->post('/admin/user', [
                'name' => 'Pengguna Email Duplikat',
                'username' => 'newduplicateemail',
                'email' => 'takenemail@example.com',
                'level' => 'admin',
                'password' => 'secret123',
                'password2' => 'secret123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    // Menguji skenario admin dapat menghapus pengguna beserta file foto terkait.
    public function test_admin_can_delete_user_and_its_photo(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminuserdelete1',
            'level' => 'admin',
        ]);

        Storage::fake();
        $user = User::factory()->create([
            'username' => 'userdelete1',
            'email' => 'userdelete1@gmail.com',
            'level' => 'user',
            'photo' => 'post-images/user/example.png',
        ]);

        Storage::put($user->photo, 'dummy-content');

        $response = $this->actingAs($admin)->delete("/admin/user/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data user berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        Storage::assertMissing($user->photo);
    }
}

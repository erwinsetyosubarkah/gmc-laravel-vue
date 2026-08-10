<?php

namespace Tests\Feature;

use App\Models\Myclient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMyclientTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan klien dengan data yang valid.
    public function test_admin_can_store_myclient_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyclient1',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/myclient', [
            'client_name' => 'Klien Baru',
            'company_name' => 'Perusahaan Baru',
            'client_address' => 'Alamat klien baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data klien berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('myclients', [
            'client_name' => 'Klien Baru',
            'company_name' => 'Perusahaan Baru',
            'client_address' => 'Alamat klien baru',
        ]);
    }

    // Menguji skenario penambahan klien gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_myclient_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyclient2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/myclient')
            ->actingAs($admin)
            ->post('/admin/myclient', [
                'client_name' => '',
                'company_name' => '',
                'client_address' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['client_name', 'company_name', 'client_address']);
    }

    // Menguji skenario admin dapat mengunggah gambar klien saat menambahkan data klien.
    public function test_admin_can_upload_client_image_when_storing_myclient(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyclient3',
            'level' => 'admin',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('client-image.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/myclient', [
            'client_name' => 'Klien Dengan Gambar',
            'company_name' => 'Perusahaan Gambar',
            'client_address' => 'Alamat dengan gambar',
            'client_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data klien berhasil ditambah.',
        ]);

        $client = Myclient::latest()->first();
        $this->assertNotNull($client->client_image);
        $this->assertStringContainsString('post-images/client', $client->client_image);
    }

    // Menguji skenario admin dapat mengubah data klien yang sudah ada.
    public function test_admin_can_edit_existing_myclient_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyclient4',
            'level' => 'admin',
        ]);

        $client = Myclient::create([
            'client_name' => 'Klien Lama',
            'company_name' => 'Perusahaan Lama',
            'client_address' => 'Alamat lama',
        ]);

        $response = $this->actingAs($admin)->post("/admin/myclient-edit/{$client->id}", [
            'client_name' => 'Klien Diperbarui',
            'company_name' => 'Perusahaan Diperbarui',
            'client_address' => 'Alamat diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data klien berhasil diubah.',
        ]);

        $this->assertDatabaseHas('myclients', [
            'id' => $client->id,
            'client_name' => 'Klien Diperbarui',
            'company_name' => 'Perusahaan Diperbarui',
            'client_address' => 'Alamat diperbarui',
        ]);
    }

    // Menguji skenario admin dapat menghapus klien beserta file gambar terkait.
    public function test_admin_can_delete_myclient_and_its_image(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyclient5',
            'level' => 'admin',
        ]);

        Storage::fake();
        $client = Myclient::create([
            'client_name' => 'Klien Untuk Dihapus',
            'company_name' => 'Perusahaan Untuk Dihapus',
            'client_address' => 'Alamat akan dihapus',
            'client_image' => 'post-images/client/example.png',
        ]);

        Storage::put($client->client_image, 'dummy-content');

        $response = $this->actingAs($admin)->delete("/admin/myclient/{$client->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data klien berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('myclients', [
            'id' => $client->id,
        ]);

        Storage::assertMissing($client->client_image);
    }
}

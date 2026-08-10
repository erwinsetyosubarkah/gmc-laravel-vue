<?php

namespace Tests\Feature;

use App\Models\Galery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGaleryTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan galeri dengan data yang valid.
    public function test_admin_can_store_galery_with_valid_data(): void
    {
        $user = User::factory()->create([
            'username' => 'admingalery1',
            'level' => 'admin',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('galery-default.png', 200, 200);

        $response = $this->actingAs($user)->post('/admin/galery', [
            'image_title' => 'Foto Baru',
            'galery_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data galeri berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('galeries', [
            'image_title' => 'Foto Baru',
        ]);
    }

    // Menguji skenario penambahan galeri gagal saat field judul wajib tidak diisi.
    public function test_admin_cannot_store_galery_when_required_fields_are_missing(): void
    {
        $user = User::factory()->create([
            'username' => 'admingalery2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/galery')
            ->actingAs($user)
            ->post('/admin/galery', [
                'image_title' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['image_title']);
    }

    // Menguji skenario admin dapat mengunggah gambar galeri saat menambahkan data galeri.
    public function test_admin_can_upload_galery_image_when_storing_galery(): void
    {
        $user = User::factory()->create([
            'username' => 'admingalery3',
            'level' => 'admin',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('galery-image.png', 200, 200);

        $response = $this->actingAs($user)->post('/admin/galery', [
            'image_title' => 'Foto Dengan Gambar',
            'galery_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data galeri berhasil ditambah.',
        ]);

        $galery = Galery::latest()->first();
        $this->assertNotNull($galery->galery_image);
        $this->assertStringContainsString('post-images/galery', $galery->galery_image);
    }

    // Menguji skenario admin dapat mengubah data galeri yang sudah ada.
    public function test_admin_can_edit_existing_galery_with_valid_data(): void
    {
        $user = User::factory()->create([
            'username' => 'admingalery4',
            'level' => 'admin',
        ]);

        $galery = Galery::create([
            'image_title' => 'Foto Lama',
            'galery_image' => 'post-images/galery/old.png',
        ]);

        $response = $this->actingAs($user)->post("/admin/galery-edit/{$galery->id}", [
            'image_title' => 'Foto Diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data galeri berhasil diubah.',
        ]);

        $this->assertDatabaseHas('galeries', [
            'id' => $galery->id,
            'image_title' => 'Foto Diperbarui',
        ]);
    }

    // Menguji skenario admin dapat menghapus galeri beserta file gambar terkait.
    public function test_admin_can_delete_galery_and_its_image(): void
    {
        $user = User::factory()->create([
            'username' => 'admingalery5',
            'level' => 'admin',
        ]);

        Storage::fake();
        $galery = Galery::create([
            'image_title' => 'Foto Untuk Dihapus',
            'galery_image' => 'post-images/galery/example.png',
        ]);

        Storage::put($galery->galery_image, 'dummy-content');

        $response = $this->actingAs($user)->delete("/admin/galery/{$galery->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data galeri berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('galeries', [
            'id' => $galery->id,
        ]);

        Storage::assertMissing($galery->galery_image);
    }
}

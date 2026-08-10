<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visidanmisi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisidanmisiTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat memperbarui data visi dan misi dengan data yang valid.
    public function test_admin_can_update_visidanmisi_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminvisi1',
            'level' => 'admin',
        ]);

        $visidanmisi = Visidanmisi::create([
            'title' => 'Visi Lama',
            'content' => 'Misi lama',
        ]);

        $response = $this->actingAs($admin)->post('/admin/visidanmisi', [
            'title' => 'Visi Baru',
            'content' => 'Misi baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data visi dan misi berhasil diubah',
        ]);

        $this->assertDatabaseHas('visidanmisis', [
            'id' => $visidanmisi->id,
            'title' => 'Visi Baru',
            'content' => 'Misi baru',
        ]);
    }

    // Menguji skenario update visi dan misi gagal saat judul wajib diisi.
    public function test_admin_cannot_update_visidanmisi_when_required_title_is_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminvisi2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/visidanmisi')
            ->actingAs($admin)
            ->post('/admin/visidanmisi', [
                'title' => '',
                'content' => 'Misi baru',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['title']);
    }

    // Menguji skenario admin dapat membuat data visi dan misi saat belum ada record sebelumnya.
    public function test_admin_can_create_visidanmisi_when_no_record_exists(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminvisi3',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/visidanmisi', [
            'title' => 'Visi dan Misi Baru',
            'content' => 'Isi visi dan misi baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data visi dan misi berhasil diubah',
        ]);

        $this->assertDatabaseHas('visidanmisis', [
            'title' => 'Visi dan Misi Baru',
            'content' => 'Isi visi dan misi baru',
        ]);
    }
}

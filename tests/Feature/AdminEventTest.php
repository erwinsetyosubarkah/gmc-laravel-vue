<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminEventTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan event dengan data yang valid.
    public function test_admin_can_store_event_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminevent1',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/event', [
            'event_title' => 'Event Baru',
            'event_date' => '2026-08-10',
            'event_description' => 'Deskripsi event baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data event berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('events', [
            'event_title' => 'Event Baru',
            'event_description' => 'Deskripsi event baru',
        ]);
    }

    // Menguji skenario penambahan event gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_event_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminevent2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/event')
            ->actingAs($admin)
            ->post('/admin/event', [
                'event_title' => '',
                'event_date' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'status' => 'error',
            'message' => 'Validasi gagal.',
        ]);
        $response->assertJsonPath('errors.event_title.0', 'Nama event wajib diisi');
        $response->assertJsonPath('errors.event_date.0', 'Tanggal wajib diisi');
    }

    // Menguji skenario admin dapat mengunggah gambar event saat menambahkan data event.
    public function test_admin_can_upload_event_image_when_storing_event(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminevent3',
            'level' => 'admin',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('event-image.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/event', [
            'event_title' => 'Event Dengan Gambar',
            'event_date' => '2026-08-11',
            'event_description' => 'Event dengan gambar',
            'event_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data event berhasil ditambah.',
        ]);

        $event = Event::latest()->first();
        $this->assertNotNull($event->event_image);
        $this->assertStringContainsString('post-images/event', $event->event_image);
    }

    // Menguji skenario admin dapat mengubah data event yang sudah ada.
    public function test_admin_can_edit_existing_event_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminevent4',
            'level' => 'admin',
        ]);

        $event = Event::create([
            'event_title' => 'Event Lama',
            'event_date' => '2026-08-01 00:00:00',
            'event_description' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($admin)->post("/admin/event-edit/{$event->id}", [
            'event_title' => 'Event Diperbarui',
            'event_date' => '2026-08-15',
            'event_description' => 'Deskripsi diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data event berhasil diubah.',
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'event_title' => 'Event Diperbarui',
            'event_description' => 'Deskripsi diperbarui',
        ]);
    }

    // Menguji skenario admin dapat menghapus event beserta file gambar terkait.
    public function test_admin_can_delete_event_and_its_image(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminevent5',
            'level' => 'admin',
        ]);

        Storage::fake();
        $event = Event::create([
            'event_title' => 'Event Untuk Dihapus',
            'event_date' => '2026-08-20 00:00:00',
            'event_description' => 'Akan dihapus',
            'event_image' => 'post-images/event/example.png',
        ]);

        Storage::put($event->event_image, 'dummy-content');

        $response = $this->actingAs($admin)->delete("/admin/event/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data event berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);

        Storage::assertMissing($event->event_image);
    }
}

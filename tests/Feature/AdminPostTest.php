<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan artikel dengan data yang valid dan gambar.
    public function test_admin_can_store_post_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminpost1',
            'level' => 'admin',
        ]);

        $category = Category::create([
            'category_name' => 'Teknologi',
            'category_slug' => 'teknologi',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('post-image.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/post', [
            'title' => 'Artikel Baru',
            'category_id' => $category->id,
            'slug' => 'artikel-baru',
            'body' => 'Isi artikel baru',
            'post_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'page_title',
            'posts',
            'categories',
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Artikel Baru',
            'slug' => 'artikel-baru',
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);
    }

    // Menguji skenario penambahan artikel gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_post_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminpost2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/post')
            ->actingAs($admin)
            ->post('/admin/post', [
                'title' => '',
                'category_id' => '',
                'slug' => '',
                'body' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['title', 'category_id', 'slug', 'body', 'post_image']);
    }

    // Menguji skenario admin dapat mengubah artikel yang sudah ada dengan data baru.
    public function test_admin_can_edit_existing_post_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminpost3',
            'level' => 'admin',
        ]);

        $category = Category::create([
            'category_name' => 'Bisnis',
            'category_slug' => 'bisnis',
        ]);

        $post = Post::create([
            'title' => 'Artikel Lama',
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'slug' => 'artikel-lama',
            'excerpt' => '<p>Isi lama</p>',
            'body' => 'Isi lama',
            'post_image' => 'post-images/post/example.png',
        ]);

        $response = $this->actingAs($admin)->post("/admin/post-edit/{$post->id}", [
            'title' => 'Artikel Diperbarui',
            'category_id' => $category->id,
            'slug' => 'artikel-diperbarui',
            'body' => 'Isi artikel yang diperbarui',
            'old_post_image' => $post->post_image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data artikel berhasil diubah.',
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Artikel Diperbarui',
            'slug' => 'artikel-diperbarui',
        ]);
    }

    // Menguji skenario admin dapat menghapus artikel beserta file gambar terkait.
    public function test_admin_can_delete_post_and_its_image(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminpost4',
            'level' => 'admin',
        ]);

        $category = Category::create([
            'category_name' => 'Lifestyle',
            'category_slug' => 'lifestyle',
        ]);

        Storage::fake();
        $post = Post::create([
            'title' => 'Artikel Untuk Dihapus',
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'slug' => 'artikel-untuk-dihapus',
            'excerpt' => '<p>Isi akan dihapus</p>',
            'body' => 'Isi akan dihapus',
            'post_image' => 'post-images/post/example.png',
        ]);

        Storage::put($post->post_image, 'dummy-content');

        $response = $this->actingAs($admin)->delete("/admin/post/{$post->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data artikel berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);

        Storage::assertMissing($post->post_image);
    }
}

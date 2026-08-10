<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan kategori dengan data yang valid.
    public function test_admin_can_store_category_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'admincategory1',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/category', [
            'category_name' => 'Kategori Baru',
            'category_slug' => 'kategori-baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data kategori berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('categories', [
            'category_name' => 'Kategori Baru',
            'category_slug' => 'kategori-baru',
        ]);
    }

    // Menguji skenario penambahan kategori gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_category_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'admincategory2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/category')
            ->actingAs($admin)
            ->post('/admin/category', [
                'category_name' => '',
                'category_slug' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'status',
            'message',
            'errors' => ['category_name', 'category_slug'],
        ]);
    }

    // Menguji skenario admin dapat mengubah data kategori yang sudah ada.
    public function test_admin_can_edit_existing_category_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'admincategory3',
            'level' => 'admin',
        ]);

        $category = Category::create([
            'category_name' => 'Kategori Lama',
            'category_slug' => 'kategori-lama',
        ]);

        $response = $this->actingAs($admin)->post("/admin/category-edit/{$category->id}", [
            'category_name' => 'Kategori Diperbarui',
            'category_slug' => 'kategori-diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data kategori berhasil diubah.',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'category_name' => 'Kategori Diperbarui',
            'category_slug' => 'kategori-diperbarui',
        ]);
    }

    // Menguji skenario admin dapat menghapus kategori yang sudah ada.
    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create([
            'username' => 'admincategory4',
            'level' => 'admin',
        ]);

        $category = Category::create([
            'category_name' => 'Kategori Untuk Dihapus',
            'category_slug' => 'kategori-untuk-dihapus',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/category/{$category->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data kategori berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}

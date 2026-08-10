<?php

namespace Tests\Feature;

use App\Models\Myproduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMyproductTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat menambahkan produk dengan data yang valid.
    public function test_admin_can_store_myproduct_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyproduct1',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/myproduct', [
            'product_name' => 'Produk Baru',
            'stock' => 10,
            'price' => 50000,
            'product_description' => 'Deskripsi produk baru',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data produk berhasil ditambah.',
        ]);

        $this->assertDatabaseHas('myproducts', [
            'product_name' => 'Produk Baru',
            'stock' => 10,
            'price' => 50000,
        ]);
    }

    // Menguji skenario penambahan produk gagal saat field wajib tidak diisi.
    public function test_admin_cannot_store_myproduct_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyproduct2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/myproduct')
            ->actingAs($admin)
            ->post('/admin/myproduct', [
                'product_name' => '',
                'stock' => '',
                'price' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['product_name', 'stock', 'price']);
    }

    // Menguji skenario admin dapat mengunggah gambar produk saat menambahkan data produk.
    public function test_admin_can_upload_product_image_when_storing_myproduct(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyproduct3',
            'level' => 'admin',
        ]);

        Storage::fake();
        $image = UploadedFile::fake()->image('product-image.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/myproduct', [
            'product_name' => 'Produk Dengan Gambar',
            'stock' => 5,
            'price' => 35000,
            'product_description' => 'Produk dengan gambar',
            'product_image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data produk berhasil ditambah.',
        ]);

        $product = Myproduct::latest()->first();
        $this->assertNotNull($product->product_image);
        $this->assertStringContainsString('post-images/product', $product->product_image);
    }

    // Menguji skenario admin dapat mengubah data produk yang sudah ada.
    public function test_admin_can_edit_existing_myproduct_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyproduct4',
            'level' => 'admin',
        ]);

        $product = Myproduct::create([
            'product_name' => 'Produk Lama',
            'stock' => 2,
            'price' => 20000,
            'product_description' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($admin)->post("/admin/myproduct-edit/{$product->id}", [
            'product_name' => 'Produk Diperbarui',
            'stock' => 8,
            'price' => 60000,
            'product_description' => 'Deskripsi diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data produk berhasil diubah.',
        ]);

        $this->assertDatabaseHas('myproducts', [
            'id' => $product->id,
            'product_name' => 'Produk Diperbarui',
            'stock' => 8,
            'price' => 60000,
        ]);
    }

    // Menguji skenario admin dapat menghapus produk beserta file gambar terkait.
    public function test_admin_can_delete_myproduct_and_its_image(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminmyproduct5',
            'level' => 'admin',
        ]);

        Storage::fake();
        $product = Myproduct::create([
            'product_name' => 'Produk Untuk Dihapus',
            'stock' => 1,
            'price' => 15000,
            'product_description' => 'Akan dihapus',
            'product_image' => 'post-images/product/example.png',
        ]);

        Storage::put($product->product_image, 'dummy-content');

        $response = $this->actingAs($admin)->delete("/admin/myproduct/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data produk berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('myproducts', [
            'id' => $product->id,
        ]);

        Storage::assertMissing($product->product_image);
    }
}

<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminMyproductRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMyproductUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request produk agar sesuai dengan batasan input yang diharapkan.
    public function test_myproduct_request_validation_rules_and_messages(): void
    {
        $request = new AdminMyproductRequest();

        $this->assertSame([
            'product_name' => 'required|min:3',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'product_image' => 'nullable|image|file|max:2048',
            'product_description' => 'nullable|string',
            'old_product_image' => 'nullable|string',
        ], $request->rules());

        $this->assertSame([
            'product_name.required' => 'Nama produk wajib diisi',
            'product_name.min' => 'Nama produk minimal 3 karakter',
            'stock.required' => 'Stok wajib diisi',
            'stock.integer' => 'Stok harus berupa angka',
            'stock.min' => 'Stok minimal 0',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
            'product_image.image' => 'Foto wajib berjenis image',
            'product_image.file' => 'Foto wajib berupa file',
            'product_image.max' => 'Foto maksimal berukuran 2 Mb',
            'product_description.string' => 'Deskripsi harus berupa teks',
        ], $request->messages());
    }

    // Menguji bahwa request produk mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_myproduct_request_is_authorized(): void
    {
        $request = new AdminMyproductRequest();

        $this->assertTrue($request->authorize());
    }
}

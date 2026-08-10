<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminGaleryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGaleryUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request galeri agar sesuai dengan batasan input yang diharapkan.
    public function test_galery_request_validation_rules_and_messages(): void
    {
        $request = new AdminGaleryRequest();

        $this->assertSame([
            'image_title' => 'required|min:3',
            'galery_image' => 'nullable|image|file|max:2048',
        ], $request->rules());

        $this->assertSame([
            'image_title.required' => 'Judul galeri wajib diisi',
            'image_title.min' => 'Judul galeri minimal 3 karakter',
            'galery_image.image' => 'Foto wajib berjenis image',
            'galery_image.file' => 'Foto wajib berupa file',
            'galery_image.max' => 'Foto maksimal berukuran 2 Mb',
        ], $request->messages());
    }

    // Menguji bahwa request galeri mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_galery_request_is_authorized(): void
    {
        $request = new AdminGaleryRequest();

        $this->assertTrue($request->authorize());
    }
}

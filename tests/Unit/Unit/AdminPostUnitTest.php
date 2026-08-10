<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminPostEditRequest;
use App\Http\Requests\Admin\AdminPostStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request untuk penyimpanan artikel agar sesuai dengan kebutuhan input yang diharapkan.
    public function test_post_store_request_validation_rules_and_messages(): void
    {
        $request = new AdminPostStoreRequest();

        $this->assertSame([
            'title' => 'required|min:5',
            'category_id' => 'required',
            'slug' => 'required|min:5|unique:posts',
            'body' => 'required',
            'post_image' => 'image|file|max:2048|required',
        ], $request->rules());

        $this->assertSame([
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 5 karakter',
            'category_id.required' => 'Kategori wajib diisi',
            'slug.required' => 'Slug wajib diisi',
            'slug.min' => 'Slug minimal 5 karakter',
            'slug.unique' => 'Slug harus merupakan objek post',
            'body.required' => 'Body wajib diisi',
            'post_image.image' => 'Foto wajib berjenis image',
            'post_image.file' => 'Foto wajib berjenis file',
            'post_image.max' => 'Foto maksimal berukuran 2 Mb',
            'post_image.required' => 'Foto wajib diisi',
        ], $request->messages());
    }

    // Menguji aturan validasi request untuk pengubahan artikel agar sesuai dengan kebutuhan input yang diharapkan.
    public function test_post_edit_request_validation_rules_and_messages(): void
    {
        $request = new AdminPostEditRequest();

        $this->assertSame([
            'title' => 'required|min:5',
            'category_id' => 'required',
            'slug' => 'required|min:5',
            'body' => 'required',
            'post_image' => 'image|file|max:2048',
        ], $request->rules());

        $this->assertSame([
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 5 karakter',
            'category_id.required' => 'Kategori wajib diisi',
            'slug.required' => 'Slug wajib diisi',
            'slug.min' => 'Slug minimal 5 karakter',
            'body.required' => 'Body wajib diisi',
            'post_image.image' => 'Foto wajib berjenis image',
            'post_image.file' => 'Foto wajib berjenis file',
            'post_image.max' => 'Foto maksimal berukuran 2 Mb',
        ], $request->messages());
    }

    // Menguji bahwa request artikel mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_post_requests_are_authorized(): void
    {
        $storeRequest = new AdminPostStoreRequest();
        $editRequest = new AdminPostEditRequest();

        $this->assertTrue($storeRequest->authorize());
        $this->assertTrue($editRequest->authorize());
    }
}

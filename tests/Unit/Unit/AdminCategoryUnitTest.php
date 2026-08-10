<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminCategoryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request kategori agar sesuai dengan batasan input yang diharapkan.
    public function test_category_request_validation_rules_and_messages(): void
    {
        $request = new AdminCategoryRequest();

        $this->assertSame([
            'category_name' => 'required|min:5',
            'category_slug' => 'required|min:5',
        ], $request->rules());

        $this->assertSame([
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.min' => 'Nama kategori minimal 5 karakter.',
            'category_slug.required' => 'Slug wajib diisi.',
            'category_slug.min' => 'Slug minimal 5 karakter.',
        ], $request->messages());
    }

    // Menguji bahwa request kategori mengizinkan aksi diproses ketika user mengaksesnya.
    public function test_category_request_is_authorized(): void
    {
        $request = new AdminCategoryRequest();

        $this->assertTrue($request->authorize());
    }
}

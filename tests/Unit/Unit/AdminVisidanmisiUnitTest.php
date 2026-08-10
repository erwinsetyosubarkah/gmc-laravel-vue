<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminVisidanmisiEditRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisidanmisiUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request visi dan misi agar sesuai dengan batasan input yang diharapkan.
    public function test_visidanmisi_request_validation_rules_and_messages(): void
    {
        $request = new AdminVisidanmisiEditRequest();

        $this->assertSame([
            'title' => 'required|min:5',
            'content' => 'nullable|string',
        ], $request->rules());

        $this->assertSame([
            'title.required' => 'Judul wajib diisi',
            'title.min' => 'Judul minimal 5 karakter',
        ], $request->messages());
    }

    // Menguji bahwa request visi dan misi mengizinkan pengguna untuk mengakses aksi edit.
    public function test_visidanmisi_request_authorization(): void
    {
        $request = new AdminVisidanmisiEditRequest();

        $this->assertTrue($request->authorize());
    }
}

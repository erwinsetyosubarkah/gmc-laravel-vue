<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminEventRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request event agar sesuai dengan batasan input yang diharapkan.
    public function test_event_request_validation_rules_and_messages(): void
    {
        $request = new AdminEventRequest();

        $this->assertSame([
            'event_title' => 'required|min:3',
            'event_date' => 'required',
            'event_description' => 'nullable|string',
            'event_image' => 'nullable|image|file|max:2048',
        ], $request->rules());

        $this->assertSame([
            'event_title.required' => 'Nama event wajib diisi',
            'event_title.min' => 'Nama event minimal 3 karakter',
            'event_date.required' => 'Tanggal wajib diisi',
            'event_description.string' => 'Deskripsi harus berupa teks',
            'event_image.image' => 'File harus berupa gambar',
            'event_image.file' => 'File harus berupa file',
            'event_image.max' => 'Gambar maksimal 2 Mb',
        ], $request->messages());
    }

    // Menguji bahwa request event mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_event_request_is_authorized(): void
    {
        $request = new AdminEventRequest();

        $this->assertTrue($request->authorize());
    }
}

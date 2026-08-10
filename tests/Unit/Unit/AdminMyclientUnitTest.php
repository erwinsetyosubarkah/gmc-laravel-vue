<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminMyclientRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMyclientUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request klien agar sesuai dengan batasan input yang diharapkan.
    public function test_myclient_request_validation_rules_and_messages(): void
    {
        $request = new AdminMyclientRequest();

        $this->assertSame([
            'client_name' => 'required|min:3',
            'company_name' => 'required|min:3',
            'client_address' => 'required',
            'client_image' => 'nullable|image|file|max:2048',
            'old_client_image' => 'nullable|string',
        ], $request->rules());

        $this->assertSame([
            'client_name.required' => 'Nama klien wajib diisi',
            'client_name.min' => 'Nama klien minimal 3 karakter',
            'company_name.required' => 'Nama perusahaan wajib diisi',
            'company_name.min' => 'Nama perusahaan minimal 3 karakter',
            'client_address.required' => 'Alamat wajib diisi',
            'client_image.image' => 'Foto wajib berjenis image',
            'client_image.file' => 'Foto wajib berupa file',
            'client_image.max' => 'Foto maksimal berukuran 2 Mb',
        ], $request->messages());
    }

    // Menguji bahwa request klien mengizinkan aksi untuk diproses ketika user mengaksesnya.
    public function test_myclient_request_is_authorized(): void
    {
        $request = new AdminMyclientRequest();

        $this->assertTrue($request->authorize());
    }
}

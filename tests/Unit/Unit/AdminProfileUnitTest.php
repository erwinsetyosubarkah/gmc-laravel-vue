<?php

namespace Tests\Unit\Unit;

use App\Http\Requests\Admin\AdminProfileEditRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileUnitTest extends TestCase
{
    use RefreshDatabase;

    // Menguji aturan validasi request profile agar sesuai dengan batasan input yang diharapkan.
    public function test_profile_request_validation_rules_and_messages(): void
    {
        $request = new AdminProfileEditRequest();

        $this->assertSame([
            'id' => 'required',
            'club_name' => 'required|min:5',
            'club_name_abbreviation' => 'required',
            'club_logo' => 'nullable|image|file|max:2048',
            'email' => 'nullable|email',
            'leader_name' => 'nullable|string',
            'leader_email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'short_description' => 'nullable|max:100',
            'old_club_logo' => 'nullable|string',
        ], $request->rules());

        $this->assertSame([
            'id.required' => 'ID wajib diisi',
            'club_name.required' => 'Nama club wajib diisi',
            'club_name.min' => 'Nama club minimal 5 karakter',
            'club_name_abbreviation.required' => 'Singkatan nama club wajib diisi',
            'club_logo.image' => 'Logo wajib berjenis image',
            'club_logo.file' => 'Logo wajib berupa file',
            'club_logo.max' => 'Logo maksimal berukuran 2 Mb',
            'email.email' => 'Format email tidak valid',
            'leader_email.email' => 'Format email ketua tidak valid',
            'short_description.max' => 'Deskripsi singkat maksimal 100 karakter',
        ], $request->messages());
    }

    // Menguji bahwa request profile mengizinkan field opsional untuk dikosongkan saat update.
    public function test_profile_request_allows_optional_fields_to_be_empty(): void
    {
        $request = new AdminProfileEditRequest();

        $this->assertTrue($request->authorize());
    }

}

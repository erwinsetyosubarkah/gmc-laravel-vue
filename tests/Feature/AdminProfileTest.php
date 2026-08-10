<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    // Menguji skenario admin dapat memperbarui profil dengan data yang valid.
    public function test_admin_can_update_profile_with_valid_data(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminprofile1',
            'level' => 'admin',
        ]);

        $profile = Profile::create([
            'club_name' => 'Old Club',
            'club_name_abbreviation' => 'OC',
            'email' => 'old@example.com',
            'leader_name' => 'Old Leader',
            'leader_email' => 'leader@example.com',
            'phone' => '081234567890',
            'club_logo' => 'old-logo.png',
            'address' => 'Old Address',
            'short_description' => 'Old short description',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($admin)->post('/admin/profile', [
            'id' => $profile->id,
            'club_name' => 'New Club',
            'club_name_abbreviation' => 'NC',
            'email' => 'new@example.com',
            'leader_name' => 'New Leader',
            'leader_email' => 'newleader@example.com',
            'phone' => '081234567891',
            'address' => 'New Address',
            'short_description' => 'New short description',
            'description' => 'New description',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data profil berhasil diubah !',
        ]);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'club_name' => 'New Club',
            'email' => 'new@example.com',
        ]);
    }

    // Menguji skenario update profil gagal saat field wajib tidak diisi.
    public function test_admin_cannot_update_profile_when_required_fields_are_missing(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminprofile2',
            'level' => 'admin',
        ]);

        $response = $this->from('/admin/profile')
            ->actingAs($admin)
            ->post('/admin/profile', [
                'id' => 1,
                'club_name' => '',
                'club_name_abbreviation' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['club_name', 'club_name_abbreviation']);
    }

    // Menguji skenario update profil gagal saat format email tidak valid.
    public function test_admin_cannot_update_profile_with_invalid_email_format(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminprofile3',
            'level' => 'admin',
        ]);

        $profile = Profile::create([
            'club_name' => 'Valid Club',
            'club_name_abbreviation' => 'VC',
            'email' => 'valid@example.com',
            'leader_name' => 'Leader',
            'leader_email' => 'leader@example.com',
            'phone' => '081234567890',
            'club_logo' => 'logo.png',
            'address' => 'Address',
            'short_description' => 'Short description',
            'description' => 'Description',
        ]);

        $response = $this->from('/admin/profile')
            ->actingAs($admin)
            ->post('/admin/profile', [
                'id' => $profile->id,
                'club_name' => 'Valid Club',
                'club_name_abbreviation' => 'VC',
                'email' => 'not-an-email',
                'leader_email' => 'also-not-an-email',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email', 'leader_email']);
    }

    // Menguji skenario upload logo profil berhasil saat admin mengupdate data profil.
    public function test_admin_can_upload_club_logo_when_updating_profile(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminprofile4',
            'level' => 'admin',
        ]);

        $profile = Profile::create([
            'club_name' => 'Logo Club',
            'club_name_abbreviation' => 'LC',
            'email' => 'logo@example.com',
            'leader_name' => 'Logo Leader',
            'leader_email' => 'leaderlogo@example.com',
            'phone' => '081234567892',
            'club_logo' => 'old-logo.png',
            'address' => 'Logo Address',
            'short_description' => 'Logo short description',
            'description' => 'Logo description',
        ]);

        Storage::fake();
        $logo = UploadedFile::fake()->image('club-logo.png', 200, 200);

        $response = $this->actingAs($admin)->post('/admin/profile', [
            'id' => $profile->id,
            'club_name' => 'Logo Club Updated',
            'club_name_abbreviation' => 'LCU',
            'email' => 'logo-updated@example.com',
            'leader_name' => 'Updated Logo Leader',
            'leader_email' => 'updatedlogo@example.com',
            'phone' => '081234567893',
            'address' => 'Updated Logo Address',
            'short_description' => 'Updated logo short description',
            'description' => 'Updated logo description',
            'club_logo' => $logo,
            'old_club_logo' => $profile->club_logo,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Data profil berhasil diubah !',
        ]);

        $storedPath = $profile->fresh()->club_logo;
        $this->assertNotNull($storedPath);
        $this->assertNotSame('old-logo.png', $storedPath);
        $this->assertStringContainsString('post-images/profile', $storedPath);
    }

    // Menguji skenario upload logo profil gagal saat file yang dikirim bukan gambar.
    public function test_admin_cannot_upload_non_image_file_as_club_logo(): void
    {
        $admin = User::factory()->create([
            'username' => 'adminprofile5',
            'level' => 'admin',
        ]);

        $profile = Profile::create([
            'club_name' => 'Non Image Club',
            'club_name_abbreviation' => 'NIC',
            'email' => 'nonimage@example.com',
            'leader_name' => 'Non Image Leader',
            'leader_email' => 'nonimageleader@example.com',
            'phone' => '081234567894',
            'club_logo' => 'old-logo.png',
            'address' => 'Non Image Address',
            'short_description' => 'Non image short description',
            'description' => 'Non image description',
        ]);

        Storage::fake();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->from('/admin/profile')
            ->actingAs($admin)
            ->post('/admin/profile', [
                'id' => $profile->id,
                'club_name' => 'Non Image Club Updated',
                'club_name_abbreviation' => 'NICU',
                'email' => 'nonimage-updated@example.com',
                'leader_name' => 'Updated Non Image Leader',
                'leader_email' => 'updatednonimage@example.com',
                'phone' => '081234567895',
                'address' => 'Updated Non Image Address',
                'short_description' => 'Updated non image short description',
                'description' => 'Updated non image description',
                'club_logo' => $file,
                'old_club_logo' => $profile->club_logo,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['club_logo']);
    }
}

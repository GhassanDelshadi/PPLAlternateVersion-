<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pasien;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Bonamu',
            'email' => 'admin@test.com',
            'no_hp' => '081234567890',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->pasien = User::create([
            'name' => 'Budi Santoso',
            'email' => 'pasien@test.com',
            'no_hp' => '081299999999',
            'password' => bcrypt('password'),
            'role' => 'pasien',
            'is_active' => true,
        ]);
        $this->pasien->patientProfile()->create([
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 10',
            'jenis_pasien' => 'UMUM',
        ]);
    }

    public function test_route_dashboard_redirects_admin_ke_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_route_dashboard_redirects_pasien_ke_pasien_dashboard(): void
    {
        $response = $this->actingAs($this->pasien)->get(route('dashboard'));
        $response->assertRedirect(route('pasien.dashboard'));
    }

    public function test_admin_bisa_membuka_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Selamat Datang, Admin Bonamu');
        $response->assertSee('Poli Aktif');
        $response->assertSee('Dokter Terdaftar');
    }

    public function test_pasien_bisa_membuka_pasien_dashboard(): void
    {
        $response = $this->actingAs($this->pasien)->get(route('pasien.dashboard'));
        $response->assertOk();
        $response->assertSee('Halo, Budi Santoso!');
        $response->assertSee('Ambil Antrean');
    }

    public function test_admin_bisa_melihat_dan_update_profil(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profil.edit'));
        $response->assertOk();
        $response->assertSee('Data Akun Administrator');

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'name' => 'Admin Bonamu Baru',
            'email' => 'admin.baru@test.com',
            'no_hp' => '081234567899',
        ]);

        $updateResponse->assertRedirect(route('admin.profil.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Admin Bonamu Baru',
            'email' => 'admin.baru@test.com',
        ]);
    }

    public function test_pasien_tidak_boleh_akses_admin_dashboard(): void
    {
        $response = $this->actingAs($this->pasien)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }
}

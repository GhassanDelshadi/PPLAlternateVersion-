<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\KlinikCabang;
use App\Models\Poli;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAllFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private KlinikCabang $cabang;
    private User $admin;
    private User $dokterUser;
    private Doctor $doctor;
    private Poli $poli;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabang = KlinikCabang::create([
            'nama_cabang' => 'Klinik Bonamu Pusat',
            'alamat' => 'Jl. Sukabirus No. 12 Bandung',
            'nomor_telepon' => '0221234567',
            'status' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Bonamu',
            'email' => 'admin@test.com',
            'no_hp' => '081234567890',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'klinik_cabang_id' => $this->cabang->id,
        ]);

        $this->dokterUser = User::create([
            'name' => 'dr. Andi Pratama',
            'email' => 'dokter.andi@test.com',
            'no_hp' => '081200000001',
            'password' => bcrypt('password'),
            'role' => 'dokter',
            'is_active' => true,
            'klinik_cabang_id' => $this->cabang->id,
        ]);

        $this->doctor = Doctor::create([
            'user_id' => $this->dokterUser->id,
            'nomor_sip' => 'SIP-0001',
            'deskripsi' => 'Dokter Umum Berpengalaman',
        ]);

        $this->poli = Poli::create([
            'klinik_cabang_id' => $this->cabang->id,
            'kode_poli' => 'PU',
            'nama_poli' => 'Poli Umum',
            'status' => true,
        ]);
    }

    public function test_pbi01_api_registrasi_pasien_baru(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Pasien Baru API',
            'email' => 'pasien.api@test.com',
            'no_hp' => '081233445566',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'pasien.api@test.com',
            'role' => 'pasien',
        ]);
    }

    public function test_pbi02_api_login_berhasil_dan_mendapatkan_token(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.role', 'admin')
            ->assertJsonStructure([
                'status',
                'data' => ['user', 'token', 'token_type'],
            ]);
    }

    public function test_pbi03_api_admin_membuat_akun_dokter_baru(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/users', [
                'name' => 'dr. Budi Susanto',
                'email' => 'dr.budi@test.com',
                'no_hp' => '081288889999',
                'password' => 'password123',
                'role' => 'dokter',
                'nomor_sip' => 'SIP-BUDI-001',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('users', ['email' => 'dr.budi@test.com', 'role' => 'dokter']);
        $this->assertDatabaseHas('doctors', ['nomor_sip' => 'SIP-BUDI-001']);
    }

    public function test_pbi04_api_profil_pasien_dapat_diambil_dan_diperbarui(): void
    {
        $pasien = User::create([
            'name' => 'Pasien Profil API',
            'email' => 'profil.pasien@test.com',
            'no_hp' => '081277778888',
            'password' => bcrypt('password'),
            'role' => 'pasien',
            'is_active' => true,
        ]);

        $response = $this->actingAs($pasien, 'sanctum')
            ->putJson('/api/v1/pasien/profil', [
                'tanggal_lahir' => '1998-08-17',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Merdeka No. 45',
                'jenis_pasien' => 'bpjs',
                'no_bpjs' => '000123999999',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.no_bpjs', '000123999999');

        $this->assertDatabaseHas('patient_profiles', [
            'user_id' => $pasien->id,
            'jenis_pasien' => 'bpjs',
            'no_bpjs' => '000123999999',
        ]);
    }

    public function test_pbi05_api_master_data_poli_crud(): void
    {
        // 1. Admin tambah poli
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/polis', [
                'kode_poli' => 'THT',
                'nama_poli' => 'Poli Telinga Hidung Tenggorokan',
                'deskripsi' => 'Spesialis THT',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.kode_poli', 'THT');

        $poliId = $response->json('data.id');

        // 2. Publik lihat poli
        $listResponse = $this->getJson('/api/v1/polis');
        $listResponse->assertStatus(200)
            ->assertJsonFragment(['kode_poli' => 'THT']);

        // 3. Toggle status
        $toggleResponse = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/polis/{$poliId}/toggle-status");
        $toggleResponse->assertStatus(200)
            ->assertJsonPath('data.status', false);
    }

    public function test_pbi07_dan_pbi08_api_jadwal_dokter(): void
    {
        // 1. Admin buat jadwal
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/jadwal', [
                'doctor_id' => $this->doctor->id,
                'poli_id' => $this->poli->id,
                'hari' => 'senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'kuota' => 20,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.hari', 'senin');

        // 2. Publik lihat jadwal
        $listResponse = $this->getJson('/api/v1/jadwal?hari=senin');
        $listResponse->assertStatus(200)
            ->assertJsonFragment(['hari' => 'senin']);
    }

    public function test_pbi09_api_antrean_online_dan_monitoring_admin(): void
    {
        // Pasien dengan profil lengkap
        $pasien = User::create([
            'name' => 'Pasien Antrean API',
            'email' => 'antrean.api@test.com',
            'no_hp' => '081299991111',
            'password' => bcrypt('password'),
            'role' => 'pasien',
            'is_active' => true,
        ]);

        $pasien->patientProfile()->create([
            'tanggal_lahir' => '1995-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Bandung',
            'jenis_pasien' => 'umum',
        ]);

        // Cari hari hari ini
        $namaHariMap = [0 => 'minggu', 1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu'];
        $hariIni = $namaHariMap[Carbon::today()->dayOfWeek];

        $schedule = DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'poli_id' => $this->poli->id,
            'hari' => $hariIni,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '16:00:00',
            'kuota' => 20,
            'status' => true,
        ]);

        // Pasien ambil antrean
        $antreanResponse = $this->actingAs($pasien, 'sanctum')
            ->postJson('/api/v1/antrean', [
                'doctor_schedule_id' => $schedule->id,
                'tanggal_antrean' => Carbon::today()->toDateString(),
            ]);

        $antreanResponse->assertStatus(201)
            ->assertJsonPath('data.nomor_antrean', 1)
            ->assertJsonPath('data.status', 'waiting');

        $queueId = $antreanResponse->json('data.id');

        // Admin monitoring antrean & ubah status ke serving
        $statusResponse = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/antrean/{$queueId}/status", [
                'status' => 'serving',
            ]);

        $statusResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'serving');
    }

    public function test_pbi10_dan_pbi11_api_konsultasi_online_dan_chat(): void
    {
        $pasien = User::create([
            'name' => 'Pasien Chat API',
            'email' => 'chat.pasien@test.com',
            'no_hp' => '081266667777',
            'password' => bcrypt('password'),
            'role' => 'pasien',
            'is_active' => true,
        ]);
        $pasien->patientProfile()->create(['alamat' => 'Bandung']);

        // Pasien mulai konsultasi
        $konsultasiResponse = $this->actingAs($pasien, 'sanctum')
            ->postJson('/api/v1/konsultasi', [
                'doctor_id' => $this->doctor->id,
                'subject' => 'Demam dan Flu',
                'keluhan' => 'Dok, saya mengalami demam tinggi sejak kemarin sore.',
            ]);

        $konsultasiResponse->assertStatus(201)
            ->assertJsonPath('data.subject', 'Demam dan Flu');

        $consultationId = $konsultasiResponse->json('data.id');

        // Dokter membalas chat
        $balasResponse = $this->actingAs($this->dokterUser, 'sanctum')
            ->postJson("/api/v1/konsultasi/{$consultationId}/pesan", [
                'message' => 'Halo, apakah ada batuk atau radang tenggorokan?',
            ]);

        $balasResponse->assertStatus(201)
            ->assertJsonPath('data.message', 'Halo, apakah ada batuk atau radang tenggorokan?');
    }

    public function test_pbi12_api_layanan_dan_tarif_klinik(): void
    {
        // Admin tambah tarif baru
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/layanan', [
                'nama_layanan' => 'Pemeriksaan EKG Jantung',
                'kode_layanan' => 'EKG-01',
                'tarif_umum' => 150000,
                'tarif_bpjs' => 0,
                'poli_id' => $this->poli->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama_layanan', 'Pemeriksaan EKG Jantung');

        // Publik baca daftar layanan
        $listResponse = $this->getJson('/api/v1/layanan');
        $listResponse->assertStatus(200)
            ->assertJsonFragment(['nama_layanan' => 'Pemeriksaan EKG Jantung']);
    }
}

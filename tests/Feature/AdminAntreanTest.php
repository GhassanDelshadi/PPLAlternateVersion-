<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\KlinikCabang;
use App\Models\PatientProfile;
use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAntreanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pasien;
    protected PatientProfile $profile;
    protected DoctorSchedule $jadwal;
    protected Queue $queue1;
    protected Queue $queue2;

    protected function setUp(): void
    {
        parent::setUp();

        $cabang = KlinikCabang::create([
            'nama_cabang' => 'Klinik Bonamu Pusat',
            'alamat' => 'Jl. Pengujian No. 1',
            'status' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Bonamu',
            'email' => 'admin@bonamu.test',
            'no_hp' => '081234567890',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->pasien = User::create([
            'name' => 'Pasien Budi',
            'email' => 'budi@pasien.test',
            'no_hp' => '081299999999',
            'role' => 'pasien',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->profile = PatientProfile::create([
            'user_id' => $this->pasien->id,
            'nik' => '3273010101010001',
            'tanggal_lahir' => '1995-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pasien No. 12',
            'jenis_pasien' => 'umum',
        ]);

        $doctorUser = User::create([
            'name' => 'dr. Andi',
            'email' => 'andi@dokter.test',
            'no_hp' => '081200000001',
            'role' => 'dokter',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'nomor_sip' => 'SIP-0001',
        ]);

        $poli = Poli::create([
            'klinik_cabang_id' => $cabang->id,
            'nama_poli' => 'Poli Umum',
            'kode_poli' => 'PU',
            'status' => true,
        ]);

        $this->jadwal = DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'poli_id' => $poli->id,
            'hari' => 'senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'kuota' => 15,
            'status' => true,
        ]);

        // Antrean 1: Menunggu
        $this->queue1 = Queue::create([
            'patient_profile_id' => $this->profile->id,
            'doctor_schedule_id' => $this->jadwal->id,
            'tanggal_antrean' => Carbon::today()->toDateString(),
            'nomor_antrean' => 1,
            'status' => Queue::STATUS_WAITING,
        ]);

        // Antrean 2: Menunggu
        $this->queue2 = Queue::create([
            'patient_profile_id' => $this->profile->id,
            'doctor_schedule_id' => $this->jadwal->id,
            'tanggal_antrean' => Carbon::today()->toDateString(),
            'nomor_antrean' => 2,
            'status' => Queue::STATUS_WAITING,
        ]);
    }

    public function test_admin_bisa_melihat_daftar_antrean_pasien(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.antrean.index'));

        $response->assertStatus(200);
        $response->assertSee('Monitoring &amp; Kelola Antrean Pasien', false);
        $response->assertSee('Pasien Budi');
        $response->assertSee('PU-001', false);
        $response->assertSee('PU-002', false);
    }

    public function test_admin_bisa_memfilter_antrean_berdasarkan_status(): void
    {
        // Ubah antrean 1 menjadi sedang dilayani
        $this->queue1->update(['status' => Queue::STATUS_SERVING]);

        $response = $this->actingAs($this->admin)->get(route('admin.antrean.index', [
            'status' => 'serving',
        ]));

        $response->assertStatus(200);
        $response->assertSee('PU-001', false);
        $response->assertDontSee('PU-002', false);
    }

    public function test_admin_bisa_mengubah_status_antrean_dari_waiting_ke_serving(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.antrean.update-status', $this->queue1), [
            'status' => 'serving',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue1->id,
            'status' => 'serving',
        ]);
    }

    public function test_admin_bisa_mengubah_status_antrean_dari_serving_ke_completed(): void
    {
        $this->queue1->update(['status' => Queue::STATUS_SERVING]);

        $response = $this->actingAs($this->admin)->patch(route('admin.antrean.update-status', $this->queue1), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue1->id,
            'status' => 'completed',
        ]);
    }

    public function test_update_status_ditolak_jika_nilai_status_tidak_valid(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.antrean.update-status', $this->queue1), [
            'status' => 'status_ngawur',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue1->id,
            'status' => Queue::STATUS_WAITING,
        ]);
    }

    public function test_pasien_tidak_bisa_mengakses_antrean_admin(): void
    {
        $response = $this->actingAs($this->pasien)->get(route('admin.antrean.index'));
        $response->assertStatus(403);
    }
}

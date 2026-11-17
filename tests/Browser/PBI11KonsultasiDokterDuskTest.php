<?php

namespace Tests\Browser;

use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI11KonsultasiDokterDuskTest extends DuskTestCase
{
    private User $dokterUser;
    private Doctor $dokter;
    private Consultation $konsultasi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dokterUser = User::updateOrCreate(
            ['email' => 'dokter.andi@test.com'],
            [
                'name' => 'dr. Andi',
                'no_hp' => '081200000001',
                'password' => bcrypt('password'),
                'role' => 'dokter',
                'is_active' => true,
            ]
        );

        $this->dokter = Doctor::firstOrCreate(
            ['user_id' => $this->dokterUser->id],
            ['nomor_sip' => 'SIP-0001']
        );

        $pasien = User::firstOrCreate(
            ['email' => 'pasien@test.com'],
            [
                'name' => 'Budi Santoso',
                'no_hp' => '081299999999',
                'password' => bcrypt('password'),
                'role' => 'pasien',
                'is_active' => true,
            ]
        );

        $profil = PatientProfile::firstOrCreate(['user_id' => $pasien->id]);

        $jadwal = DoctorSchedule::where('doctor_id', $this->dokter->id)->first()
            ?: DoctorSchedule::firstOrFail();

        // Bersihkan konsultasi uji sebelumnya
        Consultation::where('nomor_konsultasi', 'like', 'KON-%-999%')->delete();

        $this->konsultasi = Consultation::create([
            'nomor_konsultasi' => 'KON-' . date('Ymd') . '-999' . rand(1, 9),
            'patient_profile_id' => $profil->id,
            'doctor_id' => $this->dokter->id,
            'doctor_schedule_id' => $jadwal->id,
            'tanggal_konsultasi' => Carbon::today()->toDateString(),
            'subject' => 'Konsultasi Gejala Demam',
            'keluhan' => 'Demam dan flu sejak dua hari terakhir.',
            'status' => 'pending',
        ]);
    }

    /**
     * Skenario Normal PBI-11:
     * Dokter melihat konsultasi masuk, memulai sesi (status: ongoing),
     * lalu mengakhiri sesi konsultasi (status: completed).
     */
    public function test_normal_dokter_menangani_konsultasi_online_hingga_selesai(): void
    {
        $nomor = $this->konsultasi->nomor_konsultasi;
        $id = $this->konsultasi->id;

        $this->browse(function (Browser $browser) use ($nomor, $id) {
            // 1. Login Dokter
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081200000001')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // 2. Masuk ke halaman Konsultasi Dokter
            $browser->visit('/dokter/konsultasi')
                ->waitForText('Permintaan konsultasi')
                ->assertSee($nomor);

            // 3. Mulai Sesi Konsultasi untuk konsultasi ini
            $browser->script("document.querySelector(\"form[action*='{$id}/mulai']\").submit();");

            $browser->waitForText('Berlangsung')
                ->assertSee($nomor);

            // Pembuktian Database Tahap 1: Status berubah menjadi ongoing
            $this->assertDatabaseHas('consultations', [
                'id' => $id,
                'status' => 'ongoing',
            ]);

            // 4. Akhiri Sesi Konsultasi
            $browser->script("document.querySelector(\"form[action*='{$id}/selesai']\").submit();");

            $browser->waitForText('Permintaan konsultasi')
                ->assertSee('Sesi diakhiri.');
        });

        // Pembuktian Database Tahap 2: Status akhir tersimpan completed
        $this->assertDatabaseHas('consultations', [
            'id' => $this->konsultasi->id,
            'status' => 'completed',
        ]);
    }

    /**
     * Skenario Exception PBI-11:
     * Dokter tidak dapat memulai sesi yang statusnya sudah Selesai (completed).
     */
    public function test_exception_dokter_tidak_bisa_memulai_sesi_yang_sudah_selesai(): void
    {
        // Ubah status konsultasi langsung menjadi completed
        $this->konsultasi->update(['status' => 'completed']);
        $id = $this->konsultasi->id;

        $this->browse(function (Browser $browser) use ($id) {
            $browser->visit('/dokter/konsultasi')
                ->waitForText('Permintaan konsultasi')
                ->assertMissing("form[action*='{$id}/mulai'] button"); // Tidak ada tombol mulai sesi
        });
    }
}

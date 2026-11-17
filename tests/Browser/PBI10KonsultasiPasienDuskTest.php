<?php

namespace Tests\Browser;

use App\Models\Consultation;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\User;
use App\Services\QueueService;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI10KonsultasiPasienDuskTest extends DuskTestCase
{
    private User $pasien;
    private PatientProfile $profil;
    private DoctorSchedule $jadwal;
    private string $tanggalUji;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pasien = User::updateOrCreate(
            ['email' => 'pasien@test.com'],
            [
                'name' => 'Budi Santoso',
                'no_hp' => '081299999999',
                'password' => bcrypt('password'),
                'role' => 'pasien',
                'is_active' => true,
            ]
        );

        $this->profil = PatientProfile::updateOrCreate(
            ['user_id' => $this->pasien->id],
            [
                'tanggal_lahir' => '1995-01-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Sukabirus No. 12 Bandung',
                'jenis_pasien' => 'UMUM',
            ]
        );

        $this->jadwal = DoctorSchedule::where('status', true)->firstOrFail();

        // Cari tanggal yang harinya cocok dengan jadwal
        $hariTujuan = strtolower($this->jadwal->hari);
        $date = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            if (QueueService::hariDariTanggal($date->toDateString()) === $hariTujuan) {
                break;
            }
            $date->addDay();
        }
        $this->tanggalUji = $date->toDateString();

        // Bersihkan konsultasi aktif pasien agar tidak terkena validasi batas konsultasi aktif
        Consultation::where('patient_profile_id', $this->profil->id)->delete();
    }

    /**
     * Skenario Normal PBI-10:
     * Pasien membuat janji konsultasi online dengan dokter, mengisi keluhan medis,
     * dan berhasil mendapatkan nomor konsultasi unik.
     */
    public function test_normal_pasien_berhasil_membuat_konsultasi_online_dan_tersimpan_di_database(): void
    {
        $jadwalId = $this->jadwal->id;
        $tanggal = $this->tanggalUji;
        $poliId = $this->jadwal->poli_id;

        $this->browse(function (Browser $browser) use ($jadwalId, $tanggal, $poliId) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // Buka halaman buat konsultasi
            $url = route('konsultasi.create', ['tanggal' => $tanggal, 'poli' => $poliId]);
            $browser->visit($url)
                ->waitForText('BUAT KONSULTASI ONLINE')
                ->script("document.querySelector('input[name=\"doctor_schedule_id\"]').checked = true;");

            // Isi keluhan medis (min. 10 karakter)
            $browser->type('keluhan', 'Kepala pusing sejak kemarin malam disertai demam ringan.');

            // Submit form
            $browser->press('Buat Konsultasi');

            // Verifikasi di browser
            $browser->waitForText('Konsultasi berhasil dibuat')
                ->assertSee('NOMOR KONSULTASI')
                ->assertSee('Menunggu');
        });

        // Pembuktian Database MySQL: Konsultasi tersimpan nyata
        $this->assertDatabaseHas('consultations', [
            'patient_profile_id' => $this->profil->id,
            'doctor_schedule_id' => $this->jadwal->id,
            'tanggal_konsultasi' => $this->tanggalUji,
            'status' => 'pending',
        ]);
    }

    /**
     * Skenario Exception PBI-10:
     * Pembuatan konsultasi ditolak jika keluhan kurang dari 10 karakter.
     */
    public function test_exception_konsultasi_ditolak_jika_keluhan_terlalu_pendek(): void
    {
        $url = route('konsultasi.create', ['tanggal' => $this->tanggalUji, 'poli' => $this->jadwal->poli_id]);

        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit($url)
                ->waitForText('BUAT KONSULTASI ONLINE')
                ->script("document.querySelector('input[name=\"doctor_schedule_id\"]').checked = true;");

            $browser->type('keluhan', 'Sakit') // Hanya 5 karakter (kurang dari 10)
                ->press('Buat Konsultasi')
                ->waitForText('Keluhan minimal 10 karakter')
                ->assertSee('Keluhan minimal 10 karakter');
        });
    }
}

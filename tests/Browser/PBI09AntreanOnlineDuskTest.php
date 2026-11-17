<?php

namespace Tests\Browser;

use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Queue;
use App\Models\User;
use App\Services\QueueService;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI09AntreanOnlineDuskTest extends DuskTestCase
{
    private User $pasien;
    private PatientProfile $profil;
    private DoctorSchedule $jadwal;
    private string $tanggalUji;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Akun Pasien
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

        // Pastikan profil pasien lengkap agar diizinkan mengambil antrean online
        $this->profil = PatientProfile::updateOrCreate(
            ['user_id' => $this->pasien->id],
            [
                'tanggal_lahir' => '1995-01-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Sukabirus No. 12, Bandung',
                'jenis_pasien' => 'UMUM',
            ]
        );

        // Ambil jadwal dokter aktif
        $this->jadwal = DoctorSchedule::where('status', true)->firstOrFail();

        // Tentukan tanggal yang harinya cocok dengan jadwal
        $hariTujuan = strtolower($this->jadwal->hari);
        $date = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            if (QueueService::hariDariTanggal($date->toDateString()) === $hariTujuan) {
                break;
            }
            $date->addDay();
        }
        $this->tanggalUji = $date->toDateString();

        // Bersihkan antrean aktif pasien agar tombol ambil antrean tidak terkunci
        Queue::where('patient_profile_id', $this->profil->id)->delete();
    }

    /**
     * Skenario PBI-09 (Pendaftaran Antrean Online Mandiri oleh Pasien):
     * 1. Login sebagai Pasien
     * 2. Buka menu Pendaftaran Antrean Online (/antrean/create)
     * 3. Pilih tanggal kunjungan yang memiliki jadwal praktik dokter
     * 4. Klik Ambil Nomor Antrean pada jadwal dokter terpilih
     * 5. Verifikasi di browser: Terbit Tiket Antrean Online dengan nomor antrean digital
     * 6. Pembuktian Database: Tiket antrean tersimpan nyata di tabel MySQL 'queues'
     */
    public function test_pasien_mendaftar_antrean_online_dan_tiket_tersimpan_di_database(): void
    {
        $jadwalId = $this->jadwal->id;
        $tanggal = $this->tanggalUji;

        $this->browse(function (Browser $browser) use ($jadwalId, $tanggal) {
            // 1. Login Pasien
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // 2. Buka halaman Ambil Antrean dengan filter tanggal dan poli yang cocok
            $url = route('antrean.create', ['tanggal' => $tanggal, 'poli' => $this->jadwal->poli_id]);
            $browser->visit($url)
                ->waitForText('Pendaftaran Antrean Online')
                ->assertSee('Dokter Berpraktik Hari');

            // 3. Konfirmasi alert dialog dan submit form Ambil Antrean
            $browser->script("window.confirm = () => true;");
            $browser->driver->findElement(WebDriverBy::xpath("//input[@name='doctor_schedule_id' and @value='{$jadwalId}']/parent::form"))->submit();

            // 4. Verifikasi halaman Tiket Antrean Online (PBI-09)
            $browser->waitForText('Bukti Pendaftaran Antrean Online')
                ->assertSee('KLINIK BONA MITRA KELUARGA')
                ->assertPresent('@nomor-antrean-display')
                ->assertSee('Menunggu');
        });

        // 5. Pembuktian Database MySQL: Antrean tersimpan nyata
        $this->assertDatabaseHas('queues', [
            'patient_profile_id' => $this->profil->id,
            'doctor_schedule_id' => $this->jadwal->id,
            'tanggal_antrean' => $this->tanggalUji,
            'status' => 'waiting',
        ]);
    }

    /**
     * Skenario Exception PBI-09:
     * Pasien yang memiliki antrean aktif tidak diizinkan membuat antrean ganda (tampil peringatan antrean aktif).
     */
    public function test_exception_pasien_tidak_bisa_mengambil_antrean_ganda_saat_punya_antrean_aktif(): void
    {
        // Pastikan pasien sudah memiliki 1 antrean aktif
        Queue::firstOrCreate(
            [
                'patient_profile_id' => $this->profil->id,
                'doctor_schedule_id' => $this->jadwal->id,
                'tanggal_antrean' => $this->tanggalUji,
            ],
            [
                'nomor_antrean' => 1,
                'status' => 'waiting',
            ]
        );

        $this->browse(function (Browser $browser) {
            $browser->visit(route('antrean.create'))
                ->waitForText('Pendaftaran Antrean Online')
                ->assertSee('Nomor Antrean Anda:')
                ->assertSee('Lihat Tiket');
        });
    }
}

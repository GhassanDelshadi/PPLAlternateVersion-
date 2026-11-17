<?php

namespace Tests\Browser;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Poli;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI07JadwalDokterDuskTest extends DuskTestCase
{
    private Doctor $doctor;
    private Poli $poli;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan akun admin tersedia
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Bonamu',
                'no_hp' => '081234567890',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->doctor = Doctor::firstOrFail();
        $this->poli = Poli::firstOrFail();
    }

    /**
     * Skenario PBI-07 (Manajemen Jadwal Praktik Dokter):
     * 1. Login sebagai Administrator
     * 2. Buka menu Jadwal Dokter (/admin/jadwal)
     * 3. Masuk ke form Tambah Jadwal Praktik (/admin/jadwal/create)
     * 4. Pilih Dokter, Poli, Hari (Jumat), Jam Mulai, Jam Selesai, dan Kuota Antrean
     * 5. Simpan dan verifikasi jadwal baru muncul di tabel
     * 6. Pembuktian Database: Record jadwal tersimpan nyata di tabel MySQL 'doctor_schedules'
     */
    public function test_admin_membuat_jadwal_praktik_baru_dan_tersimpan_di_database(): void
    {
        // Bersihkan seluruh jadwal hari jumat untuk dokter ini agar tidak bentrok jam
        DoctorSchedule::where('doctor_id', $this->doctor->id)
            ->where('hari', 'jumat')
            ->delete();

        $this->browse(function (Browser $browser) {
            // 1. Login Admin
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081234567890')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // 2. Akses menu Jadwal Dokter
            $browser->visit('/admin/jadwal')
                ->waitForText('Jadwal Praktik Dokter')
                ->visit('/admin/jadwal/create');

            // 3. Mengisi form jadwal baru
            $browser->waitForText('Tambah Jadwal Praktik')
                ->select('doctor_id', (string) $this->doctor->id)
                ->select('poli_id', (string) $this->poli->id)
                ->select('hari', 'jumat')
                ->value('input[name="jam_mulai"]', '13:00')
                ->value('input[name="jam_selesai"]', '16:00')
                ->value('input[name="kuota"]', '30');

            // 4. Submit form
            $browser->driver->findElement(WebDriverBy::cssSelector('form[action*="admin/jadwal"]'))->submit();

            // 5. Verifikasi di browser
            $browser->visit('/admin/jadwal?hari=jumat')
                ->waitForText('Jadwal Praktik Dokter')
                ->assertSee('Jumat')
                ->assertSee('30 pasien');
        });

        // 6. Pembuktian Database MySQL
        $this->assertDatabaseHas('doctor_schedules', [
            'doctor_id' => $this->doctor->id,
            'poli_id' => $this->poli->id,
            'hari' => 'jumat',
            'kuota' => 30,
            'status' => 1,
        ]);
    }

    /**
     * Skenario Exception PBI-07:
     * Pembuatan jadwal ditolak jika jam selesai lebih awal dari jam mulai.
     */
    public function test_exception_pembuatan_jadwal_gagal_jika_jam_selesai_lebih_awal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/jadwal/create')
                ->waitForText('Tambah Jadwal Praktik')
                ->select('doctor_id', (string) $this->doctor->id)
                ->select('poli_id', (string) $this->poli->id)
                ->select('hari', 'sabtu')
                ->value('input[name="jam_mulai"]', '15:00')
                ->value('input[name="jam_selesai"]', '10:00') // Tidak logis: selesai sebelum mulai
                ->value('input[name="kuota"]', '20');

            $browser->driver->findElement(WebDriverBy::cssSelector('form[action*="admin/jadwal"]'))->submit();

            $browser->waitForText('Jam selesai harus setelah jam mulai.')
                ->assertSee('Jam selesai harus setelah jam mulai.');
        });
    }
}

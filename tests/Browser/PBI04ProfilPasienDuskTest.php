<?php

namespace Tests\Browser;

use App\Models\PatientProfile;
use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI04ProfilPasienDuskTest extends DuskTestCase
{
    private User $pasien;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan akun pasien tersedia
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

        PatientProfile::firstOrCreate(['user_id' => $this->pasien->id]);
    }

    /**
     * Skenario PBI-04 (Pengisian Identitas & Profil Pasien):
     * 1. Login sebagai Pasien (Budi Santoso)
     * 2. Buka menu Profil Pasien (/pasien/profil)
     * 3. Isi data identitas: Tanggal Lahir, Jenis Kelamin, Alamat, Kategori BPJS, dan No. BPJS
     * 4. Simpan perubahan profil
     * 5. Verifikasi notifikasi sukses dan data terpasang di layar
     * 6. Pembuktian Database: Data tersimpan nyata di tabel MySQL 'patient_profiles'
     */
    public function test_pasien_melengkapi_profil_identitas_dan_tersimpan_di_database(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Login Pasien
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // 2. Akses halaman Profil Pasien
            $browser->visit('/pasien/profil')
                ->waitForText('Profil & Identitas Pasien')
                ->assertSee('Form Data Identitas Pasien');

            // 3. Mengisi data profil pasien
            $browser->value('input[name="tanggal_lahir"]', '1998-05-20')
                ->select('jenis_kelamin', 'L')
                ->value('textarea[name="alamat"]', 'Jl. Sukabirus No. 45, Bojongsoang, Bandung')
                ->select('jenis_pasien', 'BPJS')
                ->pause(300)
                ->value('input[name="no_bpjs"]', '0001234567890');

            // 4. Submit form
            $browser->driver->findElement(WebDriverBy::cssSelector('form[dusk="form-profil"]'))->submit();

            // 5. Verifikasi di browser
            $browser->waitForText('Profil identitas pasien berhasil diperbarui.')
                ->assertSee('BPJS Kesehatan');
        });

        // 6. Pembuktian Database MySQL
        $this->assertDatabaseHas('patient_profiles', [
            'user_id' => $this->pasien->id,
            'jenis_kelamin' => 'L',
            'jenis_pasien' => 'BPJS',
            'no_bpjs' => '0001234567890',
        ]);
    }

    /**
     * Skenario Exception PBI-04:
     * Pasien BPJS wajib menyertakan nomor BPJS, jika kosong form ditolak validasi.
     */
    public function test_exception_profil_bpjs_ditolak_jika_nomor_bpjs_kosong(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/pasien/profil')
                ->waitForText('Profil & Identitas Pasien')
                ->value('input[name="tanggal_lahir"]', '1998-05-20')
                ->select('jenis_kelamin', 'L')
                ->value('textarea[name="alamat"]', 'Jl. Sukabirus No. 45 Bandung')
                ->select('jenis_pasien', 'BPJS')
                ->pause(200)
                ->value('input[name="no_bpjs"]', ''); // Kosongkan nomor BPJS

            $browser->driver->findElement(WebDriverBy::cssSelector('form[dusk="form-profil"]'))->submit();

            $browser->waitForText('Nomor BPJS wajib diisi untuk pasien BPJS.')
                ->assertSee('Nomor BPJS wajib diisi untuk pasien BPJS.');
        });
    }
}

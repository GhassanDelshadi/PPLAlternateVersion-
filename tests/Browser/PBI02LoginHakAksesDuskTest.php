<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI02LoginHakAksesDuskTest extends DuskTestCase
{
    /**
     * Skenario Normal PBI-02:
     * Pasien login dengan kredensial benar dan dapat mengakses dashboard pasien.
     */
    public function test_normal_pasien_berhasil_login_dan_masuk_ke_dashboard_pasien(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil')
                ->pause(500)
                ->visit('/pasien/dashboard')
                ->waitForText('Halo, Budi Santoso!')
                ->assertSee('Ambil Antrean');
        });
    }

    /**
     * Skenario Exception PBI-02:
     * Login gagal jika password salah, pesan error ditampilkan.
     */
    public function test_exception_login_gagal_jika_password_salah(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'passwordsalah123')
                ->press('button[type="submit"]')
                ->waitForText('Nomor telepon atau password salah.')
                ->assertSee('Nomor telepon atau password salah.');
        });
    }

    /**
     * Skenario Exception PBI-02 (Hak Akses):
     * Pengguna dengan role pasien dilarang mengakses halaman admin (HTTP 403).
     */
    public function test_exception_pasien_dilarang_mengakses_halaman_admin(): void
    {
        $this->browse(function (Browser $browser) {
            // Login Pasien
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil')
                ->pause(500);

            // Coba paksa akses URL admin
            $browser->visit('/admin/dashboard')
                ->assertSee('403');
        });
    }
}

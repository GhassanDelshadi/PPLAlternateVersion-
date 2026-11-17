<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI01RegistrasiDuskTest extends DuskTestCase
{
    /**
     * Skenario Normal PBI-01:
     * Pasien mendaftar akun baru dengan data valid.
     * Hasil: Akun baru otomatis tersimpan di tabel 'users' dengan role 'pasien'.
     */
    public function test_normal_pasien_berhasil_registrasi_akun_baru_dan_tersimpan_di_database(): void
    {
        $emailUji = 'pasien.baru.dusk@bonamu.com';
        $noHpUji = '081288887777';

        User::where('email', $emailUji)->orWhere('no_hp', $noHpUji)->delete();

        $this->browse(function (Browser $browser) use ($emailUji, $noHpUji) {
            $browser->visit('/register')
                ->waitForText('Registrasi Pasien Baru')
                ->type('name', 'Pasien Dusk Pratama')
                ->type('email', $emailUji)
                ->type('no_hp', $noHpUji)
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('button[type="submit"]')
                ->waitForLocation('/login')
                ->assertSee('Registrasi berhasil');
        });

        // Pembuktian Database MySQL
        $this->assertDatabaseHas('users', [
            'email' => $emailUji,
            'no_hp' => $noHpUji,
            'role' => 'pasien',
        ]);
    }

    /**
     * Skenario Exception PBI-01:
     * Registrasi ditolak jika email sudah terdaftar di sistem.
     */
    public function test_exception_registrasi_ditolak_jika_email_sudah_digunakan(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->waitForText('Registrasi Pasien Baru')
                ->type('name', 'Pasien Duplikat')
                ->type('email', 'pasien@test.com') // Email bawaan seeder
                ->type('no_hp', '081299990001')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('button[type="submit"]')
                ->waitForText('Email sudah digunakan.')
                ->assertSee('Email sudah digunakan.');
        });
    }
}

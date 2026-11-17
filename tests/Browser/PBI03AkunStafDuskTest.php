<?php

namespace Tests\Browser;

use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI03AkunStafDuskTest extends DuskTestCase
{
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
    }

    /**
     * Skenario PBI-03 (Manajemen Akun Staf):
     * 1. Login sebagai Administrator
     * 2. Buka menu Akun Staf (/admin/akun)
     * 3. Buka form Buat Akun Baru (/admin/akun/create)
     * 4. Isi data staf dokter baru: Nama, Email, No HP, Role Dokter, Password
     * 5. Simpan dan verifikasi akun baru muncul di tabel
     * 6. Pembuktian Database: User tersimpan nyata di tabel MySQL 'users'
     */
    public function test_admin_membuat_akun_staf_dokter_baru_dan_tersimpan_di_database(): void
    {
        $testEmail = 'dr.dusk.test@bonamu.com';
        User::where('email', $testEmail)->delete();

        $this->browse(function (Browser $browser) use ($testEmail) {
            // 1. Login Admin
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081234567890')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // 2. Buka menu Akun Staf
            $browser->visit('/admin/akun')
                ->waitForText('Daftar Akun Dokter & Admin')
                ->visit('/admin/akun/create');

            // 3. Masuk ke form Buat Akun Baru
            $browser->waitForText('Buat Akun Staf Baru')
                ->select('role', 'dokter')
                ->value('input[name="name"]', 'dr. Dusk Automation')
                ->value('input[name="email"]', $testEmail)
                ->value('input[name="no_hp"]', '081999888777')
                ->value('input[name="password"]', 'Password123')
                ->value('input[name="password_confirmation"]', 'Password123');

            // 4. Submit form
            $browser->driver->findElement(WebDriverBy::cssSelector('form[dusk="form-buat-akun"]'))->submit();

            // 5. Verifikasi di browser
            $browser->waitForText('Daftar Akun Dokter & Admin')
                ->assertSee('dr. Dusk Automation')
                ->assertSee($testEmail)
                ->assertSee('DOKTER');
        });

        // 6. Pembuktian Database MySQL
        $this->assertDatabaseHas('users', [
            'email' => $testEmail,
            'name' => 'dr. Dusk Automation',
            'role' => 'dokter',
        ]);
    }

    /**
     * Skenario Exception PBI-03:
     * Pembuatan akun staf gagal jika email sudah terdaftar.
     */
    public function test_exception_pembuatan_akun_staf_ditolak_jika_email_sudah_digunakan(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/akun/create')
                ->waitForText('Buat Akun Staf Baru')
                ->select('role', 'dokter')
                ->value('input[name="name"]', 'dr. Duplikat Staf')
                ->value('input[name="email"]', 'admin@test.com') // Email admin sudah ada
                ->value('input[name="no_hp"]', '081299990002')
                ->value('input[name="password"]', 'Password123')
                ->value('input[name="password_confirmation"]', 'Password123');

            $browser->driver->findElement(WebDriverBy::cssSelector('form[dusk="form-buat-akun"]'))->submit();

            $browser->waitForText('Email sudah digunakan.')
                ->assertSee('Email sudah digunakan.');
        });
    }
}

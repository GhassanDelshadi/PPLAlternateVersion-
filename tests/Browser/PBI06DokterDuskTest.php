<?php

namespace Tests\Browser;

use App\Models\Doctor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI06DokterDuskTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan akun dokter dr. Andi tersedia
        $userDoc = User::updateOrCreate(
            ['email' => 'dokter.andi@test.com'],
            [
                'name' => 'dr. Andi',
                'no_hp' => '081200000001',
                'password' => bcrypt('password'),
                'role' => 'dokter',
                'is_active' => true,
            ]
        );

        Doctor::firstOrCreate(
            ['user_id' => $userDoc->id],
            ['nomor_sip' => 'SIP-0001']
        );
    }

    /**
     * Skenario Normal PBI-06:
     * Admin dapat melihat daftar tenaga medis dokter terdaftar di sistem.
     */
    public function test_normal_admin_melihat_daftar_data_dokter_dan_status_aktif(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081234567890')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil')
                ->visit('/admin/akun')
                ->waitForText('Daftar Akun Dokter & Admin')
                ->assertSee('dr. Andi')
                ->assertSee('DOKTER');
        });

        // Pembuktian Database MySQL
        $this->assertDatabaseHas('users', [
            'email' => 'dokter.andi@test.com',
            'role' => 'dokter',
            'is_active' => 1,
        ]);
    }

    /**
     * Skenario Exception PBI-06:
     * Admin tidak boleh membuat akun staf dengan role pasien (ditolak aturan bisnis).
     */
    public function test_exception_admin_dilarang_membuat_akun_staf_dengan_role_pasien(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/akun/create')
                ->waitForText('Buat Akun Staf Baru')
                ->assertSelectHasOptions('role', ['dokter', 'admin'])
                ->assertSelectMissingOption('role', 'pasien');
        });
    }
}

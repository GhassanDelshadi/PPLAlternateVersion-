<?php

namespace Tests\Browser;

use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Poli;
use App\Models\User;
use App\Services\QueueService;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI08LihatJadwalDuskTest extends DuskTestCase
{
    private User $pasien;
    private DoctorSchedule $jadwal;
    private string $tanggalAdaJadwal;
    private string $tanggalTidakAdaJadwal;

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

        PatientProfile::updateOrCreate(
            ['user_id' => $this->pasien->id],
            [
                'tanggal_lahir' => '1995-01-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Sukabirus No. 12 Bandung',
                'jenis_pasien' => 'UMUM',
            ]
        );

        $this->jadwal = DoctorSchedule::where('status', true)->firstOrFail();

        // Cari tanggal yang harinya ADA jadwal
        $hariAda = strtolower($this->jadwal->hari);
        $date = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            if (QueueService::hariDariTanggal($date->toDateString()) === $hariAda) {
                break;
            }
            $date->addDay();
        }
        $this->tanggalAdaJadwal = $date->toDateString();

        // Cari tanggal yang harinya TIDAK ADA jadwal untuk dokter ini
        $allHariJadwal = DoctorSchedule::where('poli_id', $this->jadwal->poli_id)->pluck('hari')->map(fn($h) => strtolower($h))->toArray();
        $dateKosong = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            $hariCek = QueueService::hariDariTanggal($dateKosong->toDateString());
            if (!in_array($hariCek, $allHariJadwal, true)) {
                break;
            }
            $dateKosong->addDay();
        }
        $this->tanggalTidakAdaJadwal = $dateKosong->toDateString();
    }

    /**
     * Skenario Normal PBI-08:
     * Pasien dapat melihat jadwal dokter yang aktif, jam praktik, dan sisa kuota antrean per poli.
     */
    public function test_normal_pasien_melihat_jadwal_praktik_dokter_aktif(): void
    {
        $poliId = $this->jadwal->poli_id;
        $tanggal = $this->tanggalAdaJadwal;

        $this->browse(function (Browser $browser) use ($poliId, $tanggal) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda')
                ->type('no_hp', '081299999999')
                ->type('password', 'password')
                ->press('button[type="submit"]')
                ->waitForText('Login berhasil');

            // Buka halaman jadwal / antrean poli
            $browser->visit(route('antrean.create', ['poli' => $poliId, 'tanggal' => $tanggal]))
                ->waitForText('Pendaftaran Antrean Online')
                ->assertSee('Dokter Berpraktik')
                ->assertSee($this->jadwal->doctor->user->name)
                ->assertSee('WIB');
        });
    }

    /**
     * Skenario Exception PBI-08:
     * Pasien memilih poli yang belum memiliki jadwal dokter, sistem menampilkan pesan informatif.
     */
    public function test_exception_pasien_melihat_jadwal_kosong_pada_hari_tanpa_praktik(): void
    {
        $poliKosong = Poli::firstOrCreate(
            ['kode_poli' => 'PRAD'],
            [
                'nama_poli' => 'Poli Radiologi Dusk',
                'klinik_cabang_id' => 1,
                'status' => true,
            ]
        );

        $this->browse(function (Browser $browser) use ($poliKosong) {
            $browser->visit(route('antrean.create', ['poli' => $poliKosong->id]))
                ->waitForText('Pendaftaran Antrean Online')
                ->assertSee('Tidak Ada Praktik Dokter');
        });
    }
}

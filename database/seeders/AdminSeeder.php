<?php

namespace Database\Seeders;

use App\Models\KlinikCabang;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk Akun Administrator & Staf Admin Bonamu.
 * Jalankan: php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguarded(function () {
            // Pastikan klinik cabang utama tersedia
            KlinikCabang::firstOrCreate(
                ['nama_cabang' => 'Klinik Bonamu Pusat'],
                [
                    'alamat' => 'Jl. Contoh No. 1, Bandung',
                    'nomor_telepon' => '0221234567',
                    'status' => true,
                ]
            );

            // Daftar akun Admin default untuk kebutuhan login dan pengujian
            $daftarAdmin = [
                [
                    'name' => 'Admin Bonamu',
                    'email' => 'admin@test.com',
                    'no_hp' => '081234567890',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'is_active' => true,
                ],
                [
                    'name' => 'Staf Admin Klinik',
                    'email' => 'admin.staf@test.com',
                    'no_hp' => '081234567899',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'is_active' => true,
                ],
            ];

            foreach ($daftarAdmin as $data) {
                User::updateOrCreate(
                    ['email' => $data['email']],
                    $data
                );
            }
        });
    }
}

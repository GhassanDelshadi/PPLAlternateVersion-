<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\PatientProfileController;
use App\Http\Controllers\Api\PoliController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes Bonamu (PBI-01 s/d PBI-12)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (Tanpa Autentikasi)
// ==========================================
Route::prefix('v1')->group(function () {

    // PBI-01: Registrasi Akun Pasien Baru
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');

    // PBI-02: Login Autentikasi Pengguna (Mendapatkan Bearer Token Sanctum)
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    // PBI-05: Tampilkan Data Poli Aktif
    Route::get('/polis', [PoliController::class, 'index'])->name('api.polis.index');
    Route::get('/polis/{id}', [PoliController::class, 'show'])->name('api.polis.show');

    // PBI-06: Tampilkan Daftar Dokter Aktif
    Route::get('/doctors', [DoctorController::class, 'index'])->name('api.doctors.index');
    Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('api.doctors.show');

    // PBI-08: Tampilkan & Filter Jadwal Praktik Dokter
    Route::get('/jadwal', [DoctorScheduleController::class, 'index'])->name('api.jadwal.index');
    Route::get('/jadwal/{id}', [DoctorScheduleController::class, 'show'])->name('api.jadwal.show');

    // PBI-12: Tampilkan Daftar Layanan & Tarif Klinik
    Route::get('/layanan', [LayananController::class, 'index'])->name('api.layanan.index');
    Route::get('/layanan/{id}', [LayananController::class, 'show'])->name('api.layanan.show');

    // ==========================================
    // 2. PROTECTED ROUTES (Wajib Bearer Token)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // User Info & Logout
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        // PBI-04: Profil Pasien
        Route::get('/pasien/profil', [PatientProfileController::class, 'show'])->name('api.pasien.profil.show');
        Route::put('/pasien/profil', [PatientProfileController::class, 'update'])->name('api.pasien.profil.update');

        // PBI-09: Antrean Online Pasien
        Route::get('/antrean', [QueueController::class, 'index'])->name('api.antrean.index');
        Route::post('/antrean', [QueueController::class, 'store'])->name('api.antrean.store');
        Route::get('/antrean/{id}', [QueueController::class, 'show'])->name('api.antrean.show');

        // PBI-10 & PBI-11: Konsultasi Online Medis
        Route::get('/konsultasi', [ConsultationController::class, 'index'])->name('api.konsultasi.index');
        Route::post('/konsultasi', [ConsultationController::class, 'store'])->name('api.konsultasi.store');
        Route::get('/konsultasi/{id}', [ConsultationController::class, 'show'])->name('api.konsultasi.show');
        Route::post('/konsultasi/{id}/pesan', [ConsultationController::class, 'sendMessage'])->name('api.konsultasi.pesan');

        // PBI-11: Dokter Memperbarui Status Sesi Konsultasi
        Route::patch('/dokter/konsultasi/{id}/status', [ConsultationController::class, 'updateStatus'])
            ->middleware('role:dokter,admin')
            ->name('api.dokter.konsultasi.status');

        // ==========================================
        // 3. ADMIN ONLY ROUTES (role:admin)
        // ==========================================
        Route::middleware('role:admin')->prefix('admin')->group(function () {

            // PBI-03: Pembuatan Akun Dokter / Staf Admin Baru
            Route::post('/users', [UserManagementController::class, 'store'])->name('api.admin.users.store');

            // PBI-05: Kelola Master Data Poli
            Route::post('/polis', [PoliController::class, 'store'])->name('api.admin.polis.store');
            Route::put('/polis/{id}', [PoliController::class, 'update'])->name('api.admin.polis.update');
            Route::patch('/polis/{id}/toggle-status', [PoliController::class, 'toggleStatus'])->name('api.admin.polis.toggle');

            // PBI-07: Kelola Jadwal Praktik Dokter
            Route::post('/jadwal', [DoctorScheduleController::class, 'store'])->name('api.admin.jadwal.store');
            Route::put('/jadwal/{id}', [DoctorScheduleController::class, 'update'])->name('api.admin.jadwal.update');
            Route::patch('/jadwal/{id}/toggle-status', [DoctorScheduleController::class, 'toggleStatus'])->name('api.admin.jadwal.toggle');
            Route::delete('/jadwal/{id}', [DoctorScheduleController::class, 'destroy'])->name('api.admin.jadwal.destroy');

            // Monitoring Antrean Pasien & Ubah Status (waiting, serving, completed)
            Route::get('/antrean', [QueueController::class, 'index'])->name('api.admin.antrean.index');
            Route::patch('/antrean/{id}/status', [QueueController::class, 'updateStatus'])->name('api.admin.antrean.status');

            // PBI-12: Kelola Master Data Layanan & Tarif
            Route::post('/layanan', [LayananController::class, 'store'])->name('api.admin.layanan.store');
            Route::put('/layanan/{id}', [LayananController::class, 'update'])->name('api.admin.layanan.update');
            Route::patch('/layanan/{id}/toggle-status', [LayananController::class, 'toggleStatus'])->name('api.admin.layanan.toggle');
        });
    });
});

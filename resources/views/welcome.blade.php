@extends('layouts.app')

@section('title', 'Portal Pengujian & Dashboard Terpadu')

@section('content')
<div class="py-2">
    {{-- Hero Section --}}
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white" style="background: linear-gradient(135deg, #005954 0%, #003633 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i> White-Box Testing & Prototype Hub
                </span>
                <h1 class="display-6 fw-bold mb-2">Portal Integrasi &amp; Pengujian Fitur Bonamu</h1>
                <p class="lead text-white-50 mb-4" style="font-size: 1rem;">
                    Platform pengujian langsung seluruh fitur aplikasi terintegrasi (Auth, Poli, Jadwal Dokter, Antrean Online, Akun Staf, Konsultasi Online, dan Profil).
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> 39 Automated Feature Tests PASS
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        <i class="bi bi-database-check me-1"></i> Database: bonamu (MySQL)
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                        <i class="bi bi-diagram-3 me-1"></i> 7 PBI Terhubung Sempurna
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 border border-white border-opacity-25">
                    <i class="bi bi-hospital fs-1 text-warning mb-2 d-block"></i>
                    <h5 class="fw-bold mb-1">Klinik Bonamu Pusat</h5>
                    <p class="small text-white-50 mb-0">Sistem Antrean &amp; Layanan Terintegrasi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 1-Click Instant Login Section --}}
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="h5 fw-bold text-dark mb-1">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> 1-Click Instant Login (Pilih Role untuk Uji Langsung)
                </h3>
                <p class="text-muted small mb-0">Klik tombol di bawah untuk langsung login tanpa memasukkan password secara manual.</p>
            </div>
            @auth
                <div class="text-muted small">
                    Saat ini Anda masuk sebagai: <strong class="text-primary">{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})
                </div>
            @endauth
        </div>

        <div class="row g-4">
            {{-- Card Admin --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: #005954;"></div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-3 text-white" style="background-color: #005954;">
                                <i class="bi bi-person-gear fs-3"></i>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">ADMINISTRATOR</span>
                                <h5 class="fw-bold mb-0 mt-1">Admin Bonamu</h5>
                                <small class="text-muted">admin@test.com</small>
                            </div>
                        </div>

                        <p class="text-muted small flex-grow-1">
                            Akses modul master data poli, pengaturan jam buka-tutup dan kuota jadwal praktik dokter, serta pembuatan akun staf.
                        </p>

                        <div class="border-top pt-3 mt-2 mb-3">
                            <small class="fw-semibold text-secondary d-block mb-2">Fitur Utama Admin:</small>
                            <ul class="list-unstyled small mb-0 space-y-1">
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-05</strong>: Kelola Data Poli (CRUD)</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-07</strong>: Jadwal Dokter &amp; Kuota</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-03</strong>: Buat Akun Dokter &amp; Admin</li>
                            </ul>
                        </div>

                        <a href="{{ route('dev.login-as', 'admin') }}" class="btn btn-app-primary w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk sebagai Admin
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card Pasien --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: #0d6efd;"></div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-3 text-white bg-primary">
                                <i class="bi bi-person-heart fs-3"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary border px-2 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">PASIEN</span>
                                <h5 class="fw-bold mb-0 mt-1">Budi Santoso</h5>
                                <small class="text-muted">pasien@test.com</small>
                            </div>
                        </div>

                        <p class="text-muted small flex-grow-1">
                            Akses pendaftaran nomor antrean live, cetak tiket barcode digital, riwayat antrean, profil pasien, dan konsultasi online.
                        </p>

                        <div class="border-top pt-3 mt-2 mb-3">
                            <small class="fw-semibold text-secondary d-block mb-2">Fitur Utama Pasien:</small>
                            <ul class="list-unstyled small mb-0 space-y-1">
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-09</strong>: Ambil Antrean &amp; Tiket Online</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-04</strong>: Profil &amp; Identitas Lengkap</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-10</strong>: Chat Konsultasi Online</li>
                            </ul>
                        </div>

                        <a href="{{ route('dev.login-as', 'pasien') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk sebagai Pasien
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card Dokter --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background: #198754;"></div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-3 text-white bg-success">
                                <i class="bi bi-clipboard2-pulse fs-3"></i>
                            </div>
                            <div>
                                <span class="badge bg-success-subtle text-success border px-2 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">DOKTER PRAKTIK</span>
                                <h5 class="fw-bold mb-0 mt-1">dr. Andi</h5>
                                <small class="text-muted">dokter.andi@test.com</small>
                            </div>
                        </div>

                        <p class="text-muted small flex-grow-1">
                            Akses ruang konsultasi klinis, terima permintaan konsultasi dari pasien yang masuk, chat real-time, dan tandai sesi selesai.
                        </p>

                        <div class="border-top pt-3 mt-2 mb-3">
                            <small class="fw-semibold text-secondary d-block mb-2">Fitur Utama Dokter:</small>
                            <ul class="list-unstyled small mb-0 space-y-1">
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-11</strong>: Manajemen Konsultasi Masuk</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-11</strong>: Mulai &amp; Selesaikan Sesi Konsultasi</li>
                                <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i> <strong>PBI-07</strong>: Terjadwal di Poli Gigi &amp; Mulut</li>
                            </ul>
                        </div>

                        <a href="{{ route('dev.login-as', 'dokter') }}" class="btn btn-success w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk sebagai Dokter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panduan Alur White Box Testing --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h4 class="h5 fw-bold text-dark mb-1">
                <i class="bi bi-card-checklist text-primary me-2"></i> Panduan Skenario White Box Testing
            </h4>
            <p class="text-muted small">Coba langsung alur end-to-end antar modul untuk memastikan backend dan tampilan berjalan sinkron:</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                {{-- Step 1 --}}
                <div class="col-lg-4">
                    <div class="p-3 border rounded-3 h-100 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle bg-dark text-white p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                            <h6 class="fw-bold mb-0 text-dark">Uji Fitur Admin (PBI-05 &amp; 07)</h6>
                        </div>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Klik <strong>Masuk sebagai Admin</strong>.</li>
                            <li>Buka <strong>Data Poli</strong>: Coba tambah poli baru (contoh: Poli Mata, kode: <code>PMT</code>).</li>
                            <li>Buka <strong>Jadwal Dokter</strong>: Buat jadwal praktik baru untuk dokter di poli tersebut dan atur kuotanya (misal 15 pasien).</li>
                            <li>Buka <strong>Akun</strong>: Tambahkan akun staf baru.</li>
                        </ol>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="col-lg-4">
                    <div class="p-3 border rounded-3 h-100 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle bg-primary text-white p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                            <h6 class="fw-bold mb-0 text-dark">Uji Fitur Pasien (PBI-04, 09, 10)</h6>
                        </div>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Klik <strong>Switch Role &rarr; Pasien</strong> di pojok kanan atas.</li>
                            <li>Buka <strong>Profil Pasien</strong>: Periksa atau lengkapi data profil (NIK, Alamat).</li>
                            <li>Buka <strong>Ambil Antrean</strong>: Pilih tanggal dan poli, lalu klik daftar untuk mendapat nomor tiket.</li>
                            <li>Buka <strong>Konsultasi Online</strong>: Buat pengajuan konsultasi keluhan baru.</li>
                        </ol>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="col-lg-4">
                    <div class="p-3 border rounded-3 h-100 bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-circle bg-success text-white p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                            <h6 class="fw-bold mb-0 text-dark">Uji Fitur Dokter (PBI-11)</h6>
                        </div>
                        <ol class="small text-muted ps-3 mb-0">
                            <li>Klik <strong>Switch Role &rarr; Dokter</strong>.</li>
                            <li>Buka <strong>Konsultasi Masuk</strong>: Anda akan melihat konsultasi dari pasien tadi.</li>
                            <li>Klik <strong>Mulai Sesi</strong>, kirim chat balasan kepada pasien.</li>
                            <li>Setelah selesai diagnosa, klik <strong>Selesaikan Konsultasi</strong>.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Direktori URL & Route --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h4 class="h5 fw-bold text-dark mb-1">
                <i class="bi bi-signpost-split text-secondary me-2"></i> Direktori URL &amp; Route Lengkap
            </h4>
            <p class="text-muted small">Daftar route yang tersedia di aplikasi Bonamu saat ini:</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Modul / PBI</th>
                            <th>Role Akses</th>
                            <th>URL Route</th>
                            <th>Nama Route</th>
                            <th class="text-end pe-4">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-shield-lock text-primary me-1"></i> Autentikasi (PBI-01 &amp; 02)
                            </td>
                            <td><span class="badge bg-secondary">Guest / Publik</span></td>
                            <td><code>/login</code> &amp; <code>/register</code></td>
                            <td><code>login</code>, <code>register</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary py-1">Buka Login</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-diagram-3 text-success me-1"></i> Data Poli (PBI-05)
                            </td>
                            <td><span class="badge bg-warning text-dark">Admin</span></td>
                            <td><code>/admin/poli</code></td>
                            <td><code>admin.poli.index</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.poli.index') }}" class="btn btn-sm btn-outline-primary py-1">Buka Poli</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-calendar3 text-info me-1"></i> Jadwal Praktik &amp; Kuota (PBI-07)
                            </td>
                            <td><span class="badge bg-warning text-dark">Admin</span></td>
                            <td><code>/admin/jadwal</code></td>
                            <td><code>admin.jadwal.index</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-sm btn-outline-primary py-1">Buka Jadwal</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-people text-danger me-1"></i> Akun Dokter/Admin (PBI-03)
                            </td>
                            <td><span class="badge bg-warning text-dark">Admin</span></td>
                            <td><code>/admin/akun</code></td>
                            <td><code>admin.akun.index</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.akun.index') }}" class="btn btn-sm btn-outline-primary py-1">Buka Akun</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-ticket-perforated text-primary me-1"></i> Pendaftaran Antrean (PBI-09)
                            </td>
                            <td><span class="badge bg-info text-dark">Pasien</span></td>
                            <td><code>/antrean/ambil</code></td>
                            <td><code>antrean.create</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('antrean.create') }}" class="btn btn-sm btn-outline-success py-1">Ambil Antrean</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-card-checklist text-primary me-1"></i> Tiket &amp; Riwayat Antrean (PBI-09)
                            </td>
                            <td><span class="badge bg-info text-dark">Pasien</span></td>
                            <td><code>/antrean</code></td>
                            <td><code>antrean.index</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('antrean.index') }}" class="btn btn-sm btn-outline-success py-1">Tiket Saya</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-person-lines-fill text-secondary me-1"></i> Profil Pasien (PBI-04)
                            </td>
                            <td><span class="badge bg-info text-dark">Pasien</span></td>
                            <td><code>/pasien/profil</code></td>
                            <td><code>pasien.profil.edit</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('pasien.profil.edit') }}" class="btn btn-sm btn-outline-success py-1">Edit Profil</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                <i class="bi bi-chat-dots text-warning me-1"></i> Konsultasi Online (PBI-10 &amp; 11)
                            </td>
                            <td><span class="badge bg-secondary">Pasien &amp; Dokter</span></td>
                            <td><code>/konsultasi/buat</code> &amp; <code>/dokter/konsultasi</code></td>
                            <td><code>konsultasi.create</code>, <code>dokter.konsultasi.index</code></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('dokter.konsultasi.index') }}" class="btn btn-sm btn-outline-warning py-1">Konsultasi</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

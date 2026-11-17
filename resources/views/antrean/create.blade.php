@extends('layouts.app')

@section('title', 'Ambil Antrean Online — Bonamu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        {{-- Header Halaman --}}
        <div class="mb-4">
            <span class="badge-tag">LAYANAN ANTREAN</span>
            <h1 class="h3 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Pendaftaran Antrean Online</h1>
            <p class="text-muted small mb-0">Pilih poliklinik, tanggal kunjungan, dan dokter praktik untuk mendapatkan nomor antrean klinik</p>
        </div>

        {{-- Cek Profil Lengkap --}}
        @if(!$profil || !$profil->isLengkap())
            <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center mb-4 p-3" style="background-color: #fffbeb; border: 1px solid #fde68a !important;">
                <i class="bi bi-person-exclamation fs-3 text-warning me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1 text-dark">Identitas Profil Belum Lengkap</h6>
                    <p class="small mb-0 text-muted">
                        Untuk mengambil nomor antrean online, Anda wajib melengkapi data profil pasien (tanggal lahir, jenis kelamin, dan alamat).
                    </p>
                </div>
            </div>
        @endif

        {{-- Cek Antrean Aktif Ganda --}}
        @if($antreanAktif)
            <div class="alert border-0 shadow-sm rounded-4 d-flex align-items-center justify-content-between mb-4 p-3" style="background-color: #f0fdf9; border: 1px solid #ccfbf1 !important;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-3 me-3" style="color: var(--app-primary);"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1" style="color: var(--app-primary);">Anda Sudah Memiliki Antrean Aktif</h6>
                        <p class="small mb-0 text-muted">
                            Nomor Antrean Anda: <strong class="badge rounded-pill px-3 py-1 text-white" style="background-color: var(--app-primary); font-size: 0.85rem;">{{ $antreanAktif->formatted_nomor }}</strong> 
                            ({{ $antreanAktif->doctorSchedule->poli->nama_poli ?? 'Poli' }} - {{ $antreanAktif->doctorSchedule->doctor->user->name ?? 'Dokter' }})
                            pada tanggal {{ \Carbon\Carbon::parse($antreanAktif->tanggal_antrean)->translatedFormat('d F Y') }}.
                        </p>
                    </div>
                </div>
                <a href="{{ route('antrean.show', $antreanAktif) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold text-nowrap ms-3">
                    <i class="bi bi-ticket-detailed me-1"></i> Lihat Tiket
                </a>
            </div>
        @endif

        <!-- Card Pemilihan Tanggal dan Poli -->
        <div class="card-mockup shadow-sm mb-4">
            <div class="card-mockup-body p-4">
                <form method="GET" action="{{ route('antrean.create') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-secondary">Rencana Tanggal Kunjungan</label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="{{ $tanggalString }}" min="{{ date('Y-m-d') }}" onchange="this.form.submit()" dusk="input-pilih-tanggal">
                        <div class="form-text small" style="font-size: 0.775rem;">Hari terpilih: <strong>{{ ucfirst($namaHari) }}</strong>, {{ \Carbon\Carbon::parse($tanggalString)->translatedFormat('d F Y') }}</div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-secondary">Poli Klinik</label>
                        <select name="poli" class="form-select rounded-3" onchange="this.form.submit()" dusk="select-pilih-poli">
                            @foreach($polis as $poli)
                                <option value="{{ $poli->id }}" {{ $poliId == $poli->id ? 'selected' : '' }}>
                                    {{ $poli->nama_poli }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small" style="font-size: 0.775rem;">Daftar dokter yang berpraktik akan menyesuaikan poli yang dipilih.</div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary rounded-pill w-100 fw-semibold">
                            <i class="bi bi-arrow-repeat me-1"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Jadwal Praktik Dokter -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-calendar2-check me-2" style="color: var(--app-primary);"></i>
                Dokter Berpraktik Hari {{ ucfirst($namaHari) }}
            </h5>
            <small class="text-muted">{{ count($schedules) }} sesi dokter ditemukan</small>
        </div>

        <div class="row g-3">
            @forelse($schedules as $item)
                @php
                    $jadwal = $item['schedule'];
                    $sisa = $item['sisa_kuota'];
                    $penuh = $item['is_penuh'];
                    $bisaAmbil = $profil && $profil->isLengkap() && !$antreanAktif && !$penuh;
                @endphp
                <div class="col-md-6">
                    <div class="card-mockup h-100 shadow-sm {{ $penuh ? 'opacity-75 bg-light' : '' }}" style="border-radius: 16px;">
                        <div class="card-mockup-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">{{ $jadwal->doctor->user->name ?? 'Dokter' }}</h5>
                                        <span class="badge rounded-pill px-2.5 py-1" style="background-color: var(--app-primary-light); color: var(--app-primary); font-size: 0.75rem; font-weight: 600;">
                                            <i class="bi bi-hospital me-1"></i> {{ $jadwal->poli->nama_poli ?? 'Poli' }}
                                        </span>
                                    </div>
                                    @if($penuh)
                                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fee2e2; color: #b91c1c;">
                                            <i class="bi bi-x-circle me-1"></i> Jadwal Penuh
                                        </span>
                                    @else
                                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #dcfce7; color: #15803d;">
                                            <i class="bi bi-check-circle me-1"></i> Tersedia
                                        </span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                                    SIP: <span class="font-mono">{{ $jadwal->doctor->nomor_sip ?? 'SIP Belum Tercatat' }}</span>
                                </p>

                                <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span><i class="bi bi-clock me-1"></i> Jam Praktik:</span>
                                        <strong class="text-dark">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB
                                        </strong>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span><i class="bi bi-people me-1"></i> Kuota Antrean:</span>
                                        <span>{{ $jadwal->kuota }} pasien (Terisi: {{ $item['terpakai'] }})</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><i class="bi bi-ticket me-1"></i> Sisa Kuota:</span>
                                        <strong class="{{ $penuh ? 'text-danger' : 'text-success' }}">{{ $sisa }} nomor lagi</strong>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('antrean.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="doctor_schedule_id" value="{{ $jadwal->id }}">
                                <input type="hidden" name="tanggal_antrean" value="{{ $tanggalString }}">

                                @if($penuh)
                                    <button type="button" class="btn btn-secondary rounded-pill w-100 py-2" disabled>
                                        <i class="bi bi-slash-circle me-1"></i> Jadwal Penuh
                                    </button>
                                @elseif(!$profil || !$profil->isLengkap())
                                    <button type="button" class="btn btn-outline-warning rounded-pill w-100 py-2" disabled title="Lengkapi profil dahulu">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Lengkapi Profil Pasien
                                    </button>
                                @elseif($antreanAktif)
                                    <button type="button" class="btn btn-outline-secondary rounded-pill w-100 py-2" disabled title="Selesaikan antrean aktif Anda terlebih dahulu">
                                        <i class="bi bi-lock me-1"></i> Ada Antrean Aktif
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm" dusk="btn-ambil-antrean-{{ $jadwal->id }}" onclick="return confirm('Ambil nomor antrean untuk {{ $jadwal->doctor->user->name ?? 'dokter' }} pada {{ \Carbon\Carbon::parse($tanggalString)->translatedFormat('d F Y') }}?')">
                                        <i class="bi bi-ticket-perforated me-1"></i> Ambil Nomor Antrean
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card-mockup text-center py-5 shadow-sm">
                        <div class="card-mockup-body text-muted">
                            <i class="bi bi-calendar-x fs-1 text-secondary d-block mb-2"></i>
                            <h6 class="fw-semibold text-dark">Tidak Ada Praktik Dokter</h6>
                            <p class="small mb-0">Pada hari {{ ucfirst($namaHari) }}, tidak ada dokter yang berpraktik di poli ini. Silakan pilih tanggal atau poli lainnya.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

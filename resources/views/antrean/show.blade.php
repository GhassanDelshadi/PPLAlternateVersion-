@extends('layouts.app')

@section('title', 'Tiket Antrean Pasien — Bonamu')

@section('content')
<div class="row justify-content-center py-2">
    <div class="col-md-8 col-lg-6 col-xl-5">
        {{-- Tombol Aksi Navigasi --}}
        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
            <a href="{{ route('antrean.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Antrean Saya
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak Tiket
            </button>
        </div>

        {{-- Kartu Tiket Antrean Klinis --}}
        <div class="card-mockup shadow-sm overflow-hidden" id="areaTiket" style="border-radius: 20px; border: 1px solid #dbeae3;">
            {{-- Header Tiket Medis --}}
            <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #005954 0%, #004743 100%);">
                <div class="d-inline-flex align-items-center justify-content-center p-2 rounded-circle bg-white bg-opacity-15 mb-2">
                    <i class="bi bi-hospital fs-4 text-white"></i>
                </div>
                <h5 class="fw-bold mb-0" style="letter-spacing: -0.01em;">KLINIK BONA MITRA KELUARGA</h5>
                <p class="small text-white-50 mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Bukti Pendaftaran Antrean Online</p>
            </div>

            {{-- Body Tiket --}}
            <div class="card-mockup-body p-4 text-center">
                {{-- Kategori Poli --}}
                <div class="mb-2">
                    <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase" style="background-color: var(--app-primary-light); color: var(--app-primary); font-size: 0.75rem; letter-spacing: 0.05em;">
                        <i class="bi bi-clipboard2-pulse me-1"></i> {{ $queue->doctorSchedule->poli->nama_poli ?? 'POLI KLINIK' }}
                    </span>
                </div>

                {{-- Label Nomor Antrean --}}
                <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.75rem; letter-spacing: 0.08em;">Nomor Antrean Anda</span>

                {{-- Angka Tiket Raksasa & Jelas --}}
                <div class="font-mono fw-bold my-2 text-dark" style="font-size: 60px; line-height: 1; letter-spacing: -0.04em; color: var(--app-primary) !important;" dusk="nomor-antrean-display">
                    {{ $queue->formatted_nomor }}
                </div>

                {{-- Status Pill --}}
                <div class="mb-3">
                    @if($queue->status === 'waiting')
                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                            <i class="bi bi-hourglass-split me-1"></i> Status: {{ $queue->status_label }}
                        </span>
                    @elseif($queue->status === 'called' || $queue->status === 'serving')
                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                            <i class="bi bi-megaphone-fill me-1"></i> Sedang Dipanggil / Dilayani
                        </span>
                    @else
                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #e2e8f0; color: #475569;">
                            Status: {{ $queue->status_label }}
                        </span>
                    @endif
                </div>

                {{-- Sisa Antrean Box --}}
                @if($antreanDiDepan > 0)
                    <div class="p-3 rounded-3 text-center mb-3" style="background-color: #f0fdf9; border: 1px solid #ccfbf1; color: #005954;">
                        <i class="bi bi-people-fill me-1"></i> Terdapat <strong>{{ $antreanDiDepan }} antrean</strong> yang masih menunggu di depan Anda.
                    </div>
                @else
                    <div class="p-3 rounded-3 text-center mb-3" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;">
                        <i class="bi bi-check-circle-fill me-1"></i> Anda adalah nomor urut pertama untuk sesi praktik ini.
                    </div>
                @endif

                {{-- Garis Pemisah Tiket --}}
                <div class="my-3 py-2 border-top border-bottom text-start" style="border-color: #edf2f0 !important;">
                    <div class="row g-2 small">
                        <div class="col-5 text-muted">Dokter Praktik</div>
                        <div class="col-7 fw-semibold text-dark">: {{ $queue->doctorSchedule->doctor->user->name ?? '-' }}</div>

                        <div class="col-5 text-muted">Tanggal Kunjungan</div>
                        <div class="col-7 fw-semibold text-dark">: {{ \Carbon\Carbon::parse($queue->tanggal_antrean)->translatedFormat('l, d F Y') }}</div>

                        <div class="col-5 text-muted">Jam Praktik</div>
                        <div class="col-7 fw-semibold text-dark">
                            : {{ \Carbon\Carbon::parse($queue->doctorSchedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($queue->doctorSchedule->jam_selesai)->format('H:i') }} WIB
                        </div>

                        <div class="col-5 text-muted">Nama Pasien</div>
                        <div class="col-7 fw-semibold text-dark">: {{ $queue->patientProfile->user->name ?? auth()->user()->name }}</div>

                        <div class="col-5 text-muted">No. Handphone</div>
                        <div class="col-7 fw-semibold text-dark">: {{ $queue->patientProfile->user->no_hp ?? auth()->user()->no_hp }}</div>
                    </div>
                </div>

                {{-- Petunjuk Pasien --}}
                <div class="text-muted small text-start p-3 rounded-3" style="background-color: #f8fafc; font-size: 0.8rem;">
                    <div class="fw-bold mb-1 text-dark d-flex align-items-center gap-1">
                        <i class="bi bi-info-circle-fill" style="color: var(--app-primary);"></i> Petunjuk Kunjungan:
                    </div>
                    <ul class="mb-0 ps-3">
                        <li>Hadir di klinik paling lambat 15 menit sebelum jam praktik dimulai.</li>
                        <li>Tunjukkan bukti nomor antrean ini kepada petugas resepsionis/loket saat tiba.</li>
                        <li>Bawa kartu identitas (KTP/KK) atau kartu BPJS (bila peserta BPJS).</li>
                    </ul>
                </div>
            </div>

            {{-- Footer Tiket --}}
            <div class="card-footer bg-light p-3 text-center text-muted small border-top" style="font-size: 0.75rem;">
                Dibuat pada: {{ $queue->created_at->format('d/m/Y H:i') }} WIB &bull; ID: <span class="font-mono">Q-{{ $queue->id }}</span>
            </div>
        </div>

        <div class="text-center mt-3 d-print-none">
            <a href="{{ route('antrean.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Selesai
            </a>
        </div>
    </div>
</div>
@endsection

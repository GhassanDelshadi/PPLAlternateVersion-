@extends('layouts.app')

@section('title', 'Daftar Antrean Saya — Bonamu')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <span class="badge-tag">STATUS PASIEN</span>
        <h1 class="h3 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Daftar Antrean Saya</h1>
        <p class="text-muted small mb-0">Pantau tiket nomor antrean aktif dan riwayat pendaftaran klinik Anda</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('antrean.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm" dusk="btn-ambil-antrean-baru">
            <i class="bi bi-plus-circle me-1"></i> Ambil Antrean Baru
        </a>
    </div>
</div>

<div class="card-mockup shadow-sm">
    <div class="card-mockup-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 140px;">No. Antrean</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Jam Praktik</th>
                        <th class="text-center">Status</th>
                        <th class="pe-3 text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues as $item)
                        <tr>
                            <td class="ps-3">
                                <span class="badge font-mono px-3 py-1.5 rounded-pill" style="background-color: var(--app-primary-light); color: var(--app-primary); font-size: 0.95rem; font-weight: 700; border: 1px solid var(--app-primary-border);">
                                    {{ $item->formatted_nomor }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->doctorSchedule->poli->nama_poli ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->doctorSchedule->doctor->user->name ?? 'Dokter' }}</div>
                                @if($item->doctorSchedule->doctor->nomor_sip)
                                    <small class="text-muted font-mono" style="font-size: 0.75rem;">SIP: {{ $item->doctorSchedule->doctor->nomor_sip }}</small>
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($item->tanggal_antrean)->translatedFormat('d F Y') }}
                            </td>
                            <td>
                                <i class="bi bi-clock me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($item->doctorSchedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->doctorSchedule->jam_selesai)->format('H:i') }} WIB
                            </td>
                            <td class="text-center">
                                @if($item->status === 'waiting')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                    </span>
                                @elseif($item->status === 'called')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                        <i class="bi bi-megaphone-fill me-1"></i> Dipanggil
                                    </span>
                                @elseif($item->status === 'serving')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                                        <i class="bi bi-activity me-1"></i> Sedang Dilayani
                                    </span>
                                @elseif($item->status === 'completed')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                        <i class="bi bi-check-circle me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fee2e2; color: #b91c1c;">
                                        Dilewati
                                    </span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('antrean.show', $item) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" dusk="lihat-tiket-{{ $item->id }}">
                                    <i class="bi bi-ticket-perforated me-1"></i> Lihat Tiket
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-ticket-detailed fs-1 text-secondary d-block mb-2"></i>
                                Belum ada antrean yang terdaftar.
                                <div class="mt-2">
                                    <a href="{{ route('antrean.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold">
                                        Ambil Nomor Antrean Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

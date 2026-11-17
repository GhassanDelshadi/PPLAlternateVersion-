@extends('layouts.app')

@section('title', 'Monitoring Antrean Pasien — Admin')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <span class="badge-tag">MONITORING KLINIK</span>
        <h1 class="h3 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Monitoring &amp; Kelola Antrean Pasien</h1>
        <p class="text-muted small mb-0">Pantau kehadiran pasien dan ubah status antrean (Menunggu &rarr; Sedang Dilayani &rarr; Selesai) secara real-time.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-mono" style="font-size: 0.8rem;">
            <i class="bi bi-calendar3 me-1 text-primary"></i> {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        </span>
    </div>
</div>

{{-- 4 Kartu Metrik Ringkas --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-mockup p-3 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Antrean</span>
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: var(--app-primary-light); color: var(--app-primary);">
                    <i class="bi bi-ticket-detailed fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-bold text-dark mb-0 font-mono">{{ $stats['total'] }}</h2>
                <small class="text-muted">Pasien</small>
            </div>
            <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.75rem;">
                Tanggal terpilih
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-mockup p-3 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Sedang Dilayani</span>
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 36px; height: 36px;">
                    <i class="bi bi-person-check fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-bold mb-0 font-mono text-success">{{ $stats['serving'] }}</h2>
                <small class="text-muted">Di Dalam Poli</small>
            </div>
            <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.75rem;">
                Pelayanan berlangsung
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-mockup p-3 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Menunggu Giliran</span>
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-warning-subtle text-warning-emphasis" style="width: 36px; height: 36px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-bold mb-0 font-mono text-warning-emphasis">{{ $stats['waiting'] }}</h2>
                <small class="text-muted">Di Ruang Tunggu</small>
            </div>
            <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.75rem;">
                Menunggu panggilan
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-mockup p-3 h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Selesai Pelayanan</span>
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-light text-secondary border" style="width: 36px; height: 36px;">
                    <i class="bi bi-check2-circle fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <h2 class="fw-bold text-dark mb-0 font-mono">{{ $stats['completed'] }}</h2>
                <small class="text-muted">Tuntas</small>
            </div>
            <div class="small text-muted mt-2 pt-2 border-top" style="font-size: 0.75rem;">
                Pemeriksaan beres
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card-mockup shadow-sm mb-4">
    <div class="card-mockup-body p-3">
        <form method="GET" action="{{ route('admin.antrean.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Tanggal Kunjungan</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm rounded-3" onchange="this.form.submit()" dusk="input-filter-tanggal">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Poli Klinik</label>
                <select name="poli_id" class="form-select form-select-sm rounded-3" onchange="this.form.submit()" dusk="select-filter-poli">
                    <option value="">Semua Poli</option>
                    @foreach($polis as $poli)
                        <option value="{{ $poli->id }}" {{ request('poli_id') == $poli->id ? 'selected' : '' }}>
                            {{ $poli->nama_poli }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">Status</label>
                <select name="status" class="form-select form-select-sm rounded-3" onchange="this.form.submit()" dusk="select-filter-status">
                    <option value="">Semua Status</option>
                    <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Menunggu</option>
                    <option value="called" {{ request('status') === 'called' ? 'selected' : '' }}>Dipanggil</option>
                    <option value="serving" {{ request('status') === 'serving' ? 'selected' : '' }}>Sedang Dilayani</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Dilewati</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Cari Pasien / No Antrean</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Ketik nama / no antrean...">
                </div>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('admin.antrean.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 31px; height: 31px;" title="Reset filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Antrean Pasien --}}
<div class="card-mockup shadow-sm">
    <div class="card-mockup-header d-flex justify-content-between align-items-center border-bottom pb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Daftar Antrean Pasien</h5>
            <small class="text-muted">Klik tombol aksi untuk langsung memajukan alur pelayanan pasien</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill font-mono">
            Total: {{ $queues->total() }} Data
        </span>
    </div>

    <div class="card-mockup-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 130px;">No. Antrean</th>
                        <th>Data Pasien</th>
                        <th>Poli &amp; Dokter</th>
                        <th>Waktu Praktik</th>
                        <th class="text-center" style="width: 170px;">Status Pelayanan</th>
                        <th class="pe-3 text-end" style="width: 250px;">Ubah Status Pelayanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues as $item)
                        <tr class="{{ $item->status === 'serving' ? 'table-success bg-opacity-25' : '' }}">
                            {{-- Nomor Antrean --}}
                            <td class="ps-3">
                                <span class="badge font-mono px-3 py-2 rounded-pill" style="background-color: var(--app-primary-light); color: var(--app-primary); font-size: 0.95rem; font-weight: 700; border: 1px solid var(--app-primary-border);" dusk="badge-antrean-{{ $item->id }}">
                                    {{ $item->doctorSchedule->poli->kode_poli ?? 'ANT' }}-{{ $item->formatted_nomor }}
                                </span>
                            </td>

                            {{-- Data Pasien --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $item->patientProfile->user->name ?? 'Pasien' }}</div>
                                <div class="small text-muted">
                                    <i class="bi bi-telephone me-1"></i> {{ $item->patientProfile->user->no_hp ?? '-' }}
                                    @if($item->patientProfile && $item->patientProfile->nomor_bpjs)
                                        &bull; <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.65rem;">BPJS: {{ $item->patientProfile->nomor_bpjs }}</span>
                                    @else
                                        &bull; <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.65rem;">UMUM</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Poli & Dokter --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->doctorSchedule->poli->nama_poli ?? 'Poli' }}</div>
                                <small class="text-muted d-block">
                                    <i class="bi bi-person-badge me-1"></i> {{ $item->doctorSchedule->doctor->user->name ?? 'Dokter' }}
                                </small>
                            </td>

                            {{-- Waktu Praktik --}}
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="bi bi-clock me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($item->doctorSchedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->doctorSchedule->jam_selesai)->format('H:i') }} WIB
                                </div>
                                <small class="text-muted font-mono" style="font-size: 0.75rem;">
                                    Daftar: {{ $item->created_at->format('H:i') }} WIB
                                </small>
                            </td>

                            {{-- Status Saat Ini --}}
                            <td class="text-center">
                                @if($item->status === 'serving')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;" dusk="status-badge-{{ $item->id }}">
                                        <i class="bi bi-activity me-1"></i> Sedang Dilayani
                                    </span>
                                @elseif($item->status === 'called')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;" dusk="status-badge-{{ $item->id }}">
                                        <i class="bi bi-megaphone-fill me-1"></i> Dipanggil
                                    </span>
                                @elseif($item->status === 'waiting')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;" dusk="status-badge-{{ $item->id }}">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                    </span>
                                @elseif($item->status === 'completed')
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" dusk="status-badge-{{ $item->id }}">
                                        <i class="bi bi-check2-all me-1 text-success"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;" dusk="status-badge-{{ $item->id }}">
                                        Dilewati
                                    </span>
                                @endif
                            </td>

                            {{-- Ubah Status Antrean --}}
                            <td class="pe-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    {{-- 1-Click Action Buttons Sesuai Skenario Pelayanan --}}
                                    @if($item->status === 'waiting')
                                        {{-- Tombol Langsung: Mulai Layani --}}
                                        <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="serving">
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-2.5 py-1 fw-semibold shadow-sm" title="Mulai layani pasien ini sekarang" dusk="btn-layani-{{ $item->id }}">
                                                <i class="bi bi-play-circle me-1"></i> Layani
                                            </button>
                                        </form>

                                        {{-- Tombol: Panggil --}}
                                        <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="called">
                                            <button type="submit" class="btn btn-outline-info btn-sm rounded-pill px-2 py-1" title="Panggil pasien ke loket">
                                                <i class="bi bi-megaphone"></i>
                                            </button>
                                        </form>
                                    @elseif($item->status === 'called')
                                        {{-- Dipanggil -> Langsung Mulai Layani --}}
                                        <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="serving">
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-2.5 py-1 fw-semibold shadow-sm" title="Mulai layani di poli" dusk="btn-layani-{{ $item->id }}">
                                                <i class="bi bi-play-circle me-1"></i> Layani
                                            </button>
                                        </form>
                                    @elseif($item->status === 'serving')
                                        {{-- Sedang Dilayani -> Selesaikan --}}
                                        <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-2.5 py-1 fw-semibold shadow-sm" title="Tandai pelayanan dokter sudah beres" dusk="btn-selesai-{{ $item->id }}">
                                                <i class="bi bi-check2-circle me-1"></i> Selesai
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Dropdown Pilih Manual untuk Seluruh Status --}}
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih status lain...">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.8rem; min-width: 170px;">
                                            <li><h6 class="dropdown-header text-uppercase fw-bold" style="font-size: 0.7rem;">Ubah Status Manual</h6></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="waiting">
                                                    <button type="submit" class="dropdown-item py-1.5 {{ $item->status === 'waiting' ? 'active' : '' }}">
                                                        <i class="bi bi-hourglass-split me-1 text-warning"></i> Menunggu
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="called">
                                                    <button type="submit" class="dropdown-item py-1.5 {{ $item->status === 'called' ? 'active' : '' }}">
                                                        <i class="bi bi-megaphone me-1 text-info"></i> Dipanggil
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="serving">
                                                    <button type="submit" class="dropdown-item py-1.5 {{ $item->status === 'serving' ? 'active' : '' }}">
                                                        <i class="bi bi-activity me-1 text-success"></i> Sedang Dilayani
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="dropdown-item py-1.5 {{ $item->status === 'completed' ? 'active' : '' }}">
                                                        <i class="bi bi-check2-circle me-1 text-primary"></i> Selesai
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.antrean.update-status', $item) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="skipped">
                                                    <button type="submit" class="dropdown-item py-1.5 text-danger {{ $item->status === 'skipped' ? 'active' : '' }}">
                                                        <i class="bi bi-x-circle me-1"></i> Dilewati / Batal
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-ticket-perforated fs-1 text-secondary d-block mb-2"></i>
                                <h6 class="fw-semibold text-dark mb-1">Belum Ada Antrean Pasien</h6>
                                <p class="small mb-0">Tidak ada data antrean pasien untuk tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong> yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($queues->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $queues->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

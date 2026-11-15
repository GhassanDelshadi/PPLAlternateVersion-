@extends('layouts.app')

@section('title', 'Admin - Mengelola Layanan & Tarif')

@section('content')
<div class="row g-4">
    {{-- Kolom Kiri: Form Tambah / Ubah Layanan & Tarif --}}
    <div class="col-lg-5">
        <div class="card-mockup">
            <div class="card-mockup-header">
                <h5 class="fw-bold mb-0 text-dark">
                    @if(isset($editLayanan) && $editLayanan)
                        Ubah Layanan &amp; Tarif
                    @else
                        Tambah Layanan &amp; Tarif
                    @endif
                </h5>
            </div>
            <div class="card-mockup-body">
                <form method="POST"
                      action="{{ isset($editLayanan) && $editLayanan ? route('admin.layanan.update', $editLayanan) : route('admin.layanan.store') }}"
                      dusk="{{ isset($editLayanan) && $editLayanan ? 'form-edit-layanan' : 'form-tambah-layanan' }}">
                    @csrf
                    @if(isset($editLayanan) && $editLayanan)
                        @method('PUT')
                    @endif

                    {{-- Nama Layanan --}}
                    <div class="mb-3">
                        <label for="nama_layanan" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                            Nama Layanan / Tindakan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="nama_layanan"
                               name="nama_layanan"
                               class="form-control rounded-3 py-2 @error('nama_layanan') is-invalid @enderror"
                               value="{{ old('nama_layanan', $editLayanan->nama_layanan ?? '') }}"
                               placeholder="Contoh: Pemeriksaan Umum / Scaling Gigi"
                               maxlength="150"
                               required
                               dusk="input-nama-layanan">
                        @error('nama_layanan')
                            <div class="invalid-feedback" dusk="error-nama-layanan">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kode Layanan & Poli Terkait --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-5">
                            <label for="kode_layanan" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                                Kode Layanan
                            </label>
                            <input type="text"
                                   id="kode_layanan"
                                   name="kode_layanan"
                                   class="form-control rounded-3 py-2 @error('kode_layanan') is-invalid @enderror"
                                   value="{{ old('kode_layanan', $editLayanan->kode_layanan ?? '') }}"
                                   placeholder="LAY-001"
                                   maxlength="30"
                                   dusk="input-kode-layanan">
                            @error('kode_layanan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-7">
                            <label for="poli_id" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                                Poli Layanan Terkait
                            </label>
                            <select id="poli_id"
                                    name="poli_id"
                                    class="form-select rounded-3 py-2 @error('poli_id') is-invalid @enderror"
                                    dusk="select-poli">
                                <option value="">-- Layanan Umum / Semua --</option>
                                @foreach($polis as $poli)
                                    <option value="{{ $poli->id }}" {{ old('poli_id', $editLayanan->poli_id ?? '') == $poli->id ? 'selected' : '' }}>
                                        {{ $poli->nama_poli }} ({{ $poli->kode_poli ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('poli_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tarif Pasien Umum & Tarif BPJS --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="tarif_umum" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                                Tarif Umum (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                <input type="number"
                                       id="tarif_umum"
                                       name="tarif_umum"
                                       step="500"
                                       min="0"
                                       class="form-control rounded-end-3 py-2 @error('tarif_umum') is-invalid @enderror"
                                       value="{{ old('tarif_umum', isset($editLayanan) ? (int)$editLayanan->tarif_umum : '50000') }}"
                                       required
                                       dusk="input-tarif-umum">
                            </div>
                            @error('tarif_umum')
                                <div class="invalid-feedback d-block" dusk="error-tarif-umum">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tarif_bpjs" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                                Tarif BPJS (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                <input type="number"
                                       id="tarif_bpjs"
                                       name="tarif_bpjs"
                                       step="500"
                                       min="0"
                                       class="form-control rounded-end-3 py-2 @error('tarif_bpjs') is-invalid @enderror"
                                       value="{{ old('tarif_bpjs', isset($editLayanan) ? (int)$editLayanan->tarif_bpjs : '0') }}"
                                       required
                                       dusk="input-tarif-bpjs">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.725rem;">
                                *0 jika ditanggung BPJS penuh.
                            </div>
                            @error('tarif_bpjs')
                                <div class="invalid-feedback d-block" dusk="error-tarif-bpjs">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Deskripsi Layanan --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold text-secondary" style="font-size: 0.875rem;">
                            Deskripsi / Cakupan Layanan
                        </label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  class="form-control rounded-3 py-2 @error('deskripsi') is-invalid @enderror"
                                  rows="3"
                                  placeholder="Keterangan tindakan, obat standar yang termasuk, atau persiapan pasien..."
                                  dusk="input-deskripsi">{{ old('deskripsi', $editLayanan->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox"
                                   id="status"
                                   name="status"
                                   value="1"
                                   class="form-check-input"
                                   {{ old('status', (isset($editLayanan) ? $editLayanan->status : 1)) ? 'checked' : '' }}
                                   dusk="input-status">
                            <label for="status" class="form-check-label fw-semibold" style="font-size: 0.875rem;">
                                Aktif (dapat dipilih untuk billing &amp; layanan)
                            </label>
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    @if(isset($editLayanan) && $editLayanan)
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-app-primary flex-grow-1" dusk="tombol-simpan">
                                <i class="bi bi-check-circle me-1"></i> Perbarui Layanan
                            </button>
                            <a href="{{ route('admin.layanan.index') }}" class="btn btn-outline-secondary px-3" style="border-radius: 8px;">
                                Batal
                            </a>
                        </div>
                    @else
                        <button type="submit" class="btn btn-app-primary w-100" dusk="tombol-simpan">
                            <i class="bi bi-plus-circle me-1"></i> Simpan Layanan &amp; Tarif
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Tabel Daftar Layanan & Tarif --}}
    <div class="col-lg-7">
        <div class="card-mockup">
            <div class="card-mockup-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Daftar Layanan &amp; Tarif</h5>
                    <small class="text-muted" style="font-size: 0.8rem;">Acuan dasar tagihan dan tarif tindakan klinik (PBI-12)</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.layanan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.8rem;" dusk="tombol-tambah-layanan">
                        + Tambah Baru
                    </a>
                </div>
            </div>
            <div class="card-mockup-body p-0">
                <div class="table-responsive">
                    <table class="table table-mockup align-middle" dusk="tabel-layanan">
                        <thead>
                            <tr>
                                <th style="width: 15%">KODE</th>
                                <th style="width: 32%">
                                    <a href="{{ route('admin.layanan.index', ['sort' => 'nama_layanan', 'dir' => request('sort') === 'nama_layanan' && request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-secondary text-decoration-none">
                                        LAYANAN &amp; POLI <i class="bi bi-arrow-down-up" style="font-size: 0.7rem;"></i>
                                    </a>
                                </th>
                                <th style="width: 18%">
                                    <a href="{{ route('admin.layanan.index', ['sort' => 'tarif_umum', 'dir' => request('sort') === 'tarif_umum' && request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="text-secondary text-decoration-none">
                                        TARIF UMUM <i class="bi bi-arrow-down-up" style="font-size: 0.7rem;"></i>
                                    </a>
                                </th>
                                <th style="width: 15%">TARIF BPJS</th>
                                <th style="width: 10%">STATUS</th>
                                <th style="width: 10%" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layanans as $layanan)
                                <tr dusk="baris-layanan-{{ $layanan->id }}" class="{{ isset($editLayanan) && $editLayanan->id === $layanan->id ? 'table-warning bg-opacity-10' : '' }}">
                                    <td>
                                        <span class="badge-kode" dusk="kode-layanan-{{ $layanan->id }}">
                                            {{ $layanan->kode_layanan ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $layanan->nama_layanan }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            @if($layanan->poli)
                                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                                    <i class="bi bi-hospital me-1"></i>{{ $layanan->poli->nama_poli }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                                    Umum
                                                </span>
                                            @endif
                                        </div>
                                        @if($layanan->deskripsi)
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                {{ Str::limit($layanan->deskripsi, 45) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark" dusk="tarif-umum-{{ $layanan->id }}">
                                            {{ $layanan->tarif_umum_formatted }}
                                        </span>
                                    </td>
                                    <td>
                                        @if((float)$layanan->tarif_bpjs == 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-shield-check me-1"></i> Ditanggung
                                            </span>
                                        @else
                                            <span class="fw-semibold text-secondary" style="font-size: 0.85rem;" dusk="tarif-bpjs-{{ $layanan->id }}">
                                                {{ $layanan->tarif_bpjs_formatted }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($layanan->status)
                                            <span class="status-dot-active" dusk="badge-aktif-{{ $layanan->id }}">
                                                ● Aktif
                                            </span>
                                        @else
                                            <span class="status-dot-inactive" dusk="badge-nonaktif-{{ $layanan->id }}">
                                                ● Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('admin.layanan.edit', $layanan) }}"
                                           class="btn-action-outline me-1"
                                           dusk="tombol-edit-{{ $layanan->id }}"
                                           title="Ubah data layanan">
                                            Ubah
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.layanan.toggle-status', $layanan) }}"
                                              class="d-inline"
                                              dusk="form-toggle-{{ $layanan->id }}">
                                            @csrf
                                            @method('PATCH')
                                            @if($layanan->status)
                                                <button type="submit"
                                                        class="btn-action-delete"
                                                        dusk="tombol-toggle-{{ $layanan->id }}"
                                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan layanan {{ $layanan->nama_layanan }}?')">
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="btn-action-activate"
                                                        dusk="tombol-toggle-{{ $layanan->id }}">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5" dusk="pesan-kosong">
                                        <div class="mb-2"><i class="bi bi-tag fs-2 text-secondary"></i></div>
                                        Belum ada data layanan &amp; tarif terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

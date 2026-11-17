# 🏥 BONAMU DESIGN SYSTEM (Anti-AI Slop Manifesto & UI Guidelines)
> **Sistem Manajemen Antrean Puskesmas & Rumah Sakit — Edisi Sprint 1 & 2**  
> *Versi 1.0 — Standar Visual, Tipografi, Token Warna, dan Komponen Klinis Bebas AI Slop*

---

## 1. Filosofi Desain: Melawan "AI Slop" di Aplikasi Medis

Banyak UI hasil generasi AI generatif (Claude, Cursor, v0, ChatGPT) terjebak dalam perangkap klise yang seragam (**"AI Slop"**):
* ❌ *Gradien ungu ke biru neon (`from-indigo-500 to-purple-600`)* seperti landing page crypto/SaaS murahan.
* ❌ *Glassmorphism sembarangan* (`backdrop-blur-md bg-white/20`) yang mengaburkan teks penting.
* ❌ *Teks abu-abu muda kontras rendah* (`#9CA3AF` di atas putih) yang tidak terbaca oleh pasien lansia atau layar ponsel retak di ruang tunggu.
* ❌ *Bouncy animations & hover zooms* (`hover:scale-105`) yang memusingkan dan tidak pantas untuk fasilitas kesehatan.
* ❌ *Tata letak generik "3-Card Symmetrical Grid"* yang dipaksakan untuk segala jenis data.

### ✨ Prinsip Bonamu: "Clinical Dignity & Instant Clarity"
Aplikasi antrean rumah sakit digunakan oleh **pasien yang sedang sakit/cemas** dan **staf medis yang bekerja cepat di bawah tekanan**. Desain Bonamu mengusung 4 pilar utama:

1. **Clarity Over Cleverness (Kejelasan di Atas Kemewahan Visual):**  
   Nomor antrean, nama poli, dan status giliran harus bisa dibaca dalam waktu kurang dari 1 detik dari jarak 1 meter.
2. **Clinical Calming Palette (Palet Hijau Medis & Kertas Bersih):**  
   Menggunakan hijau rumah sakit terkurasi (**Deep Hospital Teal `#005954`**) yang memberikan kesan higienis, tenang, dan terpercaya, dipadukan dengan permukaan netral hangat (**Warm Paper `#F8FAFA`**).
3. **Extreme Accessibility & High Contrast (WCAG AAA/AA Compliant):**  
   Semua teks instruksi dan label data memiliki kontras rasio minimal `4.5:1` (teks normal) dan `7:1` (label penting). Tidak ada teks abu-abu redup yang membingungkan.
4. **Institutional Editorial Typography:**  
   Kombinasi *Playfair Display* (judul display editorial yang berwibawa), *Plus Jakarta Sans* (antarmuka modern yang nyaman dibaca), dan *JetBrains Mono* (kode tiket/antrean yang tegas dan tidak ambigu).

---

## 2. Token Desain & Palet Warna

```css
:root {
    /* --- Brand Primary (Deep Hospital Teal) --- */
    --bonamu-primary: #005954;         /* Hijau Medis Utama: Tombol utama, header brand, border fokus */
    --bonamu-primary-hover: #004743;   /* Interaksi hover tombol utama */
    --bonamu-primary-dark: #092c24;    /* Aksen gelap untuk teks judul display */
    --bonamu-primary-light: #d1f4ed;   /* Mint container: Background pill aktif, badge terpilih */
    --bonamu-primary-border: #bbf0e1;  /* Garis batas halus untuk kontainer mint */

    /* --- Surfaces & Backgrounds --- */
    --bonamu-bg: #f8fafa;              /* Latar belakang aplikasi (kertas bersih) */
    --bonamu-card: #ffffff;            /* Kartu dan kontainer konten */
    --bonamu-border: #e8ecf1;          /* Border kartu, pemisah baris tabel */
    --bonamu-border-subtle: #edf2f7;   /* Garis tabel sekunder */

    /* --- Typography (Ink) --- */
    --bonamu-ink-heavy: #0f172a;       /* Judul utama, nomor tiket, angka statistik */
    --bonamu-ink-body: #1e293b;        /* Teks paragraf, input text, nama tabel */
    --bonamu-ink-muted: #64748b;       /* Keterangan tanggal, subjudul, bantuan form */
    --bonamu-ink-disabled: #94a3b8;    /* Elemen nonaktif/placeholder */

    /* --- Semantic Medical Statuses --- */
    /* 1. Aktif / Dilayani (Emerald) */
    --status-active-bg: #dcfce7;
    --status-active-text: #15803d;
    --status-active-border: #bbf7d0;

    /* 2. Menunggu / Pending (Amber) */
    --status-waiting-bg: #fef3c7;
    --status-waiting-text: #92400e;
    --status-waiting-border: #fde68a;

    /* 3. Darurat / Tutup / Nonaktif (Rose/Red) */
    --status-danger-bg: #fee2e2;
    --status-danger-text: #b91c1c;
    --status-danger-border: #fecaca;

    /* 4. Selesai / Info (Teal/Slate) */
    --status-info-bg: #e0f2fe;
    --status-info-text: #0369a1;
    --status-info-border: #bae6fd;
}
```

---

## 3. Sistem Tipografi

| Peran Tipografi | Font Family | Weight | Size / Line Height | Contoh Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **Display Header** | `'Playfair Display', serif` | Bold (700) | `32px – 40px` / `1.2` | Judul halaman utama pasien ("Layanan & Tarif", "Status Antrean") |
| **Section Header** | `'Plus Jakarta Sans', sans-serif` | SemiBold (600) | `20px – 24px` / `1.3` | Header kartu ("Pilih Jadwal", "Kelola Data Poli") |
| **Body Text** | `'Plus Jakarta Sans', sans-serif` | Regular (400) / Medium (500) | `14px – 15px` / `1.5` | Deskripsi, keterangan formulir, pesan bantuan |
| **Micro Labels** | `'Plus Jakarta Sans', sans-serif` | Bold (700) | `11px` / `Tracking 0.08em` | Tag kategori uppercase (`LAYANAN`, `DOKTER`, `PASIEN`) |
| **Monospace / Codes**| `'JetBrains Mono', monospace` | Bold (700) | `14px – 48px` | **Nomor Tiket Antrean (`PU-004`)**, Kode Poli, SIP Dokter |

---

## 4. Anatomi Komponen Kunci (Anti-AI Slop)

### A. Tiket Antrean Digital (The Patient Queue Ticket)
> Tiket pasien adalah dokumen visual paling krusial. Tidak boleh dibuat seperti card produk e-commerce.

```html
<!-- STANDAR TIKET ANTREAN BONAMU -->
<div class="card-mockup p-4 text-center border-0 shadow-sm" style="max-width: 420px; margin: 0 auto; border-radius: 20px;">
    <!-- Kategori & Poli -->
    <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase" 
          style="background-color: var(--bonamu-primary-light); color: var(--bonamu-primary); font-size: 11px; letter-spacing: 0.05em;">
        POLI UMUM
    </span>

    <!-- Nomor Antrean Raksasa (High Visibility) -->
    <div class="my-3 font-monospace fw-bold text-dark" style="font-size: 54px; letter-spacing: -0.04em; color: var(--bonamu-primary) !important;">
        PU-007
    </div>

    <!-- Status Saat Ini -->
    <div class="p-3 mb-3 rounded-4" style="background-color: #f1f8f6; border: 1px solid var(--bonamu-primary-border);">
        <div class="small text-muted mb-1">Status Panggilan</div>
        <div class="fw-bold fs-6" style="color: var(--bonamu-primary);">
            <i class="bi bi-person-walking me-1"></i> Sedang Dilayani: <strong>PU-004</strong>
        </div>
        <div class="small text-muted mt-1">Sisa 3 antrean sebelum giliran Anda</div>
    </div>

    <!-- Informasi Dokter & Estimasi Waktu -->
    <div class="d-flex justify-content-between text-start small py-2 border-top border-bottom mb-3">
        <div>
            <span class="text-muted d-block">Dokter Praktik:</span>
            <strong>dr. Budi Santoso</strong>
        </div>
        <div class="text-end">
            <span class="text-muted d-block">Jadwal Praktik:</span>
            <strong>08:00 - 12:00 WIB</strong>
        </div>
    </div>

    <!-- Tombol Aksi Nyata -->
    <button class="btn btn-primary w-100 py-2 rounded-pill fw-semibold shadow-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Cetak / Simpan Tiket
    </button>
</div>
```

---

### B. Formulir & Layout Admin Bebas Slop (Asymmetrical Split Workbench)
Jangan gunakan modal popup bertumpuk (*modal hell*) untuk tugas operasional berulang. Gunakan layout 2-kolom terbelah:
* **Kolom Kiri (40%):** Formulir input langsung (Tambah/Ubah data).
* **Kolom Kanan (60%):** Tabel data real-time dengan tombol aksi langsung.

```html
<div class="row g-4 align-items-start">
    <!-- KIRI: FORM WORKBENCH -->
    <div class="col-lg-5">
        <div class="card-mockup p-4 shadow-sm">
            <h5 class="fw-bold text-dark mb-1">Tambah Data Poli</h5>
            <p class="text-muted small mb-4">Tambahkan poliklinik baru untuk pendaftaran antrean.</p>

            <form action="..." method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">NAMA POLIKLINIK</label>
                    <input type="text" class="form-control rounded-3" placeholder="Contoh: Poli Gigi & Mulut">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">KODE POLI (3 HURUF)</label>
                    <input type="text" class="form-control font-monospace rounded-3" placeholder="PGM" maxlength="5">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold">
                    Simpan Poliklinik
                </button>
            </form>
        </div>
    </div>

    <!-- KANAN: TABEL DATA -->
    <div class="col-lg-7">
        <div class="card-mockup shadow-sm p-0 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3 small text-muted text-uppercase fw-bold">Kode</th>
                        <th class="small text-muted text-uppercase fw-bold">Poliklinik</th>
                        <th class="small text-muted text-uppercase fw-bold text-center">Status</th>
                        <th class="pe-3 py-3 small text-muted text-uppercase fw-bold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris data -->
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

### C. Komponen Pagination Bebas Raksasa (Bootstrap 5 Standard)
* Pagination wajib menggunakan `Paginator::useBootstrapFive()`.
* Ikon pagination dibatasi secara tegas (`max-width: 1rem; max-height: 1rem;`).
* Teks bernavigasi bilingual/lokal yang jelas: `« Sebelumnya` dan `Berikutnya »`.
* Posisi diletakkan di tengah (*centered*) atau di sisi kanan bawah tabel dengan informasi jumlah data yang transparan (`Menampilkan 1 sampai 15 dari 28 jadwal`).

---

## 5. Aturan Micro-Interactions & Gerakan (Motion Rules)

1. **Kecepatan Instan (120ms – 180ms):**  
   Gunakan kurva transisi `cubic-bezier(0.16, 1, 0.3, 1)` atau `ease-out`. Hindari durasi animasi lebih dari 250ms yang memperlambat respons aplikasi.
2. **Tidak Ada Transform Scale Mengambang:**  
   Jangan gunakan efek membesar (`scale-105`) pada kartu informasi medis karena membuat pasien merasa tombol tidak stabil.
3. **Focus State yang Nyata & Terlihat:**  
   Setiap elemen interaktif (input, select, button) memiliki focus ring tebal dengan transparansi warna brand:
   ```css
   .form-control:focus, .btn:focus {
       border-color: #005954 !important;
       box-shadow: 0 0 0 3px rgba(0, 89, 84, 0.18) !important;
       outline: none;
   }
   ```

---

## 6. Daftar Larangan Desain (The "Never Do" List)

| 🚫 JANGAN LAKUKAN (AI Slop) | ✅ LAKUKAN (Standar Bonamu) |
| :--- | :--- |
| Memakai warna ungu/indigo gradien untuk latar belakang | Gunakan warna solid `#005954` atau `#F8FAFA` yang bersih |
| Menaruh teks abu-abu terang `#A0AEC0` untuk instruksi penting | Gunakan warna `#1E293B` atau `#64748B` dengan kontras tinggi |
| Membuat kartu dengan bayangan hitam pekat (`box-shadow: 0 20px 25px rgba(0,0,0,0.4)`) | Gunakan bayangan halus klinis (`box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04)`) |
| Tombol teks tanpa latar belakang di samping aksi destruktif | Tombol batal abu-abu dengan outline yang jelas (`btn-outline-secondary`) |
| Placeholder form sebagai pengganti label | Label selalu tampil eksplisit di atas input text |
| Menampilkan layar kosong (*empty blank screen*) saat data 0 | Buat ilustrasi/ikon sederhana dengan pesan bersahabat + 1 tombol tindakan primer |

---

## 7. Referensi Kode Blade & CSS Kelas Utilitas

Kelas CSS yang sudah siap pakai di seluruh aplikasi (`layouts/app.blade.php`):

```html
<!-- 1. Tombol Utama Bonamu -->
<button class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
    Ambil Antrean
</button>

<!-- 2. Badge Mint Khas Bonamu -->
<span class="badge rounded-pill px-3 py-1" style="background-color: var(--bonamu-primary-light); color: var(--bonamu-primary); font-weight: 600;">
    Poli Gigi dan Mulut
</span>

<!-- 3. Kartu Kontainer Bonamu -->
<div class="card-mockup p-4 shadow-sm">
    Konten kartu klinis yang rapi dan terisolasi.
</div>

<!-- 4. Kode Monospace Tiket -->
<span class="badge-kode font-monospace">PU-001</span>
```

---

*Dokumen ini adalah acuan resmi tim pengembang Bonamu untuk menjaga kualitas antarmuka, memastikan keseragaman visual seluruh modul (PBI-03, 04, 05, 07, 09, 10, 11, 12), dan memberikan pengalaman pengguna terbaik untuk pasien dan staf medis.*

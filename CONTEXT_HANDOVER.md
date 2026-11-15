# HANDOVER & CONTEXT TRANSFER: PROYEK BONAMU & PUSKESMAS (PPL KELOMPOK AB)

Dokumen ini berisi rangkuman lengkap arsitektur, histori pekerjaan, status branch, kredensial akun, diagnosa bug tim, dan panduan melanjutkan pekerjaan di laptop/device lain.

---

## 1. Identitas & Lingkungan Kerja
- **Nama Anggota:** Muhammad Ghassan Delshadi Albarr
- **NIM:** 102022400333
- **Kelompok:** Kelompok AB (Mata Kuliah PPL / Pengembangan Perangkat Lunak)
- **Tugas Utama (PBI Resmi):** PBI-05 (Mengelola Data Poli - CRUD Poli Puskesmas)

---

## 2. Struktur Dua Folder di PC

Di komputer ini terdapat **2 folder terpisah**:

### A. Folder Resmi Tugas: `C:\PPL TUBES\puskesmas`
- **Tujuan:** Repository resmi kelompok untuk tugas akademik.
- **Git Remote:** `https://github.com/SI4801-PPL-KelompokAB/PPL-KELOMPOK-AB.git`
- **Branch Aktif:** `Ghassan_Branch` (Status: **Clean**, Up to date dengan remote `origin/Ghassan_Branch`).
- **Pekerjaan PBI-05:**
  - CRUD Data Poli lengkap sesuai mockup Bootstrap 5.
  - 8 Feature Tests (`PBI05PoliTest.php`) PASS.
  - 2 Dusk Browser Tests PASS.
  - **Catatan Penting:** Folder ini **TIDAK BOLEH dicampur aduk** dengan eksperimen integrasi tim agar branch `Ghassan_Branch` tetap bersih dan siap dinilai/dimerge ke develop resmi.

### B. Folder Integrasi & Prototipe Lengkap: `C:\PPL TUBES\Bonamu`
- **Tujuan:** Prototipe full-stack tempat seluruh fitur tim digabungkan untuk White-Box Testing & demo antarmuka.
- **Git Remote:** `https://github.com/GhassanDelshadi/PPLAlternateVersion-.git`
- **Branch Aktif:** `main` (sudah di-push).
- **Hasil Automated Test:** **45 Tests Passed (127 assertions, 0 Failures)**.

---

## 3. Fitur yang Sudah Ada & Berjalan di `Bonamu`
1. **PBI-01 & PBI-02 (Auth):** Login via No HP & Registrasi Pasien (`/login`, `/register`).
2. **PBI-03 (Akun Staf):** Admin membuat akun dokter dan admin baru (`/admin/akun`).
3. **PBI-04 (Profil Pasien):** Pengisian data diri, tanggal lahir, jenis kelamin, alamat, BPJS (`/pasien/profil`).
4. **PBI-05 (Data Poli):** CRUD Master Poli Puskesmas (`/admin/poli`).
5. **PBI-07 (Jadwal Dokter):** Jam kerja, hari praktik, kapasitas kuota per poli (`/admin/jadwal`).
6. **PBI-09 (Antrean Online):** Pasien memilih poli/dokter, terbit tiket digital berurutan (`/antrean`).
7. **PBI-10 & PBI-11 (Konsultasi):** Chat konsultasi online antara dokter dan pasien (`/konsultasi`, `/dokter/konsultasi`).
8. **Dashboard & Layout Terpadu:**
   - Multi-role dashboard router di `/dashboard`.
   - Dashboard Admin di `/admin/dashboard` (metrik statistik, jadwal hari ini, shortcut aksi).
   - Dashboard Pasien di `/pasien/dashboard` (tiket aktif live, jadwal buka, alert profil).
   - Halaman Profil Admin (`/admin/profil`) dan Profil Pasien (`/pasien/profil`).
   - Navbar dinamis dengan **1-Click Role Switcher** (`/dev/login-as/{role}`).

---

## 4. Kredensial Akun Testing di Database (MySQL `bonamu`)
- **Admin Puskesmas:**
  - No HP: `081234567890`
  - Email: `admin@test.com`
  - Password: `password`
- **Dokter Praktik (dr. Andi):**
  - No HP: `081200000001`
  - Email: `dokter.andi@test.com`
  - Password: `password`
- **Pasien (Budi Santoso):**
  - No HP: `081299999999`
  - Email: `pasien@test.com`
  - Password: `password`

---

## 5. Catatan Teknis & Solusi Bug Penting
1. **Penyebab Akun Terhapus Setelah Test (Sudah Diatasi):**
   - Di `phpunit.xml`, database pengujian sudah diset ke **SQLite in-memory** (`<env name="DB_CONNECTION" value="sqlite"/>` & `<env name="DB_DATABASE" value=":memory:"/>`).
   - Hasilnya, saat menjalankan `php artisan test`, data user di MySQL tidak akan pernah terhapus lagi.
2. **Diagnosa Bug Branch Anggota Tim Lain:**
   - `origin/Dika_Branch`: Sumber utama bug kelompok karena Dika sempat menghapus route auth di `web.php`, memakai hard-delete, dan membuat tabel duplikat `dokters` & `jadwal_dokters` yang tidak kompatibel dengan schema `doctors` & `doctor_schedules`.
   - `origin/Hanif_branch` (PBI-03, PBI-04) & `origin/Nara_Branch` (PBI-07, PBI-09) kodenya bersih dan sudah terintegrasi rapi di folder `Bonamu`.

---

## 6. Checklist Setup di Laptop Baru

1. **Clone Repo:**
   ```powershell
   git clone https://github.com/GhassanDelshadi/PPLAlternateVersion-.git Bonamu
   cd Bonamu
   ```
2. **Install Dependensi:**
   ```powershell
   composer install
   npm install
   ```
3. **Environment & Database:**
   - Salin `.env.example` ke `.env`:
     ```powershell
     cp .env.example .env
     php artisan key:generate
     ```
   - Sesuaikan konfigurasi database MySQL di `.env` (misal DB: `bonamu`, user: `root`).
   - Buat database `bonamu` di MySQL/XAMPP.
   - Jalankan migrasi & seeder:
     ```powershell
     php artisan migrate --seed
     ```
4. **Verifikasi:**
   - Jalankan test: `php artisan test` (pastikan 45 test pass).
   - Jalankan server: `php artisan serve` lalu buka `http://127.0.0.1:8000`.

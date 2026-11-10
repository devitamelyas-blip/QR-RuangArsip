# Pelita Air SCM — Document Loan Management System (Ruang Arsip)

Sistem informasi pengelolaan, penataan lokasi fisik, dan pelacakan sirkulasi dokumen arsip pengadaan berbasis **QR Code** untuk divisi **Supply Chain Management (SCM) PT Pelita Air Service**.

Aplikasi ini dibangun menggunakan arsitektur monolitik modern berbasis **Laravel 12**, **Inertia.js v2**, **Vue 3 (Composition API)**, dan **Tailwind CSS**, dengan pendekatan *mobile-first self-service* untuk staf peminjam serta *administrative dashboard* untuk petugas arsip.

---

## 1. Latar Belakang & Kebutuhan Operasional

Divisi Supply Chain Management (SCM) PT Pelita Air Service mengelola volume dokumen fisik pengadaan dalam jumlah besar, mencakup berkas pengadaan fasilitas umum, suku cadang pesawat, hingga kontrak logistik. Penyimpanan dokumen fisik dibagi ke dalam **2 Ruang Arsip Fisik** terpisah:

- **Ruang Arsip 1**: Khusus dokumen pengadaan kelompok **General Procurement (GP)** (pengadaan umum, fasilitas kantor, pemeliharaan gedung).
- **Ruang Arsip 2**: Gabungan dokumen pengadaan kelompok **Aircraft Commercial (AC)** dan **Material & Logistics (ML)** (suku cadang pesawat, lisensi software, armada operasional, logistik pemeliharaan).

### Permasalahan Operasional:
1. **Waktu Temu Kembali Berkas (Retrieval Time) Lambat**: Pencarian nomor pengadaan manual pada ratusan bantex di rak membutuhkan waktu lama.
2. **Ketiadaan Audit Trail Peminjaman**: Peminjaman dokumen fisik antar-staf atau antar-fungsi sering tidak tercatat rapi, sehingga berkas sulit dilacak saat dibutuhkan untuk kebutuhan audit internal/eksternal.
3. **Risiko Salah Tempat (Misfiling)**: Berkas rentan salah dikembalikan ke nomor rak yang tidak sesuai atau bahkan tertukar antar-ruang arsip.

---

## 2. Solusi & Alur Kerja Sistem

Sistem ini menerapkan hierarki pemindaian dua tingkat (*Two-Tier QR Architecture*):

```text
[ Pintu Masuk Ruang Arsip 1 (GP) ]       [ Pintu Masuk Ruang Arsip 2 (AC & ML) ]
               │                                            │
       (Scan QR Ruang 1)                            (Scan QR Ruang 2)
               ▼                                            ▼
      Katalog Pencarian GP                         Katalog Pencarian AC & ML
               │                                            │
               └────────────────────┬───────────────────────┘
                                    ▼
                   [ Temukan Bantex di Rak Fisik ]
                                    │
                                    ▼
                        [ Pindai QR Label Bantex ]
                                    │
                   ┌────────────────┴────────────────┐
                   ▼                                 ▼
             Form Pinjam                       Form Pengembalian
        (Nama & Keperluan Staf)               (Konfirmasi 1-Klik)
```

### A. Level 1: QR Code Ruang Arsip (Room Portal)
- Setiap ruang arsip memiliki poster label QR resmi yang terpasang pada pintu masuk.
- Saat staf berada di **Ruang Arsip 1**, pemindaian QR membuka portal pencarian yang otomatis tersaring khusus berkas **GP**.
- Saat staf berada di **Ruang Arsip 2**, pemindaian QR membuka portal pencarian berkas gabungan **AC & ML**.

### B. Level 2: QR Code Unit Bantex (Item Level)
- Setiap bantex memiliki stiker label QR unik (`/b/{slug}`) yang memuat kode bantex, letak rak, baris, posisi, serta tanda ruangan fisik.
- Staf cukup mengarahkan kamera smartphone ke stiker bantex untuk membuka form peminjaman mandiri (mengisi nama peminjam & keperluan) atau form pengembalian tanpa perlu hak akses admin.

---

## 3. Fitur Utama Sistem

### Operasional Publik & Staf (Mobile-First)
- **Pencarian Multi-Parameter & Multi-Relasi**: Mendukung pencarian instan berbasis nomor pengadaan (`Sxxxxxx`), uraian pengadaan, tahun anggaran, kode bantex, hingga posisi rak dan kelompok kerja.
- **Transaksi Peminjaman & Pengembalian Mandiri**: Antarmuka responsif ramah smartphone dengan validasi input, konfirmasi aksi, dan status ketersediaan *real-time*.
- **Pencegahan Data Race (Pessimistic Locking)**: Menggunakan `lockForUpdate` dalam transaksi database ACID untuk menjamin konsistensi status ketersediaan saat beberapa pemindaian terjadi bersamaan.

### Modul Administrasi & Petugas SCM
- **Operational KPI Dashboard**: Ringkasan inventaris fisik (Total Bantex, Dokumen Tersedia, Dokumen Dipinjam, Peringatan Keterlambatan `> 7 hari`), serta log transaksi terkini.
- **Pengembalian Manual Dokumen**: Opsi tindakan bagi admin untuk menyelesaikan status peminjaman jika staf lupa melakukan scan pengembalian saat mengembalikan berkas ke rak.
- **Audit Log & Riwayat Peminjaman**:
  - Filter pencarian *live debounced* (hasil tersaring otomatis saat mengetik).
  - Filter status transaksi, bantex spesifik, dan rentang tanggal peminjaman.
  - Bilah indikator *chips* filter aktif dan tombol *quick reset*.
- **Ekspor Laporan Resmi Microsoft Excel (.xlsx)**:
  - Dibangun menggunakan pustaka **PhpSpreadsheet**.
  - Dilengkapi kop resmi PT Pelita Air Service — Divisi SCM, ringkasan metadata cetak, dan parameter filter yang aktif.
  - Lebar kolom proporsional (mencegah teks terpotong dan error `#######` pada tanggal).
  - Dilengkapi fitur *AutoFilter* dan baris header terkunci (*freeze panes*).
  - Opsi ekspor data mentah format CSV (UTF-8 BOM).
- **Modul Cetak Label & Poster QR**:
  - **Poster Ruang Arsip**: Format cetak A4 siap tempel untuk Ruang Arsip 1 dan Ruang Arsip 2.
  - **Stiker Label Bantex**: Cetak satuan atau massal (*batch print*) dengan garis potong rapi dan identitas ruangan fisik.
- **Keamanan & Restriksi Jaringan (Opsional)**: Middleware pembatasan IP/CIDR untuk memastikan akses publik hanya dapat dilakukan melalui jaringan Wi-Fi lokal kantor Pelita Air.

---

## 4. Arsitektur Teknis & Struktur Kode

Proyek ini menerapkan pola arsitektur *Service Layer* dan prinsip *Thin Controller*:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── BantexController.php        # Master data CRUD & dokumen repeater
│   │   │   ├── DashboardController.php     # Metrik operasional & pengembalian manual
│   │   │   ├── LabelPrintController.php    # Cetak stiker bantex & poster ruang arsip
│   │   │   └── LoanHistoryController.php   # Audit log & ekspor laporan
│   │   └── Public/
│   │       ├── ScanController.php          # Transaksi mobile peminjaman/pengembalian
│   │       └── SearchController.php        # Portal pencarian dokumen & ruang arsip
│   ├── Middleware/
│   │   └── RestrictIpAddress.php           # Whitelisting subnet jaringan internal
│   └── Requests/
│       ├── BantexRequest.php
│       └── BorrowBantexRequest.php
├── Models/
│   ├── Bantex.php                          # Scopes ruang arsip, mutator, relasi
│   ├── DokumenBantex.php
│   └── Peminjaman.php                      # Audit sirkulasi & kalkulasi overdue
└── Services/
    ├── BantexService.php                   # Sinkronisasi master data & dokumen
    ├── LoanService.php                     # Logika transaksi & pessimistic locking
    ├── LoanExportService.php               # Generator Excel .xlsx enterprise
    └── QrCodeService.php                   # Generator SVG QR Code resolusi tinggi
```

---

## 5. Technology Stack

- **Backend Framework**: Laravel 12.x (PHP 8.4)
- **Frontend Layer**: Vue 3 (Composition API `<script setup>`) terintegrasi via Inertia.js v2 (arsitektur SPA tanpa overhead REST API)
- **Styling & Design System**: Tailwind CSS (Enterprise Corporate Aviation Theme: Navy `#0A2540`, White, Slate Neutral)
- **Spreadsheet Engine**: `phpoffice/phpspreadsheet`
- **QR Code Engine**: `simplesoftwareio/simple-qrcode` (BaconQrCode backend)
- **Testing Framework**: Pest PHP v5 (Feature & Unit Testing)

---

## 6. Panduan Instalasi & Menjalankan Proyek

### Prasyarat Sistem
- PHP >= 8.3 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, `xml`, `zip`.
- Composer 2.x
- Node.js >= 18.x & NPM
- MySQL >= 8.0 atau SQLite 3

### Langkah Instalasi

1. **Clone repositori dan pasang dependensi:**
   ```bash
   git clone <repository-url>
   cd QR-RuangArsip
   composer install
   npm install
   ```

2. **Pengaturan berkas konfigurasi lingkungan:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Konfigurasi Database (.env):**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=qr_ruang_arsip
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi Database & Seeding Data Awal:**
   ```bash
   php artisan migrate --seed
   ```
   *Seeder menyiapkan data master bantex, sampel dokumen pengadaan (GP & AC), riwayat peminjaman, serta akun admin default:*
   - **Email**: `admin@arsip.internal`
   - **Password**: `password123`

5. **Kompilasi Aset Frontend & Jalankan Server:**
   ```bash
   npm run build
   php artisan serve --port=8008
   ```

Aplikasi dapat diakses melalui:
- **Portal Publik Ruang 1 (GP)**: `http://localhost:8008/ruang/gp`
- **Portal Publik Ruang 2 (AC & ML)**: `http://localhost:8008/ruang/ac-ml`
- **Pencarian Dokumen**: `http://localhost:8008/cari`
- **Dashboard Admin**: `http://localhost:8008/login`

---

## 7. Pengujian Kualitas Perangkat Lunak (Automated Testing)

Proyek ini dilengkapi dengan *comprehensive test suite* menggunakan **Pest PHP** yang mencakup pengujian end-to-end:

- Fungsionalitas CRUD Master Bantex & sinkronisasi dokumen bertingkat.
- Pencegahan peminjaman ganda (*concurrency integrity*).
- Validasi input & alur transaksi peminjaman/pengembalian via scan publik.
- Penyaringan dokumen berdasarkan 2 ruang arsip fisik (`/ruang/gp` & `/ruang/ac-ml`).
- Cetak label satuan, massal, dan poster QR ruang arsip.
- Ekspor laporan audit ke format Microsoft Excel (.xlsx) dan CSV.

Jalankan pengujian:
```bash
php artisan test
```

Hasil pengujian:
```text
  Tests:    58 passed (322 assertions)
  Duration: ~3.5s
```

---

## 8. Lisensi & Hak Cipta

Proyek ini dikembangkan sebagai sistem informasi internal pengelolaan dokumen fisik Supply Chain Management (SCM) PT Pelita Air Service. Hak cipta kode dan aset grafis dilindungi.

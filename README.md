# QR Ruang Arsip

Sistem manajemen dan pelacakan arsip fisik berbasis **QR Code** untuk ruang arsip kantor, dibangun dengan **Laravel 12 (PHP 8.4)**, **Inertia.js v2**, **Vue 3 (Composition API)**, dan **Tailwind CSS**.

Aplikasi ini dirancang untuk mempermudah pencarian berkas pengadaan fisik di rak penyimpanan serta memfasilitasi pencatatan peminjaman dan pengembalian dokumen secara *mobile-first* melalui pemindaian label QR Code.

---

## ✨ Fitur Utama

- **Pencarian Cepat Dokumen**: Pencarian multi-parameter untuk menemukan nomor pengadaan (`Sxxxxxx`), nama pengadaan, tahun, maupun lokasi rak secara instan.
- **Scan QR & Alur Peminjaman Mobile**: Label QR Code pada bantex dapat dipindai langsung dari smartphone petugas untuk melihat isi berkas, meminjam, atau mengembalikan bantex tanpa harus login ke dashboard admin.
- **Pencegahan Concurrency / Race Condition**: Menggunakan transaksi database dengan *pessimistic locking* (`lockForUpdate`) untuk menjamin konsistensi status ketersediaan saat beberapa pemindaian terjadi bersamaan.
- **Dashboard Monitoring & Alert Keterlambatan**: Rekap statistik bantex (Tersedia, Dipinjam, Terlambat `> 7 hari`), tabel monitoring status fisik, dan fitur pengembalian manual oleh admin.
- **Riwayat Transaksi & Export CSV**: Pencatatan riwayat peminjaman dan pengembalian lengkap dengan filter status, rentang tanggal, dan ekspor CSV (UTF-8 BOM).
- **Cetak Label QR Siap Cetak**: Generator QR Code SVG murni dengan layout `@media print` bergaris potong, siap dicetak satuan maupun massal (*batch print*).
- **Pembatasan Jaringan Internal (Opsional)**: Dukungan middleware validasi IP / CIDR untuk membatasi akses halaman publik hanya dari jaringan WiFi internal kantor.

---

## 🏛️ Arsitektur & Pola Desain

- **Clean Architecture & Service Layer**: Logika bisnis dan konkurensi database dipisahkan dari controller ke dalam layer layanan khusus ([`LoanService`](file:///app/Services/LoanService.php), [`BantexService`](file:///app/Services/BantexService.php), dan [`QrCodeService`](file:///app/Services/QrCodeService.php)).
- **Thin Controllers**: Controller hanya bertanggung jawab mengelola HTTP request dan meneruskan data ke Inertia.
- **Form Request Validation**: Setiap validasi input (termasuk dokumen *repeater* dinamis) diisolasi dalam Form Request tersendiri.
- **Comprehensive Testing**: Dilengkapi pengujian fungsional dan unit otomatis menggunakan **Pest PHP** (53 pengujian, 251 *assertions*).

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.4, SQLite / MySQL
- **Frontend**: Vue 3 (`<script setup>`), Inertia.js v2, Tailwind CSS
- **Code Quality & Testing**: Pest PHP, Laravel Pint (PSR-12)

---

## 🚀 Menjalankan Aplikasi Secara Lokal

### 1. Kloning & Instal Dependensi

```bash
composer install
npm install
```

### 2. Konfigurasi Environment & Database

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

> **Akun Default Seeder**: `admin@arsip.internal` / `password123`

### 3. Build & Jalankan Server

```bash
npm run build
php artisan serve
```

Aplikasi dapat diakses melalui browser di:
- **Pencarian / Scan Publik**: `http://localhost:8000/cari`
- **Dashboard Admin**: `http://localhost:8000/admin` (atau `/login`)

---

## 🧪 Pengujian Otomatis

Untuk menjalankan seluruh test suite:

```bash
php artisan test
```

```text
Tests:    53 passed (251 assertions)
Duration: ~3.5s
```

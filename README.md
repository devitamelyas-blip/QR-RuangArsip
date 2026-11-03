# Sistem Arsip QR Code Ruang Arsip

Aplikasi manajemen arsip dokumen berbasis **QR Code** untuk ruang arsip internal kantor, dibangun dengan **Laravel 12 (PHP 8.4)**, **Inertia.js v2**, **Vue 3 (Composition API `<script setup>`)**, dan **Tailwind CSS**.

Aplikasi dirancang **offline-ready / internal network ready** (tanpa ketergantungan CDN eksternal, font dan ikon SVG di-bundle lokal), dengan desain **mobile-first** untuk pemindaian label bantex langsung dari smartphone petugas di ruang arsip.

---

## 📌 Fitur Utama

1. **Halaman Scan Publik (`GET /b/{slug}`)**:
   - Menampilkan kode bantex, posisi rak (Rak, Baris, Posisi Bantex, dan panduan teks cara baca), daftar dokumen di dalamnya, dan status.
   - **Status Tersedia**: Form peminjaman langsung (input Nama Peminjam & Keperluan wajib diisi).
   - **Status Sedang Dipinjam**: Menampilkan identitas peminjam, keperluan, tanggal & lama hari peminjaman, serta tombol pengembalian berkonfirmasi modal.
   - **Keamanan Konkurensi Database**: Transaksi `DB::transaction` dengan `lockForUpdate()` untuk mencegah race condition (peminjaman ganda saat dua orang scan bersamaan).
   - **Rate Limiting**: Pembatasan request submission (`throttle:20,1`).

2. **Pencarian Publik Dokumen (`GET /cari`)**:
   - Satu kolom pencarian cerdas untuk: Nomor Pengadaan (`Sxxxxxx`), Uraian Nama Pengadaan, Tahun, Kode Bantex, serta Lokasi Rak.
   - Menampilkan lokasi detail tempat dokumen disimpan dan status ketersediaannya.

3. **Dashboard Monitoring Admin (`GET /admin`)**:
   - Statistik real-time: Total Bantex, Tersedia di Rak, Sedang Dipinjam, dan Terlambat (`> 7 hari`).
   - Tabel monitoring ketersediaan bantex beserta nama peminjam aktif.
   - Fitur **Pengembalian Manual** oleh admin jika peminjam lupa melakukan scan QR pengembalian.

4. **Master Data CRUD Bantex & Dokumen (`/admin/bantex`)**:
   - Penambahan dan pembaruan bantex beserta formulir dinamis daftar berkas dokumen di dalamnya.
   - Pembuatan slug aman URL otomatis dari kode bantex (`SCM/GP/R-01/01/HCGA/GA/02` -> `SCM-GP-R-01-01-HCGA-GA-02`).
   - Proteksi integritas data: Pencegahan penghapusan bantex yang sedang berstatus dipinjam.

5. **Riwayat Peminjaman & Export CSV (`/admin/riwayat-peminjaman`)**:
   - Log lengkap seluruh transaksi peminjaman dan pengembalian.
   - Filter berdasarkan kata kunci peminjam/keperluan, spesifik bantex, rentang tanggal, dan status peminjaman.
   - Fitur **Export CSV** (dengan header UTF-8 BOM untuk kompatibilitas sempurna Microsoft Excel).

6. **Generator QR Code & Cetak Label Siap Potong**:
   - QR Code di-generate di backend dalam format SVG murni (Error Correction Level M).
   - Cetak satuan per bantex atau cetak massal (batch) sekaligus.
   - Layout cetak `@media print` dengan garis putus-putus panduan potong, ukuran font lokasi besar, dan QR Code kontras tinggi.

7. **Middleware Pembatasan IP Internal (Opsional)**:
   - Pembatasan akses halaman publik via konfigurasi `.env` (`ALLOWED_IP_RANGES`).
   - Mendukung IP tunggal maupun format subnet CIDR (contoh: `192.168.1.0/24,10.0.0.0/8`). Jika dikosongkan, middleware non-aktif.

---

## 🔐 Kredensial Akun Admin Bawaan (Seeder)

Setelah menjalankan database seeder, akun admin default dapat digunakan untuk masuk ke sistem:

- **URL Login Admin**: `/login` atau `/admin`
- **Email**: `admin@arsip.internal`
- **Password**: `password123`

> ⚠️ **PENTING SAAT PRODUKSI:** Segera ganti password atau buat akun admin baru melalui menu profil admin sebelum aplikasi digunakan di lingkungan produksi!

---

## 💻 Panduan Instalasi Lokal (Development)

### 1. Prasyarat Sistem
- PHP `>= 8.2` (disarankan PHP `8.4`) dengan ekstensi: `pdo_mysql`, `pdo_sqlite`, `gd`, `mbstring`, `openssl`, `curl`, `dom`, `zip`.
- Composer `>= 2.2`
- Node.js `>= 18` & NPM

### 2. Langkah Instalasi

```bash
# 1. Clone atau masuk ke direktori proyek
cd c:\QR-Arsip\QR-RuangArsip

# 2. Instal dependensi PHP
composer install

# 3. Instal dependensi frontend
npm install

# 4. Salin file konfigurasi environment
cp .env.example .env

# 5. Generate Application Key
php artisan key:generate

# 6. Konfigurasi Database di .env
# Untuk pengembangan cepat lokal (default SQLite):
# DB_CONNECTION=sqlite

# Atau jika menggunakan MySQL / MariaDB lokal:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=qr_ruang_arsip
# DB_USERNAME=root
# DB_PASSWORD=password_anda

# 7. Jalankan Migrasi & Seeder Data Awal
php artisan migrate:fresh --seed

# 8. Build aset frontend
npm run build

# 9. Jalankan server lokal
php artisan serve
```

Akses aplikasi di browser:
- Halaman Scan / Cari Publik: `http://localhost:8000/cari`
- Halaman Login Admin: `http://localhost:8000/login`

---

## 🚀 Panduan Deployment ke Server Internal Kantor

Untuk deployment di server internal kantor (Ubuntu/Debian Server atau Windows Server):

### 1. Instalasi Dependensi Produksi (Tanpa Dev Tools)
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 2. Konfigurasi File `.env` Produksi
Pastikan pengaturan berikut pada file `.env` di server:
```env
APP_NAME="Sistem Arsip QR Code"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://arsip.kantor.internal  # Sesuaikan IP/domain internal server

# Konfigurasi Database MySQL/MariaDB Server
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qr_ruang_arsip
DB_USERNAME=user_arsip
DB_PASSWORD=password_kuat_database

# Pembatasan IP WiFi Kantor (Opsional, kosongkan jika dibatasi di level router/firewall)
ALLOWED_IP_RANGES=192.168.10.0/24,192.168.20.0/24

# Batas Keterlambatan Peminjaman (Default 7 hari)
LOAN_DUE_DAYS=7
```

### 3. Migrasi Database Produksi
```bash
php artisan migrate --force
# (Opsional) Jalankan seeder admin jika database baru:
php artisan db:seed --class=AdminUserSeeder --force
```

### 4. Optimasi Cache Laravel
Jalankan perintah cache produksi untuk performa maksimal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Izin Folder (Permissions)
Pastikan web server (Nginx/Apache) memiliki hak akses tulis ke folder `storage` dan `bootstrap/cache`:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Contoh Konfigurasi Virtual Host Nginx
Arahkan `root` web server langsung ke direktori **`public`**:

```nginx
server {
    listen 80;
    server_name arsip.kantor.internal; # Atau IP internal: 192.168.1.100
    root /var/www/qr-ruangarsip/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🧪 Menjalankan Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan 53 automated test (menggunakan Pest PHP) mencakup pengujian fungsional database locking, CRUD admin, pencarian publik, validasi, dan pembatasan IP:

```bash
php artisan test
```

Hasil uji:
- **53 passed tests** (100%)
- **251 assertions**

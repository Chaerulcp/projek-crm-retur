# Sistem CRM Manajemen Retur

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL-orange)
![Frontend](https://img.shields.io/badge/Frontend-Bootstrap_5-purple)

Sistem CRM Retur ini adalah aplikasi web komprehensif untuk mengelola dan mengotomatiskan proses pengajuan retur dan refund barang. Dibangun dengan PHP native dan MySQL, sistem ini menyediakan portal terpisah untuk pelanggan dan staf dengan peran berbeda, memastikan alur kerja efisien dan terorganisir.

## 📜 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Alur Kerja Sistem](#-alur-kerja-sistem)
- [Peran Pengguna](#-peran-pengguna)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Prasyarat](#-prasyarat)
- [Panduan Instalasi](#-panduan-instalasi)
- [Cara Penggunaan](#-cara-penggunaan)
- [Struktur Proyek](#-struktur-proyek)

## ✨ Fitur Utama

Sistem ini kaya fitur untuk menangani seluruh siklus proses retur:

### Portal Pelanggan

- **Pengajuan Retur Mudah:** Formulir intuitif dengan upload bukti foto/video.
- **Pelacakan Status Real-time:** Pantau progres retur dengan nomor tiket unik.
- **Pusat Bantuan (FAQ):** Halaman FAQ dinamis.
- **Notifikasi Email Otomatis:** Update status tiket via email.
- **Live Chat Interaktif:** Komunikasi langsung dengan staf di halaman status tiket.

### Portal Staf (Dasbor Internal)

- **Manajemen Tiket Terpusat:** Ringkasan dan daftar semua tiket retur.
- **Dasbor Berbasis Peran:** Setiap peran (Admin, CS, Gudang, Manajemen) punya dasbor khusus.
- **Detail Tiket Komprehensif:** Lihat detail tiket, info pelanggan, produk, alasan retur, bukti, dan riwayat komunikasi.
- **Pembaruan Status & Komunikasi:** Ubah status tiket dan tambahkan catatan internal.
- **Aktivasi Live Chat:** Staf berwenang dapat mengaktifkan/menonaktifkan live chat per tiket.

### Fitur Khusus Admin & Manajemen

- **Manajemen Pengguna:** Admin kelola akun staf dengan berbagai peran.
- **Manajemen Produk:** Admin kelola daftar produk untuk retur.
- **Manajemen FAQ:** Admin/CS kelola konten FAQ.
- **Dasbor Analitik:** Visualisasi tren retur, produk terpopuler, alasan retur teratas.
- **Dasbor Keuangan:** Proses refund dan unggah bukti transfer.
- **Dasbor Gudang:** Cek kondisi barang retur dan update status.

## 🔄 Alur Kerja Sistem

1. **Pengajuan:** Pelanggan isi formulir di `form-retur.php`.
2. **Verifikasi Awal (CS):** Tiket baru diverifikasi CS.
3. **Persetujuan & Pengiriman:** Status diubah ke 'Menunggu Barang', pelanggan kirim produk.
4. **Pemeriksaan Gudang:** Gudang cek barang di `gudang-dashboard.php`.
5. **Proses Refund (Keuangan):** Jika barang 'Layak', keuangan proses refund di `keuangan-dashboard.php`.
6. **Selesai:** Setelah refund, status tiket menjadi 'Selesai'.
7. **Komunikasi:** Pelanggan dan staf bisa live chat jika diaktifkan.

## 👤 Peran Pengguna

- **Pelanggan (Publik):** Ajukan retur & lacak tiket.
- **Customer Service (CS):** Verifikasi tiket & komunikasi pelanggan.
- **Gudang:** Periksa kondisi barang retur.
- **Manajemen/Keuangan:** Lihat analitik, proses refund, tutup tiket.
- **Admin:** Akses penuh, kelola pengguna & produk.

## 💻 Teknologi yang Digunakan

- **Backend:** PHP 8+ (Native, prosedural)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Framework/Library:** Bootstrap 5.3.3, Bootstrap Icons, Chart.js
- **Manajemen Dependensi:** Composer
- **Layanan Email:** PHPMailer (via SMTP)

## 🛠️ Prasyarat

- Web Server (Apache, Nginx, dll)
- PHP 8.0+
- MySQL atau MariaDB
- Composer

## 🚀 Panduan Instalasi

1. **Clone Repositori**
    ```bash
    git clone https://github.com/chaerulcp/projek-crm-retur.git
    cd projek-crm-retur
    ```

2. **Instal Dependensi PHP**
    ```bash
    composer install
    ```
    Akan mengunduh PHPMailer ke direktori `vendor/`.

3. **Setup Database**
    - Buat database baru (misal: `crm_retur_db`).
    - Impor `database.sql` ke database tersebut.

4. **Konfigurasi Aplikasi**
    - Salin `config.php.example` ke `config.php` (atau edit langsung).
    - Sesuaikan koneksi database di `config.php`.

5. **Konfigurasi Email & reCAPTCHA**
    - **Email:** Edit `fungsi-email.php` untuk detail SMTP.
    - **reCAPTCHA:** Edit `proses-retur.php` & `form-retur.php` untuk kunci Google reCAPTCHA v2.

6. **Atur Izin Folder**
    ```bash
    chmod -R 775 uploads
    ```
    Pastikan web server bisa menulis ke `uploads/`.

## ▶️ Cara Penggunaan

1. **Buat Akun Staf Awal**
    - Akses `register-staff.php` via browser (misal: `http://localhost/projek-crm-retur/register-staff.php`).
    - Akan dibuat akun default (Admin, CS, Gudang, Keuangan).
    - **PENTING:** Hapus/ganti nama file `register-staff.php` setelah digunakan.

2. **Login ke Portal Staf**
    - Buka `login.php`.
    - Masuk dengan kredensial staf.

3. **Mulai Menggunakan Aplikasi**
    - **Admin:** Kelola produk & pengguna via sidebar.
    - **Pelanggan:** Akses `index.php` untuk ajukan retur.

## 📁 Struktur Proyek

```
/
├── assets/                  # File CSS & JS kustom
├── uploads/                 # Direktori upload (writable)
├── vendor/                  # Dependensi Composer
├── analitik-dashboard.php   # Dasbor analitik (Manajemen)
├── config.php               # Konfigurasi utama
├── dashboard.php            # Dasbor staf
├── detail-tiket.php         # Detail tiket
├── form-retur.php           # Formulir retur (Pelanggan)
├── login.php                # Login staf
├── proses-*.php             # Skrip backend
├── register-staff.php       # Registrasi staf awal (hapus setelah pakai)
├── status-tiket.php         # Pelacakan tiket (Pelanggan)
├── *-manajemen.php          # Halaman manajemen (Produk, User, FAQ)
└── README.md
```
# Sistem CRM Manajemen Retur

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL-orange)
![Frontend](https://img.shields.io/badge/Frontend-Bootstrap_5-purple)

Sistem CRM Retur ini adalah aplikasi web komprehensif yang dirancang untuk mengelola dan mengotomatiskan proses pengajuan retur dan refund barang. Dibangun dengan PHP native dan MySQL, sistem ini menyediakan portal yang terpisah untuk pelanggan dan staf dengan peran yang berbeda, memastikan alur kerja yang efisien dan terorganisir.

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

Sistem ini kaya akan fitur yang dirancang untuk menangani seluruh siklus proses retur:

#### Portal Pelanggan
* **Pengajuan Retur Mudah:** Formulir intuitif untuk mengajukan permintaan retur, lengkap dengan kemampuan upload bukti foto/video.
* **Pelacakan Status Real-time:** Pelanggan dapat memantau progres pengajuan retur mereka menggunakan nomor tiket unik.
* **Pusat Bantuan (FAQ):** Halaman FAQ yang dinamis untuk membantu pelanggan menemukan jawaban atas pertanyaan umum.
* **Notifikasi Email Otomatis:** Sistem mengirimkan notifikasi email kepada pelanggan setiap kali ada pembaruan status pada tiket mereka.
* **Live Chat Interaktif:** Pelanggan dapat berkomunikasi langsung dengan staf melalui fitur live chat yang terintegrasi di halaman status tiket.

#### Portal Staf (Dasbor Internal)
* **Manajemen Tiket Terpusat:** Dasbor utama menampilkan ringkasan dan daftar semua tiket retur.
* **Dasbor Berbasis Peran:** Setiap peran (Admin, CS, Gudang, Manajemen) memiliki dasbor khusus yang menampilkan tugas dan informasi yang relevan.
* **Detail Tiket Komprehensif:** Staf dapat melihat semua detail tiket, termasuk informasi pelanggan, produk, alasan retur, bukti, dan riwayat komunikasi lengkap.
* **Pembaruan Status & Komunikasi:** Staf dapat mengubah status tiket dan menambahkan catatan internal yang juga dapat dikirimkan sebagai notifikasi ke pelanggan.
* **Aktivasi Live Chat:** Staf yang berwenang dapat mengaktifkan atau menonaktifkan fitur live chat untuk setiap tiket.

#### Fitur Khusus Admin & Manajemen
* **Manajemen Pengguna:** Admin dapat menambah, mengedit, dan menghapus akun staf dengan berbagai peran.
* **Manajemen Produk:** Admin dapat mengelola daftar produk yang tersedia untuk dipilih dalam formulir retur.
* **Manajemen FAQ:** Admin dan CS dapat dengan mudah menambah, mengedit, dan menghapus konten di halaman Pusat Bantuan (FAQ).
* **Dasbor Analitik:** (Untuk Manajemen) Visualisasi data penting seperti tren retur harian, produk yang paling sering diretur, dan alasan retur teratas dalam bentuk grafik.
* **Dasbor Keuangan:** (Untuk Manajemen) Antarmuka khusus untuk memproses pengembalian dana (refund) dan mengunggah bukti transfer.
* **Dasbor Gudang:** (Untuk Gudang) Antarmuka untuk memeriksa kondisi barang yang diretur dan memperbarui statusnya.

## 🔄 Alur Kerja Sistem

1.  **Pengajuan:** Pelanggan mengisi formulir di `form-retur.php`.
2.  **Verifikasi Awal (CS):** Tiket baru muncul di dasbor CS. Tim CS memverifikasi kelengkapan data.
3.  **Persetujuan & Pengiriman:** Setelah disetujui, status diubah menjadi 'Menunggu Barang' dan pelanggan mengirimkan produknya.
4.  **Pemeriksaan Gudang:** Tim gudang mengakses `gudang-dashboard.php`, memeriksa kondisi barang, dan mengubah statusnya.
5.  **Proses Refund (Keuangan):** Jika barang 'Layak', tiket muncul di `keuangan-dashboard.php`. Tim keuangan memproses refund.
6.  **Selesai:** Setelah refund diproses, status tiket berubah menjadi 'Selesai'.
7.  **Komunikasi:** Pelanggan dan staf dapat berkomunikasi melalui Live Chat jika diaktifkan pada tiket terkait.

## 👤 Peran Pengguna

* **Pelanggan (Publik):** Mengajukan retur dan melacak status tiket.
* **Customer Service (CS):** Melakukan verifikasi awal tiket dan berkomunikasi dengan pelanggan.
* **Gudang:** Menerima dan memeriksa kondisi fisik barang retur.
* **Manajemen/Keuangan:** Melihat analitik, memproses refund, dan menutup tiket.
* **Admin:** Memiliki akses penuh ke semua fitur, termasuk manajemen pengguna dan produk.

## 💻 Teknologi yang Digunakan

* **Backend:** PHP 8+ (Native, prosedural)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **Framework/Library:**
    * Bootstrap 5.3.3
    * Bootstrap Icons
    * Chart.js
* **Manajemen Dependensi:** Composer
* **Layanan Email:** PHPMailer (via SMTP)

## 🛠️ Prasyarat

* Web Server (Apache, Nginx, atau sejenisnya)
* PHP versi 8.0 atau lebih baru
* MySQL atau MariaDB
* Composer

## 🚀 Panduan Instalasi

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/chaerulcp/projek-crm-retur.git](https://github.com/chaerulcp/projek-crm-retur.git)
    cd projek-crm-retur
    ```

2.  **Instal Dependensi PHP**
    Pastikan Composer terinstal, lalu jalankan:
    ```bash
    composer install
    ```
    Ini akan mengunduh PHPMailer ke dalam direktori `vendor/`.

3.  **Setup Database**
    * Buat database baru di server MySQL Anda (contoh: `crm_retur_db`).
    * Impor file `database.sql` yang ada di repositori ke dalam database yang baru dibuat.

4.  **Konfigurasi Aplikasi**
    * Salin `config.php.example` menjadi `config.php` (jika ada, jika tidak, langsung edit).
    * Buka file `config.php` dan sesuaikan detail koneksi database (`$host`, `$db_name`, `$username`, `$password`).

5.  **Konfigurasi Email & reCAPTCHA**
    * **Email:** Buka `fungsi-email.php` dan isi detail server SMTP Anda.
    * **reCAPTCHA:** Buka `proses-retur.php` dan `form-retur.php`, lalu ganti `sitekey` dan `secret` dengan kunci Google reCAPTCHA v2 Anda.

6.  **Atur Izin Folder**
    Pastikan web server memiliki izin tulis ke direktori `uploads/` untuk menangani unggahan file.
    ```bash
    chmod -R 775 uploads
    ```

## ▶️ Cara Penggunaan

1.  **Buat Akun Staf Awal**
    * Akses file `register-staff.php` dari browser Anda (misal: `http://localhost/projek-crm-retur/register-staff.php`).
    * File ini akan membuat beberapa akun staf default (Admin, CS, Gudang, Keuangan).
    * **PENTING:** Demi keamanan, segera **hapus atau ganti nama file `register-staff.php`** setelah digunakan.

2.  **Login ke Portal Staf**
    * Buka halaman `login.php`.
    * Gunakan kredensial yang dibuat pada langkah sebelumnya untuk masuk.

3.  **Mulai Menggunakan Aplikasi**
    * **Admin:** Kelola produk dan pengguna melalui menu di sidebar.
    * **Pelanggan:** Akses halaman utama (`index.php`) untuk mengajukan retur.

## 📁 Struktur Proyek
/
├── assets/                  # File CSS dan JavaScript kustom
├── uploads/                 # Direktori untuk file yang diunggah (writable)
├── vendor/                  # Dependensi dari Composer
├── analitik-dashboard.php   # Dasbor analitik (Manajemen)
├── config.php               # File konfigurasi utama
├── dashboard.php            # Dasbor utama staf
├── detail-tiket.php         # Halaman detail tiket
├── form-retur.php           # Formulir pengajuan retur (Pelanggan)
├── login.php                # Halaman login staf
├── proses-*.php             # Skrip pemrosesan backend
├── register-staff.php       # Skrip registrasi staf awal (hapus setelah pakai)
├── status-tiket.php         # Halaman pelacakan tiket (Pelanggan)
├── *-manajemen.php          # Halaman manajemen (Produk, User, FAQ)
└── README.md
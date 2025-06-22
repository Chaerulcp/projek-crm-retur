<?php
/*
 * File Konfigurasi Database
 * --------------------------
 * File ini berisi semua informasi yang dibutuhkan untuk terhubung ke database.
 * Pisahkan file ini agar mudah dikelola dan lebih aman.
 */

// Detail koneksi database
$host = 'localhost';      // Biasanya 'localhost'
$db_name = 'crm_retur_db';// Nama database yang kita buat tadi
$username = 'root';       // Username database Anda
$password = '';           // Password database Anda (kosongkan jika tidak ada)
$charset = 'utf8mb4';     // Charset yang direkomendasikan

// PDO Connection String (DSN)
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// Opsi untuk PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mengembalikan data sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Blok Try-Catch untuk menangani error koneksi
try {
    // Membuat instance PDO
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, hentikan script dan tampilkan pesan error
    // Di lingkungan produksi, sebaiknya catat error ini ke file log, bukan menampilkannya ke pengguna.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Jika berhasil, variabel $pdo sekarang bisa digunakan di file lain untuk query ke database.
?>
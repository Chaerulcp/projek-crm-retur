<?php
session_start();
// Hanya Admin yang bisa melakukan aksi ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

$action = $_POST['action'] ?? '';

try {
    if ($action == 'tambah') {
        $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, sku, deskripsi) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['nama_produk'], $_POST['sku'], $_POST['deskripsi']]);
    } elseif ($action == 'edit') {
        $stmt = $pdo->prepare("UPDATE produk SET nama_produk = ?, sku = ?, deskripsi = ? WHERE id_produk = ?");
        $stmt->execute([$_POST['nama_produk'], $_POST['sku'], $_POST['deskripsi'], $_POST['id_produk']]);
    } elseif ($action == 'hapus') {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->execute([$_POST['id_produk']]);
    }
} catch (PDOException $e) {
    // Jika ada error (misal: SKU duplikat), tampilkan pesan
    die("Database error: " . $e->getMessage());
}

header("Location: produk-manajemen.php");
exit();
?>
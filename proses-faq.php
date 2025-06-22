<?php
session_start();

// Keamanan: Pastikan hanya staf yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_peran'], ['Customer Service', 'Manajemen', 'Admin'])) {
    header("Location: login.php");
    exit();
}

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: faq-manajemen.php");
    exit();
}

require_once 'config.php';

$action = $_POST['action'] ?? '';

try {
    if ($action == 'tambah' && !empty($_POST['pertanyaan']) && !empty($_POST['jawaban'])) {
        $stmt = $pdo->prepare("INSERT INTO faq (pertanyaan, jawaban, kategori) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['pertanyaan'], $_POST['jawaban'], $_POST['kategori']]);
    } 
    elseif ($action == 'edit' && !empty($_POST['id_faq']) && !empty($_POST['pertanyaan']) && !empty($_POST['jawaban'])) {
        $stmt = $pdo->prepare("UPDATE faq SET pertanyaan = ?, jawaban = ?, kategori = ? WHERE id_faq = ?");
        $stmt->execute([$_POST['pertanyaan'], $_POST['jawaban'], $_POST['kategori'], $_POST['id_faq']]);
    } 
    elseif ($action == 'hapus' && !empty($_POST['id_faq'])) {
        $stmt = $pdo->prepare("DELETE FROM faq WHERE id_faq = ?");
        $stmt->execute([$_POST['id_faq']]);
    }
} catch (PDOException $e) {
    // Di lingkungan produksi, sebaiknya error ini dicatat ke log, bukan ditampilkan
    die("Database error: " . $e->getMessage());
}

// Setelah selesai, kembalikan ke halaman manajemen
header("Location: faq-manajemen.php");
exit();
?>
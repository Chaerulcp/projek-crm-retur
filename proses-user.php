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
        if (empty($_POST['password'])) die("Password wajib diisi untuk pengguna baru.");
        $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO pengguna_sistem (nama_lengkap, email, password, peran) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['nama_lengkap'], $_POST['email'], $hashed_password, $_POST['peran']]);

    } elseif ($action == 'edit') {
        $id_pengguna = $_POST['id_pengguna'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $email = $_POST['email'];
        // Jika admin mengedit dirinya sendiri, perannya tidak bisa diubah.
        $peran = ($id_pengguna == $_SESSION['user_id']) ? $_SESSION['user_peran'] : $_POST['peran'];
        
        if (!empty($_POST['password'])) {
            // Jika password diisi, update password
            $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE pengguna_sistem SET nama_lengkap = ?, email = ?, password = ?, peran = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama_lengkap, $email, $hashed_password, $peran, $id_pengguna]);
        } else {
            // Jika password kosong, jangan update password
            $stmt = $pdo->prepare("UPDATE pengguna_sistem SET nama_lengkap = ?, email = ?, peran = ? WHERE id_pengguna = ?");
            $stmt->execute([$nama_lengkap, $email, $peran, $id_pengguna]);
        }
    } elseif ($action == 'hapus') {
        $id_pengguna = $_POST['id_pengguna'];
        // Keamanan tambahan: pastikan admin tidak menghapus akunnya sendiri
        if ($id_pengguna == $_SESSION['user_id']) {
            die("Error: Anda tidak dapat menghapus akun Anda sendiri.");
        }
        $stmt = $pdo->prepare("DELETE FROM pengguna_sistem WHERE id_pengguna = ?");
        $stmt->execute([$id_pengguna]);
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

header("Location: user-manajemen.php");
exit();
?>
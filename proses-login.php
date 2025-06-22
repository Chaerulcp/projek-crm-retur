<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cari pengguna berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM pengguna_sistem WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Password benar, simpan info pengguna ke session
        $_SESSION['user_id'] = $user['id_pengguna'];
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['user_peran'] = $user['peran'];
        
        // Arahkan ke dashboard sesuai peran
        switch ($user['peran']) {
            case 'Customer Service':
                header("Location: cs-dashboard.php");
                break;
            case 'Gudang':
                header("Location: gudang-dashboard.php");
                break;
            case 'Manajemen':
                header("Location: keuangan-dashboard.php");
                break;
            case 'Admin':
                header("Location: dashboard.php");
                break;
            default:
                header("Location: dashboard.php");
                break;
        }
        exit();
    } else {
        // Password atau email salah
        $_SESSION['login_error'] = "Email atau password yang Anda masukkan salah.";
        header("Location: login.php");
        exit();
    }
}
?>

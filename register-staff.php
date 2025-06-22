<?php
require_once 'config.php';

echo "<h1>Skrip Pendaftaran Staf</h1>";

// =======================================================
// UBAH BAGIAN INI UNTUK MEMBUAT ADMIN
// =======================================================
$nama_lengkap = 'Administrator Utama';
$email = 'admin@tokokita.com';
$password_polos = 'adminpassword123'; // Ganti dengan password yang sangat kuat
$peran = 'Admin'; // Peran baru kita
// =======================================================

// Kode selanjutnya untuk hashing dan insert ke database...
$hashed_password = password_hash($password_polos, PASSWORD_BCRYPT);
try {
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna_sistem WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("<h2>Error:</h2> <p>Email " . htmlspecialchars($email) . " sudah terdaftar.</p>");
    }
    $sql = "INSERT INTO pengguna_sistem (nama_lengkap, email, password, peran) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nama_lengkap, $email, $hashed_password, $peran]);
    echo "<h2>Akun ADMIN Berhasil Dibuat!</h2>";
    echo "<p><strong>Email (untuk login):</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Password Sementara:</strong> " . htmlspecialchars($password_polos) . "</p>";
    echo "<p style='color:red;'><strong>PENTING:</strong> Segera amankan file ini setelah selesai!</p>";
} catch(PDOException $e) {
    die("ERROR: " . $e->getMessage());
}

// ==================== Pendaftaran Staf CS ====================

$nama_lengkap = 'Admin CS';
$email = 'cs@tokokita.com';
$password_polos = 'cs12345';
$peran = 'Customer Service';

$hashed_password = password_hash($password_polos, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna_sistem WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Email " . htmlspecialchars($email) . " sudah terdaftar.");
    }
    $sql = "INSERT INTO pengguna_sistem (nama_lengkap, email, password, peran) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nama_lengkap, $email, $hashed_password, $peran]);
    echo "Pengguna staf berhasil dibuat:<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password_polos) . "<br>";
    echo "Peran: " . htmlspecialchars($peran) . "<br>";
    echo "<strong>Hapus atau ganti nama file ini setelah selesai digunakan!</strong>";
} catch(PDOException $e) {
    die("ERROR: Tidak bisa mendaftarkan staf. " . $e->getMessage());
}

// ==================== Pendaftaran Staf Gudang ====================

echo "<h1>Skrip Pendaftaran Staf Gudang</h1>";

$nama_lengkap = 'Staf Gudang 01';
$email = 'gudang@tokokita.com';
$password_polos = 'gudang123';
$peran = 'Gudang';

$hashed_password = password_hash($password_polos, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna_sistem WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Email " . htmlspecialchars($email) . " sudah terdaftar.");
    }
    $sql = "INSERT INTO pengguna_sistem (nama_lengkap, email, password, peran) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nama_lengkap, $email, $hashed_password, $peran]);
    echo "Pengguna staf gudang berhasil dibuat!<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password_polos) . "<br>";
} catch(PDOException $e) {
    die("ERROR: Tidak bisa mendaftarkan staf. " . $e->getMessage());
}

// ==================== Pendaftaran Manajer Keuangan ====================

echo "<h1>Skrip Pendaftaran Manajer Keuangan</h1>";

$nama_lengkap = 'Manajer Keuangan';
$email = 'finance@tokokita.com';
$password_polos = 'finance123';
$peran = 'Manajemen';

$hashed_password = password_hash($password_polos, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna_sistem WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Email " . htmlspecialchars($email) . " sudah terdaftar.");
    }
    $sql = "INSERT INTO pengguna_sistem (nama_lengkap, email, password, peran) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nama_lengkap, $email, $hashed_password, $peran]);
    echo "Pengguna manajer keuangan berhasil dibuat!<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password_polos) . "<br>";
    echo "Peran: " . htmlspecialchars($peran) . "<br>";
} catch(PDOException $e) {
    die("ERROR: Tidak bisa mendaftarkan manajer keuangan. " . $e->getMessage());
}
?>
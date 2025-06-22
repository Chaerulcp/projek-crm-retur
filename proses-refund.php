<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Manajemen' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Ambil data dari form modal
$id_tiket = $_POST['id_tiket'];
$metode_refund = $_POST['metode_refund'];
$id_staf = $_SESSION['user_id'];
$status_tiket_baru = 'Selesai';

// Proses Upload File Bukti Refund
$nama_file_bukti = null;
if (isset($_FILES['bukti_refund']) && $_FILES['bukti_refund']['error'] == 0) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["bukti_refund"]["name"], PATHINFO_EXTENSION);
    $nama_file_bukti = "refund-" . $id_tiket . "-" . uniqid() . "." . $file_extension;
    if (!move_uploaded_file($_FILES["bukti_refund"]["tmp_name"], $target_dir . $nama_file_bukti)) {
        die("Gagal mengupload file bukti refund.");
    }
} else {
    die("Upload file bukti refund wajib dilakukan.");
}

try {
    $pdo->beginTransaction();

    // 1. Update tiket: status, metode, dan nama file bukti
    $stmt_update = $pdo->prepare(
        "UPDATE tiket_retur SET status_tiket = ?, metode_refund = ?, bukti_refund = ? WHERE id_tiket = ?"
    );
    $success = $stmt_update->execute([$status_tiket_baru, $metode_refund, $nama_file_bukti, $id_tiket]);
    if (!$success) {
        $errorInfo = $stmt_update->errorInfo();
        die("Error updating tiket_retur: " . implode(", ", $errorInfo));
    }

    // 2. Tambahkan catatan penyelesaian
    $catatan = "Proses refund telah diselesaikan melalui $metode_refund. Tiket ditutup.";
    $stmt_insert = $pdo->prepare("INSERT INTO komunikasi_tiket (id_tiket, id_pengirim, tipe_pengirim, pesan) VALUES (?, ?, ?, ?)");
    $stmt_insert->execute([$id_tiket, $id_staf, 'Sistem', $catatan]);
    
    // 3. Kirim notifikasi email final ke pelanggan
    $stmt_info = $pdo->prepare("SELECT t.nomor_tiket, p.nama_pelanggan, p.email_pelanggan FROM tiket_retur t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan WHERE t.id_tiket = ?");
    $stmt_info->execute([$id_tiket]);
    $info = $stmt_info->fetch();

    if ($info) {
        require_once 'fungsi-email.php';
        $subjek = "Proses Refund Selesai untuk Tiket #" . $info['nomor_tiket'];
        $isi_email = "
            Halo " . htmlspecialchars($info['nama_pelanggan']) . ",<br><br>
            Kabar baik! Proses pengembalian dana untuk tiket retur Anda telah berhasil kami proses.<br>
            Dana telah dikirim melalui: <strong>$metode_refund</strong>.<br><br>
            Terima kasih telah berbelanja bersama kami.<br><br>
            Hormat kami,<br>
            Tim TokoKita
        ";
        kirim_email_notifikasi($info['email_pelanggan'], $info['nama_pelanggan'], $subjek, $isi_email);
    }
    
    $pdo->commit();
    header("Location: keuangan-dashboard.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: Gagal memproses refund. " . $e->getMessage());
}
?>

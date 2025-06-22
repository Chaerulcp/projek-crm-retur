<?php
session_start();

// Keamanan: Cek sesi login, peran, dan metode POST
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Gudang' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Ambil data dari form
$id_tiket = $_POST['id_tiket'];
$status_barang = $_POST['status_barang']; // 'Layak' atau 'Tidak Layak'
$id_staf = $_SESSION['user_id'];

// Validasi input
if (empty($id_tiket) || !in_array($status_barang, ['Layak', 'Tidak Layak'])) {
    die("Input tidak valid.");
}

// Tentukan status tiket selanjutnya berdasarkan hasil pemeriksaan
$status_tiket_baru = '';
$catatan_otomatis = '';

if ($status_barang == 'Layak') {
    $status_tiket_baru = 'Refund Diproses'; // Memicu proses keuangan
    $catatan_otomatis = "Barang telah diterima dan dinyatakan LAYAK oleh tim gudang.";
} else { // Jika 'Tidak Layak'
    $status_tiket_baru = 'Selesai';
    $catatan_otomatis = "Barang telah diterima dan dinyatakan TIDAK LAYAK oleh tim gudang. Proses retur dihentikan.";
}

// Enforce that only Gudang role can update status_barang and status_tiket in this step
if ($_SESSION['user_peran'] !== 'Gudang' && $_SESSION['user_peran'] !== 'Admin') {
    die("Error: Anda tidak memiliki izin untuk melakukan aksi ini.");
}

try {
    $pdo->beginTransaction();

    // 1. Update status barang DAN status tiket di tabel tiket_retur
    $stmt_update = $pdo->prepare(
        "UPDATE tiket_retur SET status_barang = ?, status_tiket = ? WHERE id_tiket = ?"
    );
    $stmt_update->execute([$status_barang, $status_tiket_baru, $id_tiket]);

    // 2. Tambahkan catatan otomatis ke riwayat komunikasi
    $stmt_insert = $pdo->prepare(
        "INSERT INTO komunikasi_tiket (id_tiket, id_pengirim, tipe_pengirim, pesan) VALUES (?, ?, ?, ?)"
    );
    $stmt_insert->execute([$id_tiket, $id_staf, 'Sistem', $catatan_otomatis]);

    // --- KIRIM NOTIFIKASI EMAIL ---
    $stmt_info = $pdo->prepare("SELECT t.nomor_tiket, p.nama_pelanggan, p.email_pelanggan FROM tiket_retur t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan WHERE t.id_tiket = ?");
    $stmt_info->execute([$id_tiket]);
    $info = $stmt_info->fetch();

    if ($info) {
        require_once 'fungsi-email.php';
        $subjek = "Pembaruan Status Barang Retur #" . $info['nomor_tiket'];
        $isi_email = "
            Halo " . htmlspecialchars($info['nama_pelanggan']) . ",<br><br>
            Barang untuk tiket retur Anda telah kami terima dan periksa.<br>
            Hasil pemeriksaan: <strong>" . htmlspecialchars($status_barang) . "</strong>.<br>
            Status tiket Anda telah diperbarui menjadi: <strong>$status_tiket_baru</strong>.<br><br>
            Terima kasih,<br>
            Tim TokoKita
        ";
        kirim_email_notifikasi($info['email_pelanggan'], $info['nama_pelanggan'], $subjek, $isi_email);
    }
    // --- AKHIR DARI KODE NOTIFIKASI ---
    
    $pdo->commit();

    // Redirect kembali ke dashboard gudang
    header("Location: gudang-dashboard.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: Gagal memproses aksi gudang. " . $e->getMessage());
}
?>
<?php
session_start();

// Keamanan: Cek sesi login dan metode POST
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Ambil data dari form
$id_tiket = $_POST['id_tiket'];
$status_baru = $_POST['status_baru'];
$pesan = trim($_POST['pesan']);
$id_staf = $_SESSION['user_id'];
$user_peran = $_SESSION['user_peran'] ?? '';

// Pastikan ID tiket valid
if (empty($id_tiket) || empty($status_baru)) {
    die("Data tidak lengkap.");
}

$role_allowed_statuses = [
    'Customer Service' => ['Diajukan', 'Diverifikasi', 'Disetujui', 'Ditolak'],
    'Gudang' => ['Menunggu Barang', 'Barang Diterima', 'Pemeriksaan Gudang', 'Refund Diproses'],
    'Manajemen' => ['Refund Diproses', 'Selesai'],
    'Admin' => ['Diajukan', 'Diverifikasi', 'Disetujui', 'Ditolak', 'Menunggu Barang', 'Barang Diterima', 'Pemeriksaan Gudang', 'Selesai', 'Refund Diproses']
];

// Cek apakah peran user boleh mengubah ke status baru
if ($user_peran !== 'Admin') {
    $allowed_statuses = $role_allowed_statuses[$user_peran] ?? [];
    if (!in_array($status_baru, $allowed_statuses)) {
        die("Error: Anda tidak memiliki izin untuk mengubah status tiket ke '$status_baru'.");
    }
}

// Enforce sequential workflow
// Get current status
$stmt_current = $pdo->prepare("SELECT status_tiket FROM tiket_retur WHERE id_tiket = ?");
$stmt_current->execute([$id_tiket]);
$current_status = $stmt_current->fetchColumn();

$workflow_order = [
    'Diajukan' => 1,
    'Diverifikasi' => 2,
    'Disetujui' => 3,
    'Menunggu Barang' => 4,
    'Barang Diterima' => 5,
    'Pemeriksaan Gudang' => 6,
    'Refund Diproses' => 7,
    'Selesai' => 8,
    'Ditolak' => 9
];

if (isset($workflow_order[$current_status]) && isset($workflow_order[$status_baru])) {
    if ($workflow_order[$status_baru] < $workflow_order[$current_status]) {
        die("Error: Tidak dapat mengubah status ke status sebelumnya dalam alur kerja.");
    }
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // 1. Update status di tabel tiket_retur
    $stmt_update = $pdo->prepare("UPDATE tiket_retur SET status_tiket = ?, id_cs = ? WHERE id_tiket = ?");
    $stmt_update->execute([$status_baru, $id_staf, $id_tiket]);

    // 2. Tambahkan catatan ke tabel komunikasi_tiket jika ada pesan
    if (!empty($pesan)) {
        $stmt_insert = $pdo->prepare("INSERT INTO komunikasi_tiket (id_tiket, id_pengirim, tipe_pengirim, pesan) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$id_tiket, $id_staf, 'Staf', $pesan]);
    }

    // --- KIRIM NOTIFIKASI EMAIL ---
    // Ambil data tiket & pelanggan untuk notifikasi
    $stmt_info = $pdo->prepare("SELECT t.nomor_tiket, p.nama_pelanggan, p.email_pelanggan FROM tiket_retur t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan WHERE t.id_tiket = ?");
    $stmt_info->execute([$id_tiket]);
    $info = $stmt_info->fetch();

    if ($info) {
        require_once 'fungsi-email.php';
        $subjek = "Pembaruan Status Tiket Retur #" . $info['nomor_tiket'];
        $isi_email = "
            Halo " . htmlspecialchars($info['nama_pelanggan']) . ",<br><br>
            Ada pembaruan untuk tiket retur Anda. Status tiket Anda sekarang adalah: <strong>$status_baru</strong>.<br><br>
            Catatan dari tim kami: <br><i>" . (!empty($pesan) ? nl2br(htmlspecialchars($pesan)) : 'Tidak ada catatan tambahan.') . "</i><br><br>
            Anda dapat melihat detailnya di portal pelanggan kami.<br><br>
            Terima kasih,<br>
            Tim TokoKita
        ";
        kirim_email_notifikasi($info['email_pelanggan'], $info['nama_pelanggan'], $subjek, $isi_email);
    }
    // --- AKHIR DARI KODE NOTIFIKASI ---

    // Jika semua berhasil, commit transaksi
    $pdo->commit();

    // Redirect kembali ke halaman detail tiket
    header("Location: detail-tiket.php?id=" . $id_tiket);
    exit();

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    $pdo->rollBack();
    die("Error: Gagal memproses aksi. " . $e->getMessage());
}
?>
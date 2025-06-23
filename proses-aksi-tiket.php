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
        $isi_email = '
        <html>
        <head>
          <style>
            body {
              font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
              background-color: #f9f9f9;
              margin: 0;
              padding: 0;
              color: #333333;
            }
            .container {
              background-color: #ffffff;
              margin: 30px auto;
              padding: 30px;
              max-width: 600px;
              border-radius: 10px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.1);
              border: 1px solid #e0e0e0;
            }
            h2 {
              color: #2c3e50;
              font-weight: 700;
              margin-bottom: 20px;
            }
            p {
              font-size: 16px;
              line-height: 1.6;
              margin-bottom: 20px;
            }
            .button {
              display: inline-block;
              padding: 12px 25px;
              font-size: 16px;
              color: #ffffff;
              background-color: #3498db;
              border-radius: 5px;
              text-decoration: none;
              font-weight: 600;
              box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
              transition: background-color 0.3s ease;
            }
            .button:hover {
              background-color: #2980b9;
            }
            .footer {
              font-size: 12px;
              color: #999999;
              margin-top: 30px;
              text-align: center;
              border-top: 1px solid #e0e0e0;
              padding-top: 15px;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <h2>Pembaruan Status Tiket Retur</h2>
            <p>Halo ' . htmlspecialchars($info['nama_pelanggan']) . ',</p>
            <p>Ada pembaruan untuk tiket retur Anda. Status tiket Anda sekarang adalah: <strong>' . $status_baru . '</strong>.</p>
            <p>Catatan dari tim kami:</p>
            <p><i>' . (!empty($pesan) ? nl2br(htmlspecialchars($pesan)) : 'Tidak ada catatan tambahan.') . '</i></p>
            <p>Anda dapat memantau status pengajuan retur Anda secara langsung dengan mengklik tombol di bawah ini:</p>
            <p><a href="http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/status-tiket.php?nomor_tiket=' . urlencode($info['nomor_tiket']) . '" class="button" target="_blank" rel="noopener">Cek Status Retur</a></p>
            <p>Terima kasih,<br>Tim TokoKita</p>
            <div class="footer">Jika Anda tidak melakukan perubahan ini, harap abaikan email ini.</div>
          </div>
        </body>
        </html>
        ';
        $email_terkirim = kirim_email_notifikasi($info['email_pelanggan'], $info['nama_pelanggan'], $subjek, $isi_email);
        if (!$email_terkirim) {
            error_log("Gagal mengirim email notifikasi untuk tiket #" . $info['nomor_tiket'] . " ke " . $info['email_pelanggan'] . "\n", 3, __DIR__ . '/email-error.log');
            // Optional: You can also display an error message or handle it as needed
            // echo "Gagal mengirim email notifikasi.";
        }
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
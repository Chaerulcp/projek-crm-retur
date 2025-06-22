<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
require_once 'config.php';

// Validasi input nomor tiket
if (!isset($_GET['nomor_tiket']) || empty(trim($_GET['nomor_tiket']))) {
    // Jika tidak ada nomor tiket, kembali ke halaman pengecekan
    header("Location: cek-tiket.php");
    exit();
}

$nomor_tiket = trim($_GET['nomor_tiket']);

// Ambil detail tiket dari database
$stmt = $pdo->prepare("
    SELECT t.*, p.nama_pelanggan, pr.nama_produk
    FROM tiket_retur t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN produk pr ON t.id_produk = pr.id_produk
    WHERE t.nomor_tiket = ?
");
$stmt->execute([$nomor_tiket]);
$tiket = $stmt->fetch();

$chat_active = false;
if ($tiket) {
    // Ambil status chat_active dari tiket_retur
    $stmt_chat = $pdo->prepare("SELECT chat_active FROM tiket_retur WHERE id_tiket = ?");
    $stmt_chat->execute([$tiket['id_tiket']]);
    $chat_row = $stmt_chat->fetch(PDO::FETCH_ASSOC);
    $chat_active = $chat_row ? (bool)$chat_row['chat_active'] : false;

    // Jika tiket ditemukan, ambil riwayat komunikasinya
    $stmt_komunikasi = $pdo->prepare("
        SELECT k.*, u.nama_lengkap as nama_staf
        FROM komunikasi_tiket k
        LEFT JOIN pengguna_sistem u ON k.id_pengirim = u.id_pengguna
        WHERE k.id_tiket = ? ORDER BY k.tanggal_kirim ASC
    ");
    $stmt_komunikasi->execute([$tiket['id_tiket']]);
    $komunikasi_list = $stmt_komunikasi->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Tiket #<?php echo htmlspecialchars($nomor_tiket); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #f8f9fa;">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        </nav>
    <div class="container mt-5">
        <!-- Debug: User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'; ?> -->
        <!-- Debug: User Role: <?php echo isset($_SESSION['user_peran']) ? $_SESSION['user_peran'] : 'not set'; ?> -->
        <?php if ($tiket): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Status Tiket #<?php echo htmlspecialchars($tiket['nomor_tiket']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></div>
                        <div class="col-md-6"><strong>Produk:</strong> <?php echo htmlspecialchars($tiket['nama_produk']); ?></div>
                    </div>
                    <hr>
                    <h5 class="mt-4">Progres Saat Ini: <span class="text-primary fw-bold"><?php echo htmlspecialchars($tiket['status_tiket']); ?></span></h5>
                    
                    <?php
                        // Logika untuk progress bar
                        $progress = 0;
                        if (in_array($tiket['status_tiket'], ['Diverifikasi', 'Disetujui', 'Menunggu Barang'])) $progress = 25;
                        if (in_array($tiket['status_tiket'], ['Barang Diterima', 'Pemeriksaan Gudang'])) $progress = 50;
                        if (in_array($tiket['status_tiket'], ['Refund Diproses'])) $progress = 75;
                        if ($tiket['status_tiket'] == 'Selesai') $progress = 100;
                        if ($tiket['status_tiket'] == 'Ditolak') $progress = 100;
                    ?>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar <?php echo ($tiket['status_tiket'] == 'Ditolak') ? 'bg-danger' : 'bg-success'; ?> progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: <?php echo $progress; ?>%;" 
                             aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                             <?php echo $progress; ?>%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 small text-muted">
                        <span>Diajukan</span>
                        <span>Diproses</span>
                        <span>Refund</span>
                        <span>Selesai</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat & Log Komunikasi</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($komunikasi_list as $kom): ?>
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi <?php echo ($kom['tipe_pengirim'] == 'Staf' || $kom['tipe_pengirim'] == 'Sistem') ? 'bi-headset' : 'bi-person'; ?> fs-2 text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong><?php echo ($kom['tipe_pengirim'] == 'Staf') ? htmlspecialchars($kom['nama_staf']) : 'Anda (Pelanggan)'; ?></strong>
                            <small class="text-muted float-end"><?php echo date('d M Y, H:i', strtotime($kom['tanggal_kirim'])); ?></small>
                            <div class="p-2 mt-1" style="background-color: #f1f1f1; border-radius: 8px;">
                                <?php echo nl2br(htmlspecialchars($kom['pesan'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Live Chat</h5>
                </div>
                <div class="card-body">
                    <div id="chat-box" style="height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
                        Loading messages...
                    </div>
                    <div class="mt-3">
                        <textarea id="chat-input" class="form-control" rows="3" placeholder="Type your message..."></textarea>
                        <button id="send-btn" class="btn btn-primary mt-2">Send</button>
                    </div>
                    <div id="chat-status-message" class="mt-2 text-muted" style="display:none;">
                        Chat is currently inactive or you do not have permission to send messages.
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center">
                <img src="https://via.placeholder.com/150/FFC107/000000?Text=Not+Found" class="mb-4" alt="Not Found">
                <h2>Tiket Tidak Ditemukan</h2>
                <p class="lead text-muted">Maaf, nomor tiket <strong>"<?php echo htmlspecialchars($nomor_tiket); ?>"</strong> tidak dapat kami temukan di sistem. <br>Mohon periksa kembali nomor tiket Anda.</p>
                <a href="cek-tiket.php" class="btn btn-primary mt-3">Coba Lagi</a>
            </div>
        <?php endif; ?>
    </div>
    <script>
    <?php if ($tiket): ?>
        var idTiket = <?php echo json_encode($tiket['id_tiket']); ?>;
        var chatActive = <?php echo json_encode($chat_active); ?>;
        var chatActiveInitial = <?php echo json_encode($chat_active); ?>;
        <?php
            $user_peran = isset($_SESSION['user_peran']) ? $_SESSION['user_peran'] : null;
            $is_admin = in_array($user_peran, ['Customer Service', 'Gudang', 'Manajemen', 'Admin']);
        ?>
        var isAdmin = <?php echo json_encode($is_admin); ?>;
        var nomorTiket = <?php echo json_encode($nomor_tiket); ?>;
    <?php else: ?>
        var idTiket = null;
        var chatActive = false;
        var chatActiveInitial = false;
        var isAdmin = false;
        var nomorTiket = null;
    <?php endif; ?>


    document.addEventListener('DOMContentLoaded', function () {
        if (window.setChatActive) {
            window.setChatActive(chatActive);
        }
        var chatStatusMessage = document.getElementById('chat-status-message');
        var chatInput = document.getElementById('chat-input');
        var sendBtn = document.getElementById('send-btn');
        if (!chatActive) {
            if (chatStatusMessage) chatStatusMessage.style.display = 'block';
            if (chatInput) chatInput.disabled = true;
            if (sendBtn) sendBtn.disabled = true;
        } else {
            if (chatStatusMessage) chatStatusMessage.style.display = 'none';
        }
    });
    </script>
    <script src="assets/js/chat.js"></script>
</body>
</html>

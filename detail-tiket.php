<?php
session_start();
require_once 'config.php';

// Keamanan: Cek sesi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validasi ID tiket dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$id_tiket = $_GET['id'];

// Ambil semua detail tiket dari database
$stmt = $pdo->prepare("
    SELECT t.*, p.nama_pelanggan, p.email_pelanggan, p.telepon_pelanggan,
           pr.nama_produk, u.nama_lengkap as nama_cs
    FROM tiket_retur t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN produk pr ON t.id_produk = pr.id_produk
    LEFT JOIN pengguna_sistem u ON t.id_cs = u.id_pengguna
    WHERE t.id_tiket = ?
");
$stmt->execute([$id_tiket]);
$tiket = $stmt->fetch();

// Jika tiket tidak ditemukan, kembali ke dashboard
if (!$tiket) {
    header("Location: dashboard.php");
    exit();
}

// Ambil riwayat komunikasi untuk tiket ini
$stmt_komunikasi = $pdo->prepare("
    SELECT k.*, u.nama_lengkap as nama_staf
    FROM komunikasi_tiket k
    LEFT JOIN pengguna_sistem u ON k.id_pengirim = u.id_pengguna
    WHERE k.id_tiket = ? ORDER BY k.tanggal_kirim ASC
");
$stmt_komunikasi->execute([$tiket['id_tiket']]);
$komunikasi_list = $stmt_komunikasi->fetchAll();

// Fungsi untuk membuat badge status (agar konsisten dengan dasbor)
function getStatusBadge($status) {
    $status_lower = strtolower($status);
    $badge_class = 'bg-secondary';
    switch ($status_lower) {
        case 'diajukan': $badge_class = 'bg-primary'; break;
        case 'diverifikasi': case 'disetujui': case 'pemeriksaan gudang': $badge_class = 'bg-info text-dark'; break;
        case 'menunggu barang': case 'barang diterima': $badge_class = 'bg-warning text-dark'; break;
        case 'refund diproses': $badge_class = 'bg-purple'; break;
        case 'selesai': $badge_class = 'bg-success'; break;
        case 'ditolak': $badge_class = 'bg-danger'; break;
    }
    return '<span class="badge rounded-pill fs-6 ' . $badge_class . '">' . htmlspecialchars($status) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket #<?php echo htmlspecialchars($tiket['nomor_tiket']); ?> - CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Mengambil Gaya CSS yang sama dari dasbor untuk konsistensi */
        body { display: flex; min-height: 100vh; background-color: #f0f2f5; }
        .sidebar { width: 260px; flex-shrink: 0; background-color: #212529; color: white; }
        .sidebar .nav-link { color: #adb5bd; padding: 0.75rem 1.25rem; border-radius: 0.375rem; transition: all 0.2s ease-in-out; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #343a40; color: white; transform: translateX(5px); }
        .sidebar .nav-link .bi { margin-right: 12px; }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; }
        .top-navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,.05); }
        .content-wrapper { flex-grow: 1; padding: 2rem; overflow-y: auto; }
        .bg-purple { background-color: #6f42c1 !important; color: white; }

        /* Gaya baru untuk riwayat komunikasi chat */
        .chat-log { max-height: 450px; overflow-y: auto; }
        .chat-bubble { display: flex; margin-bottom: 1rem; }
        .chat-bubble .chat-content {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
        }
        .chat-bubble.staf { flex-direction: row-reverse; }
        .chat-bubble.staf .chat-content {
            background-color: #0d6efd;
            color: white;
            border-top-right-radius: 0.25rem;
        }
        .chat-bubble.pelanggan .chat-content {
            background-color: #e9ecef;
            color: #212529;
            border-top-left-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="sidebar p-3 d-flex flex-column">
        <h4 class="text-center mb-4"><i class="bi bi-box-seam-fill"></i> CRM RETUR</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            </ul>
        <hr>
        </div>
    <div class="main-content">
        <nav class="navbar top-navbar">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Dasbor [<?php echo htmlspecialchars($_SESSION['user_peran']); ?>]</span>
            </div>
        </nav>
        <div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Detail Tiket #<?php echo htmlspecialchars($tiket['nomor_tiket']); ?></h3>
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dasbor</a>
            </div>

            <div class="row g-4">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Status & Aksi</h5>
                        <div><?php echo getStatusBadge($tiket['status_tiket']); ?></div>
                    </div>
                    <div class="card-body">
                        <form action="proses-aksi-tiket.php" method="POST">
                            <input type="hidden" name="id_tiket" value="<?php echo $id_tiket; ?>">
                            <div class="mb-3">
                                <label for="status_baru" class="form-label">Ubah Status Menjadi:</label>
<?php
// Define allowed statuses per role to match backend in proses-aksi-tiket.php
$role_allowed_statuses = [
    'Customer Service' => ['Diajukan', 'Diverifikasi', 'Ditolak'],
    'Gudang' => ['Menunggu Barang', 'Barang Diterima', 'Pemeriksaan Gudang'],
    'Manajemen' => ['Diajukan', 'Diverifikasi', 'Ditolak', 'Selesai'],
    'Admin' => ['Diajukan', 'Diverifikasi', 'Disetujui', 'Ditolak', 'Menunggu Barang', 'Barang Diterima', 'Pemeriksaan Gudang', 'Selesai', 'Refund Diproses']
];

// Get current user role from session
$user_peran = $_SESSION['user_peran'] ?? '';

// Get allowed statuses for current role, default empty array if role unknown
$allowed_statuses = $role_allowed_statuses[$user_peran] ?? [];

?>
                                <select name="status_baru" id="status_baru" class="form-select">
                                    <?php foreach ($allowed_statuses as $status_option): ?>
                                        <option value="<?php echo htmlspecialchars($status_option); ?>"><?php echo htmlspecialchars($status_option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="pesan" class="form-label">Tambah Catatan / Pesan untuk Pelanggan:</label>
                                <textarea name="pesan" id="pesan" class="form-control" rows="3" placeholder="Tulis catatan di sini..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                        <hr>
                        <div class="mb-3">
                            <label for="chat-active-toggle" class="form-label fw-bold">Aktifkan Live Chat untuk Tiket Ini:</label>
                            <?php
                            $allowed_roles_for_chat = ['Customer Service', 'Gudang', 'Manajemen', 'Admin'];
                            $can_toggle_chat = in_array($user_peran, $allowed_roles_for_chat);
                            ?>
                            <?php if ($can_toggle_chat): ?>
                                <input type="checkbox" id="chat-active-toggle" <?php echo $tiket['chat_active'] ? 'checked' : ''; ?>>
                                <small class="form-text text-muted">Centang untuk mengaktifkan live chat pada tiket ini.</small>
                            <?php else: ?>
                                <p class="text-muted">Anda tidak memiliki izin untuk mengaktifkan live chat pada tiket ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light"><h5 class="mb-0 fw-bold">Informasi Detail</h5></div>
                        <div class="card-body">
                            <h6><i class="bi bi-person text-primary"></i> Info Pelanggan</h6>
                            <p class="mb-1 text-muted"><strong>Nama:</strong> <?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></p>
                            <p class="text-muted"><strong>Email:</strong> <?php echo htmlspecialchars($tiket['email_pelanggan']); ?></p>
                            <hr>
                            <h6><i class="bi bi-box-seam text-primary"></i> Info Produk & Retur</h6>
                            <p class="mb-1 text-muted"><strong>Produk:</strong> <?php echo htmlspecialchars($tiket['nama_produk']); ?></p>
                            <p class="mb-1 text-muted"><strong>Invoice:</strong> <?php echo htmlspecialchars($tiket['nomor_invoice']); ?></p>
                            <p class="mb-1 text-muted"><strong>Alasan:</strong><br> <?php echo nl2br(htmlspecialchars($tiket['alasan_retur'])); ?></p>
                             <hr>
                            <h6><i class="bi bi-image text-primary"></i> Bukti dari Pelanggan</h6>
                            <?php
                            if ($tiket['bukti_foto']) {
                                $buktiArray = json_decode($tiket['bukti_foto'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($buktiArray)) {
                                    foreach ($buktiArray as $buktiFile) {
                                        $safeFile = htmlspecialchars($buktiFile);
                                        echo '<a href="uploads/' . $safeFile . '" target="_blank" class="d-inline-block me-2 mb-2">';
                                        echo '<img src="uploads/' . $safeFile . '" alt="Bukti Foto" class="img-fluid rounded border p-1" style="max-width: 150px;">';
                                        echo '</a>';
                                    }
                                } else {
                                    // fallback if not a JSON array
                                    $safeFile = htmlspecialchars($tiket['bukti_foto']);
                                    echo '<a href="uploads/' . $safeFile . '" target="_blank">';
                                    echo '<img src="uploads/' . $safeFile . '" alt="Bukti Foto" class="img-fluid rounded border p-1 mt-2">';
                                    echo '</a>';
                                }
                            } else {
                                echo '<p class="text-muted fst-italic">Tidak ada bukti yang diunggah.</p>';
                            }
                            ?>

                            <hr>
                            <h6><i class="bi bi-image text-success"></i> Bukti Refund Pembayaran</h6>
                            <?php if (!empty($tiket['bukti_refund'])): ?>
                                <a href="uploads/<?php echo htmlspecialchars($tiket['bukti_refund']); ?>" target="_blank">
                                    <img src="uploads/<?php echo htmlspecialchars($tiket['bukti_refund']); ?>" alt="Bukti Refund" class="img-fluid rounded border p-1 mt-2">
                                </a>
                            <?php else: ?>
                                <p class="text-muted fst-italic">Bukti refund belum diunggah.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light"><h5 class="mb-0 fw-bold">Riwayat Komunikasi</h5></div>
                        <div class="card-body chat-log p-3" id="chat-box">
                            <?php
                            // Fetch old komunikasi_tiket messages
                            $stmt_old = $pdo->prepare("
                                SELECT k.pesan AS message, k.tanggal_kirim AS timestamp, 'komunikasi' AS source,
                                    CASE WHEN k.tipe_pengirim = 'Staf' THEN 1 ELSE 0 END AS is_staf,
                                    u.nama_lengkap AS sender_name
                                FROM komunikasi_tiket k
                                LEFT JOIN pengguna_sistem u ON k.id_pengirim = u.id_pengguna
                                WHERE k.id_tiket = ?
                            ");
                            $stmt_old->execute([$tiket['id_tiket']]);
                            $old_messages = $stmt_old->fetchAll(PDO::FETCH_ASSOC);

                            // Fetch new chat_messages
                            $stmt_new = $pdo->prepare("
                                SELECT cm.id, cm.message, cm.timestamp, 'chat' AS source,
                                    CASE WHEN cm.sender_role IN ('Customer Service', 'Gudang', 'Manajemen', 'Admin') THEN 1 ELSE 0 END AS is_staf,
                                    ps.nama_lengkap AS sender_name
                                FROM chat_messages cm
                                LEFT JOIN pengguna_sistem ps ON cm.sender_id = ps.id_pengguna
                                WHERE cm.id_tiket = ?
                            ");
                            $stmt_new->execute([$tiket['id_tiket']]);
                            $new_messages = $stmt_new->fetchAll(PDO::FETCH_ASSOC);

                            // Merge and sort by timestamp ascending
                            // DEBUG: Output fetched messages for inspection
                            echo '<pre style="display:none;">Old messages: ' . print_r($old_messages, true) . '</pre>';
                            echo '<pre style="display:none;">New messages: ' . print_r($new_messages, true) . '</pre>';

                            $all_messages = array_merge($old_messages, $new_messages);
                            usort($all_messages, function($a, $b) {
                                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
                            });
                            ?>
                            <?php if (empty($all_messages)): ?>
                                <div class="text-center text-muted p-5">
                                    <i class="bi bi-chat-dots fs-1"></i>
                                    <p class="mt-2">Belum ada komunikasi untuk tiket ini.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach($all_messages as $msg):
                                    $isStaf = $msg['is_staf'] == 1;
                                    $sender_name = $isStaf ? htmlspecialchars($msg['sender_name'] ?? 'Staf') : 'Pelanggan';
                                ?>
                                    <div class="chat-bubble <?php echo $isStaf ? 'staf' : 'pelanggan'; ?>">
                                        <div class="chat-content">
                                            <p class="mb-1">
                                                <strong><?php echo $sender_name; ?></strong>
                                            </p>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                            <small class="d-block text-end mt-2" style="opacity: 0.7;"><?php echo date('d M Y, H:i', strtotime($msg['timestamp'])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="live-chat-container" style="display: <?php echo $tiket['chat_active'] ? 'block' : 'none'; ?>; margin-top: 1rem;">
                <textarea id="chat-input" class="form-control" rows="3" placeholder="Tulis pesan..."></textarea>
                <button id="send-btn" class="btn btn-primary mt-2">Kirim</button>
                <div id="chat-status-message" class="mt-2 text-muted" style="display:none;">
                    Chat is currently inactive or you do not have permission to send messages.
                </div>
            </div>

    </div> <footer class="p-3 mt-auto bg-light text-center border-top">
            <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> CRM Retur TokoKita.</p>
        </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const nomorTiket = <?php echo json_encode($tiket['nomor_tiket']); ?>;
        window.idTiket = <?php echo json_encode($id_tiket); ?>;
        const chatActiveInitial = <?php echo json_encode((bool)$tiket['chat_active']); ?>;
        const isAdmin = <?php echo json_encode(in_array($_SESSION['user_peran'], ['Customer Service', 'Gudang', 'Manajemen', 'Admin'])); ?>;
    </script>
    <script src="assets/js/chat.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatToggle = document.getElementById('chat-active-toggle');
            const liveChatContainer = document.getElementById('live-chat-container');
            if (chatToggle) {
                chatToggle.addEventListener('change', function () {
                    const isActive = chatToggle.checked;
                    const idTiket = <?php echo json_encode($tiket['id_tiket']); ?>;

                    // Show or hide live chat container based on toggle
                    if (liveChatContainer) {
                        liveChatContainer.style.display = isActive ? 'block' : 'none';
                    }

fetch('chat-activation.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nomor_tiket: <?php echo json_encode($tiket['nomor_tiket']); ?>, chat_active: isActive })
})
.then(response => response.json())
.then(data => {
    if (data.error) {
        alert('Error updating chat activation: ' + data.error);
        // Revert checkbox state on error
        chatToggle.checked = !isActive;
        if (liveChatContainer) {
            liveChatContainer.style.display = !isActive ? 'block' : 'none';
        }
    }
})
.catch(err => {
    alert('Failed to update chat activation.');
    // Revert checkbox state on error
    chatToggle.checked = !isActive;
    if (liveChatContainer) {
        liveChatContainer.style.display = !isActive ? 'block' : 'none';
    }
});
                });
            }
        });

        // Sync chat-active-toggle checkbox with chat.js isActive state
        document.addEventListener('DOMContentLoaded', function () {
            const chatToggle = document.getElementById('chat-active-toggle');
            if (chatToggle && window.setChatActive) {
                // Initialize chat active state
                window.setChatActive(chatToggle.checked);

                chatToggle.addEventListener('change', function () {
                    window.setChatActive(chatToggle.checked);
                });
            }
        });
    </script>
</body>
</html>

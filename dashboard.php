<?php
session_start();
require_once 'config.php';

// Keamanan: Pastikan hanya staf yang login yang bisa mengakses
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_peran = $_SESSION['user_peran'] ?? '';

// Redirect Customer Service users to cs-dashboard.php
if ($user_peran === 'Customer Service') {
    header("Location: cs-dashboard.php");
    exit();
}

// === Logika PHP untuk Menyiapkan Data Dasbor ===
$stmt = $pdo->query("SELECT status_tiket FROM tiket_retur");
$all_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_tickets = count($all_tickets);
$completed_tickets = 0;
$pending_tickets = 0;

foreach ($all_tickets as $ticket) {
    $status = strtolower($ticket['status_tiket']);
    if ($status === 'selesai' || $status === 'ditolak') {
        $completed_tickets++;
    }
}
$pending_tickets = $total_tickets - $completed_tickets;

// Ambil 5 tiket terbaru untuk ditampilkan di daftar
$latest_tickets_stmt = $pdo->query("
    SELECT t.id_tiket, t.nomor_tiket, t.status_tiket, t.dibuat_pada, p.nama_pelanggan, pr.nama_produk
    FROM tiket_retur t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN produk pr ON t.id_produk = pr.id_produk
    ORDER BY t.dibuat_pada DESC
    LIMIT 5
");
$latest_tickets = $latest_tickets_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk membuat badge status
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
    return '<span class="badge rounded-pill ' . $badge_class . '">' . htmlspecialchars($status) . '</span>';
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Ringkasan Dasbor</h3>

    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="icon-circle bg-primary text-white"><i class="bi bi-journal-text"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0"><?php echo $total_tickets; ?></h2>
                        <p class="text-muted mb-0">Total Tiket</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="icon-circle bg-warning text-dark"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0"><?php echo $pending_tickets; ?></h2>
                        <p class="text-muted mb-0">Tiket Berjalan</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card summary-card">
                <div class="card-body p-4">
                    <div class="icon-circle bg-success text-white"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0"><?php echo $completed_tickets; ?></h2>
                        <p class="text-muted mb-0">Tiket Tuntas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="fw-bold mb-4">5 Tiket Terbaru</h3>

    <div class="list-group">
        <?php if (empty($latest_tickets)): ?>
            <div class="text-center text-muted p-5 bg-light rounded">
                <i class="bi bi-ticket-detailed fs-1"></i>
                <p class="mt-2 mb-0">Belum ada tiket retur yang diajukan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($latest_tickets as $tiket): ?>
                <a href="detail-tiket.php?id=<?php echo $tiket['id_tiket']; ?>" class="list-group-item list-group-item-action p-3 ticket-list-card">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 fw-semibold text-primary"><?php echo htmlspecialchars($tiket['nomor_tiket']); ?></h5>
                        <small class="text-muted"><?php echo date('d M Y', strtotime($tiket['dibuat_pada'])); ?></small>
                    </div>
                    <p class="mb-1 text-muted">
                        <span class="fw-bold"><?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></span> - 
                        <?php echo htmlspecialchars($tiket['nama_produk']); ?>
                    </p>
                    <div class="mt-2">
                        <?php echo getStatusBadge($tiket['status_tiket']); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'template-footer.php'; ?>

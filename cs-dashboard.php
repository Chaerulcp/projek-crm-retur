<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Customer Service') {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Ambil tiket dengan status awal yang perlu ditangani CS
$stmt = $pdo->query("
    SELECT t.id_tiket, t.nomor_tiket, t.status_tiket, p.nama_pelanggan, pr.nama_produk
    FROM tiket_retur t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN produk pr ON t.id_produk = pr.id_produk
    WHERE t.status_tiket IN ('Diajukan', 'Diverifikasi', 'Disetujui')
    ORDER BY t.diperbarui_pada ASC
");
$tiket_list = $stmt->fetchAll();

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3>Daftar Tiket Retur - Customer Service</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nomor Tiket</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tiket_list)): ?>
                <tr><td colspan="5" class="text-center">Tidak ada tiket yang perlu diproses.</td></tr>
            <?php else: ?>
                <?php foreach ($tiket_list as $tiket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tiket['nomor_tiket']); ?></td>
                    <td><?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></td>
                    <td><?php echo htmlspecialchars($tiket['nama_produk']); ?></td>
                    <td><?php echo htmlspecialchars($tiket['status_tiket']); ?></td>
                    <td>
                        <a href="detail-tiket.php?id=<?php echo $tiket['id_tiket']; ?>" class="btn btn-sm btn-primary">Lihat & Update</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'template-footer.php'; ?>

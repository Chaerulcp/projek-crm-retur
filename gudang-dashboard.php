<?php
session_start();

// Keamanan: Cek sesi login dan peran pengguna
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_peran'], ['Gudang', 'Admin'])) {
    // Jika bukan staf gudang atau admin, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$gudang_statuses = ['Menunggu Barang', 'Barang Diterima', 'Pemeriksaan Gudang'];

// Ambil tiket yang relevan untuk gudang (status di tahap gudang)
$placeholders = implode(',', array_fill(0, count($gudang_statuses), '?'));
$stmt = $pdo->prepare("
    SELECT 
        t.id_tiket, t.nomor_tiket, t.status_tiket,
        p.nama_pelanggan,
        pr.nama_produk
    FROM tiket_retur t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN produk pr ON t.id_produk = pr.id_produk
    WHERE t.status_tiket IN ($placeholders) AND t.status_barang = 'Belum Diterima'
    ORDER BY t.diperbarui_pada ASC
");
$stmt->execute($gudang_statuses);
$tiket_list = $stmt->fetchAll();

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3>Daftar Pemeriksaan Barang Retur</h3>
    <p>Berikut adalah daftar barang yang perlu diterima dan diperiksa kondisinya.</p>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nomor Tiket</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Aksi Pemeriksaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tiket_list)): ?>
                        <tr><td colspan="4" class="text-center">Tidak ada tugas pemeriksaan saat ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tiket_list as $tiket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tiket['nomor_tiket']); ?></td>
                            <td><?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($tiket['nama_produk']); ?></td>
                            <td>
                                <form action="proses-aksi-gudang.php" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="id_tiket" value="<?php echo $tiket['id_tiket']; ?>">
                                    <select name="status_barang" class="form-select form-select-sm">
                                        <option value="Layak">Barang Layak</option>
                                        <option value="Tidak Layak">Barang Tidak Layak</option>
                                    </select>
                                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'template-footer.php'; ?>

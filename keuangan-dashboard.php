<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_peran'], ['Manajemen', 'Admin'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

$stmt = $pdo->query("
    SELECT t.id_tiket, t.nomor_tiket, p.nama_pelanggan, pr.nama_produk
    FROM tiket_retur t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN produk pr ON t.id_produk = pr.id_produk
    WHERE t.status_tiket = 'Refund Diproses'
    ORDER BY t.diperbarui_pada ASC
");
$tiket_list = $stmt->fetchAll();

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3>Daftar Permintaan Refund</h3>
    <table class="table table-striped">
        <thead>
            <tr><th>Nomor Tiket</th><th>Pelanggan</th><th>Produk</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($tiket_list as $tiket): ?>
            <tr>
                <td><?php echo htmlspecialchars($tiket['nomor_tiket']); ?></td>
                <td><?php echo htmlspecialchars($tiket['nama_pelanggan']); ?></td>
                <td><?php echo htmlspecialchars($tiket['nama_produk']); ?></td>
                <td>
                    <button class="btn btn-sm btn-warning proses-refund-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#refundModal"
                            data-idtiket="<?php echo $tiket['id_tiket']; ?>">
                        Proses Refund
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($tiket_list)): ?>
                <tr><td colspan="4" class="text-center">Tidak ada permintaan refund yang perlu diproses.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Formulir Konfirmasi Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses-refund.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_tiket" id="modal_id_tiket">
                    <div class="mb-3">
                        <label for="metode_refund" class="form-label">Metode Pengembalian Dana</label>
                        <select name="metode_refund" class="form-select" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Saldo E-Wallet">Saldo E-Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bukti_refund" class="form-label">Unggah Bukti Transfer</label>
                        <input type="file" name="bukti_refund" class="form-control" required>
                        <div class="form-text">Unggah screenshot atau bukti transaksi.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tandai Selesai & Kirim Notifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script untuk mengirim ID tiket ke dalam modal
    document.addEventListener('DOMContentLoaded', function () {
        var refundModal = document.getElementById('refundModal');
        refundModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var idTiket = button.getAttribute('data-idtiket');
            var modalInput = refundModal.querySelector('#modal_id_tiket');
            modalInput.value = idTiket;
        });
    });
</script>

<?php require_once 'template-footer.php'; ?>

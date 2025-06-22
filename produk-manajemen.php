<?php
session_start();
// Hanya Admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Ambil semua data produk
$produk_list = $pdo->query("SELECT * FROM produk ORDER BY id_produk DESC")->fetchAll();

// Cek jika sedang mode edit untuk mengisi data di form
$is_edit_mode = false;
$produk_to_edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id_produk = ?");
    $stmt->execute([$_GET['edit']]);
    $produk_to_edit = $stmt->fetch();
    if ($produk_to_edit) {
        $is_edit_mode = true;
    }
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Manajemen Data Produk</h3>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold"><?php echo $is_edit_mode ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form action="proses-produk.php" method="POST">
                        <input type="hidden" name="action" value="<?php echo $is_edit_mode ? 'edit' : 'tambah'; ?>">
                        <?php if ($is_edit_mode): ?>
                            <input type="hidden" name="id_produk" value="<?php echo $produk_to_edit['id_produk']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input type="text" id="nama_produk" name="nama_produk" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($produk_to_edit['nama_produk']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU (Kode Unik Produk)</label>
                            <input type="text" id="sku" name="sku" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($produk_to_edit['sku']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?php echo $is_edit_mode ? htmlspecialchars($produk_to_edit['deskripsi']) : ''; ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $is_edit_mode ? 'Simpan Perubahan' : 'Tambah Produk'; ?></button>
                            <?php if ($is_edit_mode): ?>
                                <a href="produk-manajemen.php" class="btn btn-secondary mt-2">Batal Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produk_list)): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data produk.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($produk_list as $produk): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($produk['sku']); ?></td>
                                        <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                                        <td class="text-center">
                                            <a href="produk-manajemen.php?edit=<?php echo $produk['id_produk']; ?>" class="btn btn-sm btn-info" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <form action="proses-produk.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="hapus">
                                                <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus produk ini?')"><i class="bi bi-trash-fill"></i></button>
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
        </div>
    </div>
</div>

<?php require_once 'template-footer.php'; ?>

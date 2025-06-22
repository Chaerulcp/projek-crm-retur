<?php
session_start();
// Izinkan akses untuk CS, Manajemen, dan Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_peran'], ['Customer Service', 'Manajemen', 'Admin'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Ambil semua data FAQ untuk ditampilkan di tabel, diurutkan berdasarkan kategori
$faq_list = $pdo->query("SELECT * FROM faq ORDER BY kategori, id_faq DESC")->fetchAll();

// Logika untuk mode edit: jika ada parameter ?edit= di URL, ambil data FAQ tersebut
$is_edit_mode = false;
$faq_to_edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM faq WHERE id_faq = ?");
    $stmt->execute([$_GET['edit']]);
    $faq_to_edit = $stmt->fetch();
    if ($faq_to_edit) {
        $is_edit_mode = true;
    }
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Manajemen Pusat Bantuan (FAQ)</h3>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold"><?php echo $is_edit_mode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form action="proses-faq.php" method="POST">
                        <input type="hidden" name="action" value="<?php echo $is_edit_mode ? 'edit' : 'tambah'; ?>">
                        <?php if ($is_edit_mode) { echo '<input type="hidden" name="id_faq" value="' . $faq_to_edit['id_faq'] . '">'; } ?>
                        
                        <div class="mb-3">
                            <label for="pertanyaan" class="form-label">Pertanyaan</label>
                            <input type="text" id="pertanyaan" name="pertanyaan" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($faq_to_edit['pertanyaan']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="jawaban" class="form-label">Jawaban</label>
                            <textarea id="jawaban" name="jawaban" class="form-control" rows="5" required><?php echo $is_edit_mode ? htmlspecialchars($faq_to_edit['jawaban']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" id="kategori" name="kategori" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($faq_to_edit['kategori']) : 'Umum'; ?>" placeholder="Contoh: Pengiriman, Pembayaran" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $is_edit_mode ? 'Simpan Perubahan' : 'Tambah FAQ'; ?></button>
                            <?php if ($is_edit_mode): ?>
                                <a href="faq-manajemen.php" class="btn btn-secondary mt-2">Batal Edit</a>
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
                                    <th>Pertanyaan</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($faq_list)): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data FAQ.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($faq_list as $faq): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($faq['pertanyaan']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($faq['kategori']); ?></span></td>
                                        <td class="text-center">
                                            <a href="faq-manajemen.php?edit=<?php echo $faq['id_faq']; ?>" class="btn btn-sm btn-info" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <form action="proses-faq.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="hapus">
                                                <input type="hidden" name="id_faq" value="<?php echo $faq['id_faq']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin?')"><i class="bi bi-trash-fill"></i></button>
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

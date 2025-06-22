<?php
session_start();
// Hanya Admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Ambil semua data pengguna
$user_list = $pdo->query("SELECT id_pengguna, nama_lengkap, email, peran FROM pengguna_sistem ORDER BY id_pengguna ASC")->fetchAll();

// Cek jika sedang mode edit untuk mengisi data di form
$is_edit_mode = false;
$user_to_edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT id_pengguna, nama_lengkap, email, peran FROM pengguna_sistem WHERE id_pengguna = ?");
    $stmt->execute([$_GET['edit']]);
    $user_to_edit = $stmt->fetch();
    if ($user_to_edit) {
        $is_edit_mode = true;
    }
}

// Fungsi untuk memberikan warna pada badge peran
function getRoleBadge($role) {
    $role_lower = strtolower($role);
    $badge_class = 'bg-secondary';
    switch ($role_lower) {
        case 'admin': $badge_class = 'bg-danger'; break;
        case 'manajemen': $badge_class = 'bg-success'; break;
        case 'customer service': $badge_class = 'bg-primary'; break;
        case 'gudang': $badge_class = 'bg-warning text-dark'; break;
    }
    return '<span class="badge ' . $badge_class . '">' . htmlspecialchars($role) . '</span>';
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3 class="fw-bold mb-4">Manajemen Pengguna Sistem</h3>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold"><?php echo $is_edit_mode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'; ?></h5>
                </div>
                <div class="card-body">
                    <form action="proses-user.php" method="POST">
                        <input type="hidden" name="action" value="<?php echo $is_edit_mode ? 'edit' : 'tambah'; ?>">
                        <?php if ($is_edit_mode) { echo '<input type="hidden" name="id_pengguna" value="' . $user_to_edit['id_pengguna'] . '">'; } ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($user_to_edit['nama_lengkap']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($user_to_edit['email']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" <?php echo !$is_edit_mode ? 'required' : ''; ?>>
                            <?php if ($is_edit_mode): ?>
                                <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peran</label>
                            <select name="peran" class="form-select" <?php echo ($is_edit_mode && $user_to_edit['id_pengguna'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                <option value="Customer Service" <?php echo ($is_edit_mode && $user_to_edit['peran'] == 'Customer Service') ? 'selected' : ''; ?>>Customer Service</option>
                                <option value="Gudang" <?php echo ($is_edit_mode && $user_to_edit['peran'] == 'Gudang') ? 'selected' : ''; ?>>Gudang</option>
                                <option value="Manajemen" <?php echo ($is_edit_mode && $user_to_edit['peran'] == 'Manajemen') ? 'selected' : ''; ?>>Manajemen</option>
                                <option value="Admin" <?php echo ($is_edit_mode && $user_to_edit['peran'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $is_edit_mode ? 'Simpan Perubahan' : 'Tambah Pengguna'; ?></button>
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
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th class="text-center">Peran</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_list as $user): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($user['nama_lengkap']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="text-center"><?php echo getRoleBadge($user['peran']); ?></td>
                                    <td class="text-center">
                                        <a href="user-manajemen.php?edit=<?php echo $user['id_pengguna']; ?>" class="btn btn-sm btn-info" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        <?php if ($user['id_pengguna'] != $_SESSION['user_id']): // Admin tidak bisa hapus diri sendiri ?>
                                        <form action="proses-user.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="hapus">
                                            <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Anda yakin?')"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'template-footer.php'; ?>

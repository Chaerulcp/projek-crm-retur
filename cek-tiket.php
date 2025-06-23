<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Tiket Retur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include 'nav.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="text-center fw-bold">Lacak Permintaan Retur Anda</h2>
                        <p class="text-center text-muted mb-4">Masukkan nomor tiket yang Anda terima saat pengajuan.</p>
                        <form action="status-tiket.php" method="GET">
                            <div class="mb-3">
                                <label for="nomor_tiket" class="form-label">Nomor Tiket</label>
                                <input type="text" class="form-control form-control-lg" id="nomor_tiket" name="nomor_tiket" placeholder="Contoh: RET-1718928192" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search"></i> Lacak Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>

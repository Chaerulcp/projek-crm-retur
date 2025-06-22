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
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-box-seam"></i> TokoKita Retur</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="form-retur.php">Ajukan Retur</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cek-tiket.php">Cek Status Tiket</a></li>
                    <li class="nav-item"><a class="nav-link" href="faq.php">Pusat Bantuan (FAQ)</a></li>
                </ul>
                <a href="login.php" class="btn btn-outline-primary"><i class="bi bi-person-circle"></i> Login Staf</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
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
</body>
</html>
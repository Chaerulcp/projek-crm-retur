<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Pusat Retur TokoKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .hero {
            background-color: #f8f9fa;
            padding: 4rem 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-box-seam"></i>
            TokoKita Retur
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="form-retur.php">Ajukan Retur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cek-tiket.php">Cek Status Tiket</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="faq.php">Pusat Bantuan (FAQ)</a>
                </li>
            </ul>
            <a href="login.php" class="btn btn-outline-primary">
                <i class="bi bi-person-circle"></i> Login Staf
            </a>
        </div>
    </div>
</nav>

    <header class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Pusat Pengembalian Barang yang Mudah</h1>
            <p class="lead text-muted mt-3">Layanan mandiri untuk memudahkan proses pengajuan retur dan pengembalian dana produk Anda secara transparan dan efisien.</p>
            <a href="form-retur.php" class="btn btn-primary btn-lg mt-4">
                Mulai Proses Retur Sekarang
            </a>
        </div>
    </header>

    <section class="container my-5">
        <h2 class="text-center mb-5">Bagaimana Cara Kerjanya?</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <i class="bi bi-ui-checks-grid feature-icon mb-3"></i>
                <h4 class="fw-bold">1. Isi Formulir</h4>
                <p class="text-muted">Lengkapi formulir pengajuan retur dengan detail pesanan dan alasan Anda. Unggah bukti jika diperlukan.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="bi bi-truck feature-icon mb-3"></i>
                <h4 class="fw-bold">2. Kirim Barang Anda</h4>
                <p class="text-muted">Setelah pengajuan disetujui, kirimkan barang sesuai instruksi ke alamat gudang kami.</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="bi bi-cash-stack feature-icon mb-3"></i>
                <h4 class="fw-bold">3. Terima Pengembalian Dana</h4>
                <p class="text-muted">Setelah barang kami periksa dan nyatakan layak, kami akan segera memproses pengembalian dana Anda.</p>
            </div>
        </div>
    </section>

    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <p>Sistem ini dirancang untuk memberikan transparansi penuh dalam setiap langkah proses pengembalian barang dan dana Anda.</p>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            &copy; <?php echo date('Y'); ?> TokoKita. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
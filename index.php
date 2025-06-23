<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pusat Retur TokoKita - Solusi Pengembalian Barang Mudah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <style>
        /* Reset and base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        a {
            text-decoration: none;
        }
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            color: white;
            padding: 8rem 1rem 6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero h1 {
            font-size: 3.75rem;
            font-weight: 900;
            margin-bottom: 1rem;
            letter-spacing: 1.5px;
            text-shadow: 0 3px 8px rgba(0,0,0,0.3);
        }
        .hero p {
            font-size: 1.5rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            opacity: 0.9;
            text-shadow: 0 2px 6px rgba(0,0,0,0.25);
        }
        .btn-primary {
            background-color: #5a2ea6;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.25rem;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 6px 15px rgba(90, 46, 166, 0.6);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #7a4bcf;
            box-shadow: 0 8px 20px rgba(122, 75, 207, 0.8);
        }
        /* Section Titles */
        h2.section-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #4b2a99;
            text-align: center;
            position: relative;
        }
        h2.section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #6f42c1;
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        /* Features & Benefits Cards */
        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(111, 66, 193, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 2rem;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .card-custom:hover {
            transform: translateY(-12px);
            box-shadow: 0 15px 35px rgba(111, 66, 193, 0.3);
        }
        .card-icon {
            font-size: 4.5rem;
            color: #6f42c1;
            margin-bottom: 1.25rem;
            transition: color 0.3s ease;
        }
        .card-custom:hover .card-icon {
            color: #8e44ad;
        }
        .card-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #3a1a75;
        }
        .card-text {
            color: #555;
            flex-grow: 1;
            font-size: 1.1rem;
        }
        /* Responsive */
        @media (max-width: 991.98px) {
            .hero h1 {
                font-size: 2.75rem;
            }
            .hero p {
                font-size: 1.25rem;
            }
            .btn-primary {
                width: 100%;
                padding: 1rem;
                font-size: 1.1rem;
            }
        }
        @media (max-width: 575.98px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<header class="hero">
    <div class="container">
        <h1>Pusat Retur TokoKita</h1>
        <p>Mudahkan proses pengajuan retur dan pengembalian dana produk Anda dengan layanan kami yang cepat, aman, dan terpercaya.</p>
        <a href="form-retur.php" class="btn btn-primary">Mulai Proses Retur Sekarang</a>
    </div>
</header>

<section class="container my-5">
    <h2 class="section-title">Keunggulan Layanan Kami</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-speedometer2 card-icon"></i>
                <h3 class="card-title">Proses Cepat</h3>
                <p class="card-text">Pengajuan retur dan pengembalian dana diproses dengan cepat dan efisien tanpa ribet.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-shield-lock card-icon"></i>
                <h3 class="card-title">Keamanan Terjamin</h3>
                <p class="card-text">Data dan barang Anda dijaga dengan standar keamanan tinggi untuk kenyamanan Anda.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-people card-icon"></i>
                <h3 class="card-title">Dukungan Pelanggan</h3>
                <p class="card-text">Tim kami siap membantu Anda kapan saja dengan layanan pelanggan yang ramah dan responsif.</p>
            </div>
        </div>
    </div>
</section>

<section class="container my-5">
    <h2 class="section-title">Bagaimana Cara Kerjanya?</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-ui-checks-grid card-icon"></i>
                <h3 class="card-title">Isi Formulir</h3>
                <p class="card-text">Lengkapi formulir pengajuan retur dengan detail pesanan dan alasan Anda. Unggah bukti jika diperlukan.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-truck card-icon"></i>
                <h3 class="card-title">Kirim Barang Anda</h3>
                <p class="card-text">Setelah pengajuan disetujui, kirimkan barang sesuai instruksi ke alamat gudang kami.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom">
                <i class="bi bi-cash-stack card-icon"></i>
                <h3 class="card-title">Terima Pengembalian Dana</h3>
                <p class="card-text">Setelah barang kami periksa dan nyatakan layak, kami akan segera memproses pengembalian dana Anda.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

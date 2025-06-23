<?php
// Langkah 1: Hubungkan ke database untuk mengambil daftar produk
require_once 'config.php';

// Langkah 2: Query untuk mengambil semua produk dari database
// Kita urutkan berdasarkan nama agar mudah dicari oleh pelanggan
$produk_list = $pdo->query("SELECT id_produk, nama_produk FROM produk ORDER BY nama_produk ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Retur Barang</title>
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
<?php include 'nav.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Formulir Pengajuan Retur & Refund</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Silakan isi formulir di bawah ini untuk memulai proses pengembalian barang.</p>
                        
                        <form action="proses-retur.php" method="POST" enctype="multipart/form-data">
                            
                            <h5 class="mt-4">Informasi Pelanggan</h5>
                            <hr>
                            <div class="mb-3">
                                <label for="nama_pelanggan" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                            </div>
                            <div class="mb-3">
                                <label for="email_pelanggan" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email_pelanggan" name="email_pelanggan" required>
                            </div>

                            <h5 class="mt-4">Detail Pesanan & Produk</h5>
                            <hr>
                            <div class="mb-3">
                                <label for="nomor_invoice" class="form-label">Nomor Invoice / Pesanan</label>
                                <input type="text" class="form-control" id="nomor_invoice" name="nomor_invoice" required>
                            </div>
                             <div class="mb-3">
                                <label for="id_produk" class="form-label">Produk yang Ingin Diretur</label>
                                <select class="form-select" id="id_produk" name="id_produk" required>
                                    <option value="" disabled selected>-- Pilih Produk --</option>
                                    
                                    <?php // Langkah 3: Loop untuk menampilkan setiap produk sebagai sebuah option ?>
                                    <?php foreach ($produk_list as $produk): ?>
                                        <option value="<?php echo $produk['id_produk']; ?>">
                                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="alasan_retur" class="form-label">Alasan Retur</label>
                                <textarea class="form-control" id="alasan_retur" name="alasan_retur" rows="4" required placeholder="Contoh: Ukuran tidak sesuai, barang rusak saat diterima, dll."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="bukti_foto" class="form-label">Unggah Bukti (Foto/Video Produk)</label>
                                <input class="form-control" type="file" id="bukti_foto" name="bukti_foto" accept="image/*,video/*" required>
                                <div class="form-text">File maksimal 5MB. Format yang didukung: JPG, PNG, MP4.</div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Kirim Pengajuan</button>
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

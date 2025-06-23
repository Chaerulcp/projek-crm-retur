<?php
session_start();
require_once 'config.php';
$produk_list = $pdo->query("SELECT id_produk, nama_produk FROM produk ORDER BY nama_produk ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulir Pengajuan Retur Barang - TokoKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }
        .form-header {
            border-bottom: 2px solid #0d6efd;
            margin-bottom: 25px;
            padding-bottom: 10px;
        }
        .form-header h2 {
            color: #0d6efd;
            font-weight: 700;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .info-text {
            background-color: #e7f1ff;
            border-left: 5px solid #0d6efd;
            padding: 12px 15px;
            margin-bottom: 20px;
            color: #0d6efd;
            font-weight: 600;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-text i {
            font-size: 1.3rem;
        }
        textarea.form-control {
            resize: vertical;
        }
        .file-list {
            margin-top: 10px;
            list-style: none;
            padding-left: 0;
        }
        .file-list li {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin-bottom: 6px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-list li button {
            background: transparent;
            border: none;
            color: #dc3545;
            font-size: 1.2rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="form-container">
    <div class="form-header">
        <h2><i class="bi bi-arrow-repeat"></i> Formulir Pengajuan Retur & Refund</h2>
    </div>
    <div class="info-text">
        <i class="bi bi-info-circle"></i> Pastikan Anda menggunakan alamat email yang aktif dan dapat diakses, karena notifikasi terkait pengajuan retur akan dikirimkan ke email tersebut.
    </div>
    <?php
    // Start session at the very beginning of the file, before any output
    // So move this to the top of the file instead of here
    ?>
    <form action="proses-retur.php" method="POST" enctype="multipart/form-data" novalidate>
        <fieldset class="mb-4">
            <legend class="h5 mb-3">Informasi Pelanggan</legend>
            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukkan nama lengkap Anda" required />
            </div>
            <div class="mb-3">
                <label for="email_pelanggan" class="form-label">Alamat Email</label>
                <input type="email" class="form-control" id="email_pelanggan" name="email_pelanggan" placeholder="contoh@email.com" required />
            </div>
        </fieldset>

        

        <fieldset class="mb-4">
            <legend class="h5 mb-3">Detail Pesanan & Produk</legend>
            <div class="mb-3">
                <label for="nomor_invoice" class="form-label">Nomor Invoice / Pesanan</label>
                <input type="text" class="form-control" id="nomor_invoice" name="nomor_invoice" placeholder="Masukkan nomor invoice atau pesanan" required />
            </div>
            <div class="mb-3">
                <label for="id_produk" class="form-label">Produk yang Ingin Diretur</label>
                <select class="form-select" id="id_produk" name="id_produk" required>
                    <option value="" disabled selected>-- Pilih Produk --</option>
                    <?php foreach ($produk_list as $produk): ?>
                        <option value="<?php echo $produk['id_produk']; ?>">
                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="alasan_retur" class="form-label">Alasan Retur</label>
                <textarea class="form-control" id="alasan_retur" name="alasan_retur" rows="4" placeholder="Contoh: Ukuran tidak sesuai, barang rusak saat diterima, dll." required></textarea>
            </div>
            <div class="mb-3">
                <label for="bukti_foto" class="form-label">Unggah Bukti (Foto/Video Produk)</label>
                <input class="form-control" type="file" id="bukti_foto" name="bukti_foto[]" accept="image/*,video/*" multiple required />
                <div class="form-text">
                    <ul class="mb-0" style="list-style-type: disc; padding-left: 20px;">
                        <li>Maksimal 3 file yang dapat diupload sekaligus</li>
                        <li>Foto maksimal 5MB per file</li>
                        <li>Video maksimal 50MB per file</li>
                        <li>Format yang didukung: JPG, PNG, MP4</li>
                    </ul>
                </div>
                <ul id="fileList" class="file-list"></ul>
            </div>
        </fieldset>

        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>
        <!-- Google reCAPTCHA widget -->
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="6Ld8-2orAAAAABF-ftSKn3-XmTbGIHvAD0cG-1fg"></div>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg">Kirim Pengajuan</button>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
    const fileInput = document.getElementById('bukti_foto');
    const fileList = document.getElementById('fileList');
    let filesArray = [];

    function updateFileList() {
        fileList.innerHTML = '';
        filesArray.forEach((file, index) => {
            const li = document.createElement('li');
            li.textContent = file.name;
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Hapus file ini';
            removeBtn.onclick = function() {
                removeFile(index);
            };
            li.appendChild(removeBtn);
            fileList.appendChild(li);
        });
    }

    function removeFile(index) {
        filesArray.splice(index, 1);
        updateFileList();
        updateFileInput();
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    fileInput.addEventListener('change', (event) => {
        const newFiles = Array.from(event.target.files);
        if ((filesArray.length + newFiles.length) > 3) {
            alert('Maksimal 3 file yang dapat diupload.');
            return;
        }
        filesArray = filesArray.concat(newFiles);
        updateFileList();
        updateFileInput();
    });
</script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>

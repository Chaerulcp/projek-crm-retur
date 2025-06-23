<?php
// Mulai session untuk menyimpan data sementara seperti nomor tiket
session_start();

// Panggil file koneksi database
require_once 'config.php';

// Pastikan skrip hanya berjalan jika ada request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Google reCAPTCHA
    $recaptcha_secret = 'YOUR_SECRET_KEY_HERE';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verify_url . '?secret=' . urlencode($recaptcha_secret) . '&response=' . urlencode($recaptcha_response));
    $response_data = json_decode($response);

    if (!$response_data || !$response_data->success) {
        $_SESSION['error_message'] = "Verifikasi reCAPTCHA gagal. Harap coba lagi.";
        header("Location: form-retur.php");
        exit();
    }

    // --- Ambil Data dari Form ---  
    $nama_pelanggan = trim($_POST['nama_pelanggan']);
    $email_pelanggan = trim($_POST['email_pelanggan']);
    $nomor_invoice = trim($_POST['nomor_invoice']);
    $id_produk = $_POST['id_produk'];
    $alasan_retur = trim($_POST['alasan_retur']);
    
    // --- Proses Upload File ---  
    $nama_file_bukti = [];
    if (isset($_FILES['bukti_foto']) && !empty($_FILES['bukti_foto']['name'][0])) {
        $max_files = 3;
        $file_count = count($_FILES['bukti_foto']['name']);
        if ($file_count > $max_files) {
            $_SESSION['error_message'] = "Maksimal 3 file yang dapat diupload sekaligus.";
            header("Location: form-retur.php");
            exit();
        }

        $max_photo_size = 5 * 1024 * 1024; // 5MB
        $max_video_size = 50 * 1024 * 1024; // 50MB
        $target_dir = "uploads/";

        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $_FILES['bukti_foto']['name'][$i];
            $file_tmp = $_FILES['bukti_foto']['tmp_name'][$i];
            $file_size = $_FILES['bukti_foto']['size'][$i];
            $file_type = $_FILES['bukti_foto']['type'][$i];
            $file_error = $_FILES['bukti_foto']['error'][$i];

            if ($file_error !== 0) {
                $_SESSION['error_message'] = "Terjadi error saat mengupload file: $file_name";
                header("Location: form-retur.php");
                exit();
            }

            // Cek tipe file apakah foto atau video
            if (strpos($file_type, 'image/') === 0) {
                if ($file_size > $max_photo_size) {
                    $_SESSION['error_message'] = "Ukuran file foto maksimal 5MB: $file_name";
                    header("Location: form-retur.php");
                    exit();
                }
            } elseif (strpos($file_type, 'video/') === 0) {
                if ($file_size > $max_video_size) {
                    $_SESSION['error_message'] = "Ukuran file video maksimal 50MB: $file_name";
                    header("Location: form-retur.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Format file tidak didukung. Harap unggah foto atau video: $file_name";
                header("Location: form-retur.php");
                exit();
            }

            // Buat nama file unik untuk menghindari tumpang tindih
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = "bukti-" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $unique_name;

            // Pindahkan file yang diupload ke folder 'uploads'
            if (!move_uploaded_file($file_tmp, $target_file)) {
                $_SESSION['error_message'] = "Maaf, terjadi error saat mengupload file Anda: $file_name";
                header("Location: form-retur.php");
                exit();
            }

            $nama_file_bukti[] = $unique_name;
        }
        // Simpan sebagai JSON array string
        $nama_file_bukti = json_encode($nama_file_bukti);
    } else {
        $_SESSION['error_message'] = "Upload file bukti wajib dilakukan.";
        header("Location: form-retur.php");
        exit();
    }

    // --- Proses Database ---
    try {
        $pdo->beginTransaction();

        // 1. Cek atau masukkan data pelanggan
        $stmt = $pdo->prepare("SELECT id_pelanggan FROM pelanggan WHERE email_pelanggan = ?");
        $stmt->execute([$email_pelanggan]);
        $pelanggan = $stmt->fetch();

        $id_pelanggan = null;
        if ($pelanggan) {
            $id_pelanggan = $pelanggan['id_pelanggan'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO pelanggan (nama_pelanggan, email_pelanggan) VALUES (?, ?)");
            $stmt->execute([$nama_pelanggan, $email_pelanggan]);
            $id_pelanggan = $pdo->lastInsertId();
        }

        // 2. Buat nomor tiket unik
        $nomor_tiket = 'RET-' . time();

        // 3. Simpan data retur ke tabel tiket_retur
        $sql = "INSERT INTO tiket_retur (nomor_tiket, id_pelanggan, id_produk, nomor_invoice, alasan_retur, bukti_foto, status_tiket) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nomor_tiket,
            $id_pelanggan,
            $id_produk,
            $nomor_invoice,
            $alasan_retur,
            $nama_file_bukti,
            'Diajukan' // Status awal saat tiket dibuat
        ]);

        $pdo->commit();

        // --- KIRIM NOTIFIKASI EMAIL ---
        require_once 'fungsi-email.php';
        $subjek = "Pengajuan Retur Diterima (Tiket #$nomor_tiket)";
        $isi_email = '
        <html>
        <head>
          <style>
            body {
              font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
              background-color: #f9f9f9;
              margin: 0;
              padding: 0;
              color: #333333;
            }
            .container {
              background-color: #ffffff;
              margin: 30px auto;
              padding: 30px;
              max-width: 600px;
              border-radius: 10px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.1);
              border: 1px solid #e0e0e0;
            }
            h2 {
              color: #2c3e50;
              font-weight: 700;
              margin-bottom: 20px;
            }
            p {
              font-size: 16px;
              line-height: 1.6;
              margin-bottom: 20px;
            }
            .button {
              display: inline-block;
              padding: 12px 25px;
              font-size: 16px;
              color: #ffffff;
              background-color: #3498db;
              border-radius: 5px;
              text-decoration: none;
              font-weight: 600;
              box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
              transition: background-color 0.3s ease;
            }
            .button:hover {
              background-color: #2980b9;
            }
            .footer {
              font-size: 12px;
              color: #999999;
              margin-top: 30px;
              text-align: center;
              border-top: 1px solid #e0e0e0;
              padding-top: 15px;
            }
          </style>
        </head>
        <body>
          <div class="container">
            <h2>Pengajuan Retur Anda Telah Diterima</h2>
            <p>Halo ' . htmlspecialchars($nama_pelanggan) . ',</p>
            <p>Kami ingin memberitahukan bahwa pengajuan retur Anda dengan nomor tiket <strong>' . $nomor_tiket . '</strong> telah berhasil kami terima dan sedang dalam proses verifikasi.</p>
            <p>Anda dapat memantau status pengajuan retur Anda secara langsung dengan mengklik tombol di bawah ini:</p>
            <p><a href="http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/status-tiket.php?nomor_tiket=' . urlencode($nomor_tiket) . '" class="button" target="_blank" rel="noopener">Cek Status Retur</a></p>
            <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.</p>
            <p>Hormat kami,<br>Tim TokoKita</p>
            <div class="footer">Jika Anda tidak melakukan pengajuan ini, harap abaikan email ini.</div>
          </div>
        </body>
        </html>
        ';
        kirim_email_notifikasi($email_pelanggan, $nama_pelanggan, $subjek, $isi_email);
        // --- AKHIR DARI KODE NOTIFIKASI ---

        // Simpan nomor tiket ke session untuk ditampilkan di halaman sukses
        $_SESSION['nomor_tiket_baru'] = $nomor_tiket;

        // Arahkan ke halaman sukses
        header("Location: sukses.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }

} else {
    // Jika diakses langsung, arahkan kembali ke form
    header("Location: form-retur.php");
    exit();
}
?>
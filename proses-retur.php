<?php
// Mulai session untuk menyimpan data sementara seperti nomor tiket
session_start();

// Panggil file koneksi database
require_once 'config.php';

// Pastikan skrip hanya berjalan jika ada request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Ambil Data dari Form ---
    $nama_pelanggan = trim($_POST['nama_pelanggan']);
    $email_pelanggan = trim($_POST['email_pelanggan']);
    $nomor_invoice = trim($_POST['nomor_invoice']);
    $id_produk = $_POST['id_produk'];
    $alasan_retur = trim($_POST['alasan_retur']);
    
    // --- Proses Upload File ---
    $nama_file_bukti = null;
    if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] == 0) {
        $target_dir = "uploads/";
        // Buat nama file unik untuk menghindari tumpang tindih
        $file_extension = pathinfo($_FILES["bukti_foto"]["name"], PATHINFO_EXTENSION);
        $nama_file_bukti = "bukti-" . uniqid() . "." . $file_extension;
        $target_file = $target_dir . $nama_file_bukti;

        // Pindahkan file yang diupload ke folder 'uploads'
        if (!move_uploaded_file($_FILES["bukti_foto"]["tmp_name"], $target_file)) {
            die("Maaf, terjadi error saat mengupload file Anda.");
        }
    } else {
        die("Upload file bukti wajib dilakukan.");
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
        $isi_email = "
            Halo " . htmlspecialchars($nama_pelanggan) . ",<br><br>
            Kami telah menerima pengajuan retur Anda dengan nomor tiket <strong>$nomor_tiket</strong>.<br>
            Tim kami akan segera melakukan verifikasi dan akan memberi Anda pembaruan selanjutnya.<br><br>
            Terima kasih,<br>
            Tim TokoKita
        ";
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
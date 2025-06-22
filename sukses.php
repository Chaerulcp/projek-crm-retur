<?php
session_start();

// Ambil nomor tiket dari session
if (!isset($_SESSION['nomor_tiket_baru'])) {
    // Jika tidak ada nomor tiket, mungkin pengguna akses langsung. Arahkan ke form.
    header("Location: form-retur.php");
    exit();
}

$nomor_tiket = $_SESSION['nomor_tiket_baru'];

// Hapus session agar tidak tampil lagi jika halaman di-refresh
unset($_SESSION['nomor_tiket_baru']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { text-align: center; padding: 40px 0; background: #f8f9fa; }
        .card { max-width: 500px; margin: auto; border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-title { color: #28a745; }
        .ticket-number { font-size: 1.5rem; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="card p-4">
        <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 100px; color: #28a745;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
            </svg>
        </div>
        <div class="card-body">
            <h1 class="card-title">Pengajuan Berhasil!</h1>
            <p class="card-text">Terima kasih. Permintaan retur Anda telah kami terima.</p>
            <p>Nomor tiket Anda adalah:</p>
            <div class="alert alert-info">
                <span class="ticket-number"><?php echo htmlspecialchars($nomor_tiket); ?></span>
            </div>
            <p>Mohon simpan nomor tiket ini untuk melacak status permintaan Anda. Tim kami akan segera melakukan verifikasi.</p>
            <a href="form-retur.php" class="btn btn-primary mt-3">Ajukan Retur Lain</a>
        </div>
    </div>
</body>
</html>
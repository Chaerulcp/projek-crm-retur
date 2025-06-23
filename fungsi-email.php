<?php
// Menggunakan class-class dari PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Memanggil file autoload.php yang dibuat oleh Composer
require 'vendor/autoload.php';

/*
 * Fungsi Terpusat untuk Mengirim Email
 * @param string $penerima_email - Alamat email tujuan
 * @param string $penerima_nama - Nama penerima
 * @param string $subjek - Judul email
 * @param string $isi_email - Konten email dalam format HTML
 * @return bool - True jika berhasil, false jika gagal
 */
function kirim_email_notifikasi($penerima_email, $penerima_nama, $subjek, $isi_email) {
    // Buat instance baru dari PHPMailer
    $mail = new PHPMailer(true);

    try {
        // --- PENGATURAN SERVER SMTP (Gunakan detail Anda sendiri) ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;       // Aktifkan untuk melihat proses debug
        $mail->isSMTP();                                // Menggunakan protokol SMTP
        $mail->Host       = '';           // Server SMTP Mailtrap
        $mail->SMTPAuth   = true;                       // Aktifkan otentikasi SMTP
        $mail->Username   = ''; // Alamat email Mailtrap Anda
        $mail->Password   = '';         // Gunakan 'App Password' 16 digit Anda
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Gunakan enkripsi STARTTLS
        $mail->Port       = 2525;                        // Port untuk Mailtrap SMTP

        // --- PENGATURAN PENGIRIM & PENERIMA ---
        $mail->setFrom('no-reply@tokokita.com', 'Tim Dukungan TokoKita'); // Email dan nama pengirim
        $mail->addAddress($penerima_email, $penerima_nama);    // Email dan nama penerima

        // --- KONTEN EMAIL ---
        $mail->isHTML(true);                             // Mengatur format email ke HTML
        $mail->Subject = $subjek;
        $mail->Body    = $isi_email;
        $mail->AltBody = strip_tags($isi_email); // Versi teks biasa untuk email client non-HTML

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Jika gagal, catat error ke file log
        error_log("Pesan gagal terkirim. Mailer Error: {$mail->ErrorInfo}\n", 3, __DIR__ . '/email-error.log');
        return false;
    }
}
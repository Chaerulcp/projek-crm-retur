<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Manajemen') {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// --- DATA 1: JUMLAH RETUR PER HARI (30 HARI TERAKHIR) ---
$stmt_retur_per_hari = $pdo->query("
    SELECT DATE(dibuat_pada) as tanggal, COUNT(*) as jumlah
    FROM tiket_retur
    WHERE dibuat_pada >= CURDATE() - INTERVAL 30 DAY
    GROUP BY DATE(dibuat_pada)
    ORDER BY tanggal ASC
");
$retur_per_hari = $stmt_retur_per_hari->fetchAll();
$labels_harian = [];
$data_harian = [];
foreach ($retur_per_hari as $row) {
    $labels_harian[] = date('d M', strtotime($row['tanggal']));
    $data_harian[] = $row['jumlah'];
}

// --- DATA 2: PRODUK PALING SERING DIRETUR (TOP 5) ---
$stmt_top_produk = $pdo->query("
    SELECT pr.nama_produk, COUNT(t.id_tiket) as jumlah
    FROM tiket_retur t
    JOIN produk pr ON t.id_produk = pr.id_produk
    GROUP BY pr.nama_produk
    ORDER BY jumlah DESC
    LIMIT 5
");
$top_produk = $stmt_top_produk->fetchAll();
$labels_produk = [];
$data_produk = [];
foreach ($top_produk as $row) {
    $labels_produk[] = $row['nama_produk'];
    $data_produk[] = $row['jumlah'];
}

// --- DATA 3: ALASAN RETUR TERBANYAK (TOP 5) ---
// Catatan: Di sistem nyata, lebih baik jika alasan retur adalah kategori, bukan teks bebas.
$stmt_top_alasan = $pdo->query("
    SELECT alasan_retur, COUNT(*) as jumlah
    FROM tiket_retur
    GROUP BY alasan_retur
    ORDER BY jumlah DESC
    LIMIT 5
");
$top_alasan = $stmt_top_alasan->fetchAll();
$labels_alasan = [];
$data_alasan = [];
foreach ($top_alasan as $row) {
    $labels_alasan[] = $row['alasan_retur'];
    $data_alasan[] = $row['jumlah'];
}

require_once 'template-header.php';
?>

<div class="content-wrapper">
    <h3>Dashboard Analitik Retur</h3>
    <p class="text-muted">Visualisasi data untuk membantu pengambilan keputusan.</p>
    
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">Tren Retur Harian (30 Hari Terakhir)</div>
                <div class="card-body">
                    <canvas id="grafikReturHarian"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">Top 5 Produk Paling Sering Diretur</div>
                <div class="card-body">
                     <canvas id="grafikTopProduk"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
             <div class="card">
                <div class="card-header">Top 5 Alasan Retur</div>
                <div class="card-body">
                     <canvas id="grafikTopAlasan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data dari PHP
    const labelsHarian = <?php echo json_encode($labels_harian); ?>;
    const dataHarian = <?php echo json_encode($data_harian); ?>;
    const labelsProduk = <?php echo json_encode($labels_produk); ?>;
    const dataProduk = <?php echo json_encode($data_produk); ?>;
    const labelsAlasan = <?php echo json_encode($labels_alasan); ?>;
    const dataAlasan = <?php echo json_encode($data_alasan); ?>;

    // 1. Grafik Tren Harian
    const ctx1 = document.getElementById('grafikReturHarian').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: labelsHarian,
            datasets: [{
                label: 'Jumlah Retur',
                data: dataHarian,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        }
    });

    // 2. Grafik Top Produk
    const ctx2 = document.getElementById('grafikTopProduk').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labelsProduk,
            datasets: [{
                label: 'Jumlah Retur',
                data: dataProduk,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: { indexAxis: 'y' } // Membuat bar menjadi horizontal
    });

    // 3. Grafik Top Alasan
    const ctx3 = document.getElementById('grafikTopAlasan').getContext('2d');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: labelsAlasan,
            datasets: [{
                label: 'Jumlah',
                data: dataAlasan,
                backgroundColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(54, 162, 235, 0.5)'
                ]
            }]
        }
    });
});
</script>

<?php require_once 'template-footer.php'; ?>

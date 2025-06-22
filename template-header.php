<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dasbor CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="assets/css/custom-style.css" />
</head>
<body>
    <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>
    <div class="sidebar p-3 d-flex flex-column" id="sidebar">
        <h4 class="text-center mb-4"><i class="bi bi-box-seam-fill"></i> CRM RETUR</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i>Dasbor Tiket
                </a>
            </li>
            <?php if (in_array($_SESSION['user_peran'], ['Gudang', 'Admin'])): ?>
            <li>
                <a href="gudang-dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gudang-dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-box-seam"></i>Dasbor Gudang
                </a>
            </li>
            <?php endif; ?>
            <?php if (in_array($_SESSION['user_peran'], ['Manajemen', 'Admin'])): ?>
            <li>
                <a href="keuangan-dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'keuangan-dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-wallet2"></i>Dasbor Keuangan
                </a>
            </li>
            <li>
                <a href="analitik-dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analitik-dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-bar-chart-line"></i>Analitik
                </a>
            </li>
            <?php endif; ?>
            <?php if (in_array($_SESSION['user_peran'], ['Customer Service', 'Manajemen', 'Admin'])): ?>
            <li>
                <a href="faq-manajemen.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'faq-manajemen.php' ? 'active' : ''; ?>">
                    <i class="bi bi-question-circle"></i>Manajemen FAQ
                </a>
            </li>
            <?php endif; ?>
            <?php if ($_SESSION['user_peran'] == 'Admin'): ?>
            <hr class="text-secondary" />
            <h6 class="text-muted ps-3">Manajemen Sistem</h6>
            <li>
                <a href="produk-manajemen.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'produk-manajemen.php' ? 'active' : ''; ?>">
                    <i class="bi bi-tags"></i>Manajemen Produk
                </a>
            </li>
            <li>
                <a href="user-manajemen.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user-manajemen.php' ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>Manajemen Pengguna
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <hr />
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <strong><?php echo htmlspecialchars($_SESSION['user_nama']); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <div class="main-content" id="mainContent">
        <nav class="navbar top-navbar">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Dasbor [<?php echo htmlspecialchars($_SESSION['user_peran']); ?>]</span>
            </div>
        </nav>
        <div class="content-wrapper"></div>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('show');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            const footer = document.querySelector('footer');
            if (footer) {
                footer.classList.toggle('collapsed');
            }
        });
    </script>
</body>
</html>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">[<?php echo htmlspecialchars($_SESSION['user_peran']); ?>] CRM</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-staf">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav-staf">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard Tiket</a>
                </li>
                
                <?php // Tampilkan menu khusus jika peran adalah Admin ?>
                <?php if ($_SESSION['user_peran'] == 'Admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                        Manajemen Sistem
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="produk-manajemen.php">Manajemen Produk</a></li>
                        <li><a class="dropdown-item" href="user-manajemen.php">Manajemen Pengguna</a></li>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>
            <span class="navbar-text me-3">
                Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?>!
            </span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
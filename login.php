<?php 
session_start(); 
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login Staf - CRM Retur TokoKita</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS sama seperti versi geometris sebelumnya, tidak ada perubahan */
        body { background-color: #eef1f4; }
        .login-wrapper { display: flex; min-height: 100vh; }
        .login-branding-panel {
            background-color: #1a253c; color: white;
            padding: 4rem; position: relative;
            clip-path: polygon(0 0, 100% 0, 80% 100%, 0% 100%);
        }
        .login-branding-panel::before {
            content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            opacity: 0.03;
        }
        .login-form-panel {
            display: flex; align-items: center; justify-content: center; padding: 2rem;
        }
        .form-container { width: 100%; max-width: 380px; }
        .password-wrapper { position: relative; }
        #togglePassword {
            position: absolute; top: 50%; right: 15px;
            transform: translateY(-50%); cursor: pointer; color: #6c757d;
        }
        @media (max-width: 991.98px) {
            .login-branding-panel { clip-path: none; border-radius: 1rem 1rem 0 0; text-align: center; padding: 3rem 2rem; }
            .login-wrapper { padding: 1rem; }
            .login-form-panel { border-radius: 0 0 1rem 1rem; background-color: #fff; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <div class="col-lg-10 mx-auto">
                    <div class="card border-0 shadow-lg">
                        <div class="row g-0">
                            <div class="col-lg-6 login-branding-panel">
                                <div class="p-lg-4">
                                    <div class="mb-4">
                                        <i class="bi bi-shield-lock-fill" style="font-size: 3rem;"></i>
                                    </div>
                                    <h2 class="fw-bolder display-6">Akses Internal Staf</h2>
                                    <hr class="my-4 border-light w-25">
                                    <p class="lead" style="opacity: 0.9;">Portal ini dikhususkan untuk tim operasional TokoKita.</p>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 login-form-panel">
                                <div class="form-container">
                                    <h2 class="fw-bold mb-2">Portal Khusus Staf</h2>
                                    <p class="text-muted mb-4">Silakan login untuk melanjutkan ke dasbor Anda.</p>
                                    
                                    <?php
                                    if (isset($_SESSION['login_error'])) {
                                        echo '<div class="alert alert-danger p-2">' . $_SESSION['login_error'] . '</div>';
                                        unset($_SESSION['login_error']);
                                    }
                                    ?>
                                    
                                    <form action="proses-login.php" method="POST">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Staf</label> <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="mb-4">
                                             <label for="password" class="form-label">Password</label>
                                             <div class="password-wrapper">
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <i class="bi bi-eye-slash" id="togglePassword"></i>
                                            </div>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary fw-bold">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="text-center mt-4">
                        <a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Halaman Utama</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // JavaScript tidak ada perubahan
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            if (togglePassword) {
                togglePassword.addEventListener('click', function () {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }
        });
    </script>
</body>
</html>
<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
$flashError = AuthHelper::getFlash('error');
$flashSuccess = AuthHelper::getFlash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem MEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #FBFFFF 0%, #f0f7e6 100%);
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            width: 100%;
            max-width: 440px;
            padding: 40px 32px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-badge {
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: #1e330c;
            font-size: 1.5rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(181, 206, 136, 0.45);
        }
        .quick-role-badge {
            cursor: pointer;
            padding: 6px 10px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 0.75rem;
            color: #475569;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .quick-role-badge:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: #1e330c;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-badge">MEL</div>
                <h1 style="font-size: 1.4rem; font-weight: 700; color: #0f172a;">Sistem MEL</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Monitoring, Evaluasi, dan Pembelajaran Terpadu</p>
            </div>

            <?php if ($flashError): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($flashError) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashSuccess): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= htmlspecialchars($flashSuccess) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST">
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@ynki.org" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" style="padding-right: 42px;" required>
                        <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 0.95rem; display: flex; align-items: center; justify-content: center; padding: 4px;" title="Tampilkan / Sembunyikan Kata Sandi">
                            <i id="passwordToggleIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
                </button>
            </form>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 8px; font-weight: 600;">AKUN CONTOH (Klik untuk mengisi):</div>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <span class="quick-role-badge" onclick="fillLogin('lapangan@ynki.org', 'password123')">Staf Lapangan (Budi)</span>
                    <span class="quick-role-badge" onclick="fillLogin('mel@ynki.org', 'password123')">Staf MEL (Siti)</span>
                    <span class="quick-role-badge" onclick="fillLogin('leader@ynki.org', 'password123')">Project Leader (Rahmat)</span>
                    <span class="quick-role-badge" onclick="fillLogin('direktur@ynki.org', 'password123')">Direktur (Hendra)</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="<?= BASE_URL ?>/public-dashboard" style="font-size: 0.85rem; color: #2563eb; font-weight: 500;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Publik
                </a>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

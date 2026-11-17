<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function showLogin(): void {
        AuthHelper::initSession();
        if (AuthHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Cek apakah sedang dalam masa blokir sementara
        $isBlocked = false;
        $remainingSeconds = 0;
        if (isset($_SESSION['login_blocked_until'])) {
            $remainingSeconds = $_SESSION['login_blocked_until'] - time();
            if ($remainingSeconds > 0) {
                $isBlocked = true;
            } else {
                unset($_SESSION['login_blocked_until']);
                $_SESSION['login_attempts'] = 0;
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin(): void {
        AuthHelper::initSession();

        // 1. Cek proteksi blokir login
        if (isset($_SESSION['login_blocked_until'])) {
            $remaining = $_SESSION['login_blocked_until'] - time();
            if ($remaining > 0) {
                $m = ceil($remaining / 60);
                AuthHelper::setFlash('error', "Akses login sedang diblokir sementara. Silakan tunggu {$remaining} detik ({$m} menit) lagi.");
                header('Location: ' . BASE_URL . '/login');
                exit;
            } else {
                unset($_SESSION['login_blocked_until']);
                $_SESSION['login_attempts'] = 0;
            }
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            AuthHelper::setFlash('error', 'Silakan masukkan email dan kata sandi.');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if ($user) {
            // Reset counter percobaan gagal jika login berhasil
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_blocked_until']);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['organization'] = $user['organization'];

            AuthHelper::setFlash('success', 'Selamat datang kembali, ' . $user['name'] . '!');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } else {
            // Catat kegagalan login (maksimal 3 kali, blokir 3 menit)
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $maxAttempts = 3;
            $lockoutDuration = 3 * 60; // 3 menit (180 detik)

            if ($_SESSION['login_attempts'] >= $maxAttempts) {
                $_SESSION['login_blocked_until'] = time() + $lockoutDuration;
                $_SESSION['login_attempts'] = 0;
                AuthHelper::setFlash('error', 'Anda telah 3 kali salah memasukkan email atau kata sandi. Akses login diblokir selama 3 menit.');
            } else {
                $sisa = $maxAttempts - $_SESSION['login_attempts'];
                AuthHelper::setFlash('error', "Kombinasi email atau kata sandi tidak valid. Sisa percobaan: {$sisa} kali sebelum diblokir.");
            }

            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function logout(): void {
        AuthHelper::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

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
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin(): void {
        AuthHelper::initSession();
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
            AuthHelper::setFlash('error', 'Kombinasi email atau kata sandi tidak valid.');
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

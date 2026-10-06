<?php
// Session and Auth Helper
require_once __DIR__ . '/../config/config.php';

class AuthHelper {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool {
        self::initSession();
        return isset($_SESSION['user_id']);
    }

    public static function getUser(): ?array {
        self::initSession();
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'user_id' => $_SESSION['user_id'],
            'role_id' => $_SESSION['role_id'],
            'role_name' => $_SESSION['role_name'] ?? '',
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'organization' => $_SESSION['organization'] ?? ''
        ];
    }

    public static function requireLogin(): void {
        self::initSession();
        if (!self::isLoggedIn()) {
            $_SESSION['flash_error'] = 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireRole(array|int|string $allowedRoles): void {
        self::requireLogin();
        $userRole = $_SESSION['role_name'] ?? '';
        $userRoleId = $_SESSION['role_id'] ?? 0;

        if (is_array($allowedRoles)) {
            $matched = false;
            foreach ($allowedRoles as $role) {
                if (is_numeric($role) && $userRoleId == $role) $matched = true;
                if (is_string($role) && strcasecmp($userRole, $role) === 0) $matched = true;
            }
            if (!$matched) {
                http_response_code(403);
                die("Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.");
            }
        } else {
            if (is_numeric($allowedRoles) && $userRoleId != $allowedRoles) {
                http_response_code(403);
                die("Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.");
            }
            if (is_string($allowedRoles) && strcasecmp($userRole, $allowedRoles) !== 0) {
                http_response_code(403);
                die("Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.");
            }
        }
    }

    public static function setFlash(string $key, string $message): void {
        self::initSession();
        $_SESSION['flash_' . $key] = $message;
    }

    public static function getFlash(string $key): ?string {
        self::initSession();
        if (isset($_SESSION['flash_' . $key])) {
            $msg = $_SESSION['flash_' . $key];
            unset($_SESSION['flash_' . $key]);
            return $msg;
        }
        return null;
    }
}

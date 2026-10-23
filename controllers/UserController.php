<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    public function __construct() {
        AuthHelper::requireLogin();
        // Hanya Project Leader (role_id = 3) dan Direktur (role_id = 4)
        AuthHelper::requireRole([3, 4]);
    }

    public function index(): void {
        $userModel = new User();
        $search = trim($_GET['search'] ?? '');
        if ($search !== '') {
            $users = $userModel->search($search);
        } else {
            $users = $userModel->getAll();
        }

        $roles = $userModel->getAllRoles();
        $pageTitle = 'Manajemen Pengguna';
        $activeMenu = 'users';

        require_once __DIR__ . '/../views/users/index.php';
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $roleId = (int)($_POST['role_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $organization = trim($_POST['organization'] ?? 'Yayasan Natural Kapital Indonesia');

        if (empty($name) || empty($email) || empty($password) || $roleId <= 0) {
            AuthHelper::setFlash('error', 'Semua kolom wajib diisi dengan benar.');
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userModel = new User();
        try {
            $userModel->create($roleId, $name, $email, $password, $organization);
            AuthHelper::setFlash('success', 'Pengguna ' . htmlspecialchars($name) . ' berhasil ditambahkan.');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                AuthHelper::setFlash('error', 'Email ' . htmlspecialchars($email) . ' sudah terdaftar.');
            } else {
                AuthHelper::setFlash('error', 'Gagal menambahkan pengguna: ' . $e->getMessage());
            }
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    public function edit(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $organization = trim($_POST['organization'] ?? '');
        $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

        if ($userId <= 0 || empty($name) || empty($email) || $roleId <= 0) {
            AuthHelper::setFlash('error', 'Data pengguna tidak valid.');
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userModel = new User();
        try {
            $userModel->update($userId, $roleId, $name, $email, $organization, $isActive);
            AuthHelper::setFlash('success', 'Data pengguna ' . htmlspecialchars($name) . ' berhasil diperbarui.');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                AuthHelper::setFlash('error', 'Email sudah digunakan pengguna lain.');
            } else {
                AuthHelper::setFlash('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
            }
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    public function resetPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = trim($_POST['new_password'] ?? '');

        if ($userId <= 0 || strlen($newPassword) < 5) {
            AuthHelper::setFlash('error', 'Kata sandi baru minimal 5 karakter.');
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userModel = new User();
        $success = $userModel->resetPassword($userId, $newPassword);
        if ($success) {
            AuthHelper::setFlash('success', 'Kata sandi pengguna berhasil direset.');
        } else {
            AuthHelper::setFlash('error', 'Gagal mereset kata sandi pengguna.');
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }

    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $currentUser = AuthHelper::getUser();

        if ($userId === (int)$currentUser['user_id']) {
            AuthHelper::setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
            header('Location: ' . BASE_URL . '/users');
            exit;
        }

        $userModel = new User();
        try {
            $userModel->delete($userId);
            AuthHelper::setFlash('success', 'Pengguna berhasil dihapus.');
        } catch (PDOException $e) {
            AuthHelper::setFlash('error', 'Pengguna tidak dapat dihapus karena terkait dengan data proyek, laporan, atau riwayat sistem. Anda dapat menonaktifkan status akunnya.');
        }

        header('Location: ' . BASE_URL . '/users');
        exit;
    }
}

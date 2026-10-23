<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
$currentUser = AuthHelper::getUser();
$pageTitle = $pageTitle ?? 'Sistem MEL';
$activeMenu = $activeMenu ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand-badge">
                    <img src="<?= BASE_URL ?>/gambar%20logo/ynki-icon.webp" alt="Logo YNKI" style="width: 100%; height: 100%; object-fit: contain; border-radius: inherit;">
                </div>
                <div class="brand-text">
                    <div class="brand-title">Sistem MEL</div>
                    <div class="brand-sub">Yayasan Natural Kapital Indonesia</div>
                </div>
                <!-- Tombol tunggal pengontrol sidebar (chevron) -->
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Tutup Sidebar" aria-label="Buka / Tutup Sidebar">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <?php
                // Hitung notifikasi unread untuk badge sidebar
                require_once __DIR__ . '/../../models/Notification.php';
                $notifModelSide = new Notification();
                $unreadSide = $notifModelSide->getUnreadCount($currentUser['user_id']);
                ?>

                <?php if ($currentUser['role_id'] == 1): // Aktor: Staf Lapangan ?>
                    <div class="nav-label">Menu Staf Lapangan</div>
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>/projects" class="nav-link <?= $activeMenu === 'projects' ? 'active' : '' ?>" title="Proyek & Kegiatan">
                        <i class="fa-solid fa-folder-tree"></i>
                        <span class="nav-text">Proyek & Kegiatan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/field-updates" class="nav-link <?= $activeMenu === 'field-updates' ? 'active' : '' ?>" title="Pelaporan Lapangan">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span class="nav-text">Pelaporan Lapangan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/corrections" class="nav-link <?= $activeMenu === 'corrections' ? 'active' : '' ?>" title="Permintaan Revisi">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="nav-text">Permintaan Revisi</span>
                    </a>
                    <a href="<?= BASE_URL ?>/notifications" class="nav-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="nav-text">Notifikasi</span>
                        <?php if ($unreadSide > 0): ?>
                        <span class="badge" style="background: #ef4444; color: #fff; font-size: 0.68rem; padding: 2px 6px; margin-left: auto;">
                            <?= $unreadSide ?>
                        </span>
                        <?php endif; ?>
                    </a>

                <?php elseif ($currentUser['role_id'] == 2): // Aktor: Staf MEL ?>
                    <div class="nav-label">Menu Staf MEL</div>
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>/projects" class="nav-link <?= $activeMenu === 'projects' ? 'active' : '' ?>" title="Proyek & Kegiatan">
                        <i class="fa-solid fa-folder-tree"></i>
                        <span class="nav-text">Proyek & Kegiatan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/field-updates" class="nav-link <?= $activeMenu === 'field-updates' ? 'active' : '' ?>" title="Laporan Lapangan">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span class="nav-text">Laporan Lapangan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/evaluations" class="nav-link <?= in_array($activeMenu, ['evaluations', 'corrections']) ? 'active' : '' ?>" title="Evaluasi & Perbaikan">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <span class="nav-text">Evaluasi & Perbaikan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/mel-results" class="nav-link <?= $activeMenu === 'mel-results' ? 'active' : '' ?>" title="Hasil MEL">
                        <i class="fa-solid fa-stamp"></i>
                        <span class="nav-text">Hasil MEL</span>
                    </a>
                    <a href="<?= BASE_URL ?>/notifications" class="nav-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="nav-text">Notifikasi</span>
                        <?php if ($unreadSide > 0): ?>
                        <span class="badge" style="background: #ef4444; color: #fff; font-size: 0.68rem; padding: 2px 6px; margin-left: auto;">
                            <?= $unreadSide ?>
                        </span>
                        <?php endif; ?>
                    </a>
                <?php elseif ($currentUser['role_id'] == 3): // Aktor: Project Leader ?>
                    <div class="nav-label">Menu Project Leader</div>
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>/projects" class="nav-link <?= $activeMenu === 'projects' ? 'active' : '' ?>" title="Proyek & Kegiatan">
                        <i class="fa-solid fa-folder-tree"></i>
                        <span class="nav-text">Proyek & Kegiatan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/field-updates" class="nav-link <?= $activeMenu === 'field-updates' ? 'active' : '' ?>" title="Pelaporan Lapangan">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span class="nav-text">Pelaporan Lapangan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/evaluations" class="nav-link <?= $activeMenu === 'evaluations' ? 'active' : '' ?>" title="Evaluasi Kinerja">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <span class="nav-text">Evaluasi Kinerja</span>
                    </a>
                    <a href="<?= BASE_URL ?>/corrections" class="nav-link <?= $activeMenu === 'corrections' ? 'active' : '' ?>" title="Permintaan Perbaikan">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="nav-text">Permintaan Perbaikan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/users" class="nav-link <?= $activeMenu === 'users' ? 'active' : '' ?>" title="Manajemen Pengguna">
                        <i class="fa-solid fa-users-gear"></i>
                        <span class="nav-text">Manajemen Pengguna</span>
                    </a>
                    <a href="<?= BASE_URL ?>/notifications" class="nav-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="nav-text">Notifikasi</span>
                        <?php if ($unreadSide > 0): ?>
                        <span class="badge" style="background: #ef4444; color: #fff; font-size: 0.68rem; padding: 2px 6px; margin-left: auto;">
                            <?= $unreadSide ?>
                        </span>
                        <?php endif; ?>
                    </a>

                <?php elseif ($currentUser['role_id'] == 4): // Aktor: Direktur ?>
                    <div class="nav-label">Menu Direktur</div>
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>/projects" class="nav-link <?= $activeMenu === 'projects' ? 'active' : '' ?>" title="Proyek & Kegiatan">
                        <i class="fa-solid fa-folder-tree"></i>
                        <span class="nav-text">Proyek & Kegiatan</span>
                    </a>
                    <a href="<?= BASE_URL ?>/evaluations" class="nav-link <?= $activeMenu === 'evaluations' ? 'active' : '' ?>" title="Ringkasan Capaian">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="nav-text">Ringkasan Capaian</span>
                    </a>
                    <a href="<?= BASE_URL ?>/mel-results" class="nav-link <?= $activeMenu === 'mel-results' ? 'active' : '' ?>" title="Verifikasi MEL">
                        <i class="fa-solid fa-stamp"></i>
                        <span class="nav-text">Verifikasi MEL</span>
                    </a>
                    <a href="<?= BASE_URL ?>/notifications" class="nav-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>" title="Notifikasi">
                        <i class="fa-solid fa-bell"></i>
                        <span class="nav-text">Notifikasi</span>
                        <?php if ($unreadSide > 0): ?>
                        <span class="badge" style="background: #ef4444; color: #fff; font-size: 0.68rem; padding: 2px 6px; margin-left: auto;">
                            <?= $unreadSide ?>
                        </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-badge" title="<?= htmlspecialchars($currentUser['name'] ?? '') ?> (<?= htmlspecialchars($currentUser['role_name'] ?? '') ?>)">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($currentUser['name'] ?? '') ?></div>
                        <div class="user-role"><?= htmlspecialchars($currentUser['role_name'] ?? '') ?></div>
                    </div>
                </div>
                <div class="logout-container" style="margin-top: 12px;">
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-secondary btn-sm logout-btn" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="logout-text">Keluar</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Backdrop overlay for mobile screen when sidebar is open -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Main Content Wrapper -->
        <main class="main-content" id="mainContent">
            <header class="navbar">
                <div class="page-title"><?= htmlspecialchars($pageTitle) ?></div>
                <div class="navbar-actions">
                    <a href="<?= BASE_URL ?>/notifications" class="btn btn-secondary btn-sm" style="position: relative;">
                        <i class="fa-solid fa-bell"></i>
                        <?php
                        require_once __DIR__ . '/../../models/Notification.php';
                        $notifModel = new Notification();
                        $unread = $notifModel->getUnreadCount($currentUser['user_id']);
                        if ($unread > 0):
                        ?>
                        <span style="position: absolute; top: -4px; right: -4px; background: var(--danger); color: #fff; border-radius: 50%; font-size: 0.7rem; padding: 2px 6px;">
                            <?= $unread ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <span class="badge badge-success">
                        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($currentUser['organization'] ?? 'YNKI') ?>
                    </span>
                </div>
            </header>

            <div class="content-body">
                <?php 
                $successMsg = AuthHelper::getFlash('success');
                // Jika sedang di dashboard dan pesan adalah ucapan login selamat datang, jangan tampilkan alert tipis untuk menghindari duplikasi dengan card header
                $isDashboard = isset($activeMenu) && $activeMenu === 'dashboard';
                $isWelcomeAlert = $successMsg && stripos($successMsg, 'Selamat datang') !== false;
                if ($successMsg && !($isDashboard && $isWelcomeAlert)): 
                ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?= htmlspecialchars($successMsg) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($errorMsg = AuthHelper::getFlash('error')): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span><?= htmlspecialchars($errorMsg) ?></span>
                    </div>
                <?php endif; ?>


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
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="brand-badge">MEL</div>
                <div>
                    <div class="brand-title">Sistem MEL</div>
                    <div class="brand-sub">Yayasan YNKI</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Utama</div>
                <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/projects" class="nav-link <?= $activeMenu === 'projects' ? 'active' : '' ?>">
                    <i class="fa-solid fa-folder-tree"></i>
                    <span>Proyek & Kegiatan</span>
                </a>

                <div class="nav-label">Operasional MEL</div>
                <a href="<?= BASE_URL ?>/field-updates" class="nav-link <?= $activeMenu === 'field-updates' ? 'active' : '' ?>">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Pelaporan Lapangan</span>
                </a>
                
                <?php if (in_array($currentUser['role_id'], [2, 3, 4])): // MEL, PL, Direktur ?>
                <a href="<?= BASE_URL ?>/evaluations" class="nav-link <?= $activeMenu === 'evaluations' ? 'active' : '' ?>">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Evaluasi Kinerja</span>
                </a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/corrections" class="nav-link <?= $activeMenu === 'corrections' ? 'active' : '' ?>">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Permintaan Perbaikan</span>
                </a>

                <?php if (in_array($currentUser['role_id'], [2, 4])): // MEL, Direktur ?>
                <a href="<?= BASE_URL ?>/mel-results" class="nav-link <?= $activeMenu === 'mel-results' ? 'active' : '' ?>">
                    <i class="fa-solid fa-stamp"></i>
                    <span>Pengesahan & Hasil</span>
                </a>
                <?php endif; ?>

                <div class="nav-label">Informasi & Utilitas</div>
                <a href="<?= BASE_URL ?>/notifications" class="nav-link <?= $activeMenu === 'notifications' ? 'active' : '' ?>">
                    <i class="fa-solid fa-bell"></i>
                    <span>Notifikasi & Reminder</span>
                </a>
                <a href="<?= BASE_URL ?>/public-dashboard" target="_blank" class="nav-link">
                    <i class="fa-solid fa-globe"></i>
                    <span>Portal Publik <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem; margin-left: 4px;"></i></span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-badge">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($currentUser['name']) ?></div>
                        <div class="user-role"><?= htmlspecialchars($currentUser['role_name']) ?></div>
                    </div>
                </div>
                <div style="margin-top: 12px; display: flex; gap: 8px;">
                    <a href="<?= BASE_URL ?>/logout" class="btn btn-secondary btn-sm" style="width: 100%;">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="main-content">
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
                <?php if ($successMsg = AuthHelper::getFlash('success')): ?>
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

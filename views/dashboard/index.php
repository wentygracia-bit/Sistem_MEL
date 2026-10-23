<?php
require_once __DIR__ . '/../layouts/header.php';
$currentUser = $currentUser ?? AuthHelper::getUser();

// Penentuan badge peran, ikon, dan deskripsi dinamis berdasarkan role_id
$userRoleId = (int)($currentUser['role_id'] ?? 0);
switch ($userRoleId) {
    case 1:
        $roleBadgeText = 'Staf Lapangan';
        $roleBadgeIcon = 'fa-person-digging';
        $roleDescription = 'Ringkasan tugas lapangan, pengiriman laporan, dan respons perbaikan.';
        break;
    case 2:
        $roleBadgeText = 'Staf MEL';
        $roleBadgeIcon = 'fa-user-check';
        $roleDescription = 'Ringkasan pemantauan proyek, laporan lapangan masuk, tindak lanjut perbaikan, dan dokumen hasil MEL.';
        break;
    case 3:
        $roleBadgeText = 'Project Leader';
        $roleBadgeIcon = 'fa-user-tie';
        $roleDescription = 'Ringkasan evaluasi indikator kinerja, peninjauan laporan, dan permintaan perbaikan.';
        break;
    case 4:
        $roleBadgeText = 'Direktur';
        $roleBadgeIcon = 'fa-crown';
        $roleDescription = 'Ringkasan capaian proyek organisasi dan antrean pengesahan hasil MEL.';
        break;
    default:
        $roleBadgeText = $currentUser['role_name'] ?? 'Pengguna';
        $roleBadgeIcon = 'fa-user';
        $roleDescription = 'Ringkasan operasional dan pemantauan program YNKI.';
        break;
}
?>

<!-- Card Header Sapaan Pengguna Dinamis (Multi-Role) -->
<div style="background: linear-gradient(135deg, #ffffff 0%, #f4faeb 100%); border: 1px solid var(--border-color); border-left: 6px solid var(--primary-dark); border-radius: var(--radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px;">
                <i class="fa-solid <?= $roleBadgeIcon ?>"></i> <?= htmlspecialchars($roleBadgeText) ?>
            </span>
            <span style="font-size: 0.82rem; color: var(--text-muted); font-weight: 500;">Yayasan Natural Kapital Indonesia</span>
        </div>
        <h1 style="font-size: 1.4rem; font-weight: 800; color: #1e330c; margin: 6px 0 2px 0;">
            Selamat datang kembali, <?= htmlspecialchars($currentUser['name'] ?? $roleBadgeText) ?>!
        </h1>
        <p style="font-size: 0.88rem; color: #475569; margin: 0;">
            <?= htmlspecialchars($roleDescription) ?>
        </p>
    </div>
    <?php if ((int)$currentUser['role_id'] === 3): ?>
        <div>
            <a href="<?= BASE_URL ?>/users" class="btn btn-primary" style="height: 38px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users-gear"></i> Manajemen Pengguna
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if ((int)$currentUser['role_id'] === 3 && !empty($plStats)): ?>
<!-- 4 KPI Cards Khusus Project Leader (Mencerminkan Tanggung Jawab PL) -->
<div class="grid-cols-4" style="margin-bottom: 24px;">
    <!-- 1. Proyek Aktif -->
    <a href="<?= BASE_URL ?>/projects" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 5px solid #2e4a16; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(43, 107, 43, 0.12); color: #2e4a16;">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #2e4a16;"><?= number_format($plStats['active_projects'], 0, ',', '.') ?></div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Proyek Aktif</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                    Dari total <?= $plStats['total_projects'] ?> proyek kewenangan PL
                </div>
            </div>
        </div>
    </a>

    <!-- 2. Progres Kegiatan -->
    <a href="<?= BASE_URL ?>/projects" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 5px solid #0284c7; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="fa-solid fa-bars-progress"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #0284c7;"><?= $plStats['avg_progress'] ?>%</div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Progres Kegiatan</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                    Rata-rata <?= $plStats['total_activities'] ?> kegiatan teragregasi unik
                </div>
            </div>
        </div>
    </a>

    <!-- 3. Laporan Tindak Lanjut -->
    <a href="<?= BASE_URL ?>/field-updates?status=Diajukan" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 5px solid #eab308; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; <?= $plStats['pending_review_reports'] > 0 ? 'background: #fefce8;' : '' ?>">
            <div class="stat-icon" style="background: rgba(234, 179, 8, 0.14); color: #a16207;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="stat-value" style="color: <?= $plStats['pending_review_reports'] > 0 ? '#a16207' : '#1e293b' ?>;">
                    <?= number_format($plStats['pending_review_reports'], 0, ',', '.') ?>
                </div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Laporan Tindak Lanjut</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                    Menunggu evaluasi kesesuaian target
                </div>
            </div>
        </div>
    </a>

    <!-- 4. Perlu Perbaikan -->
    <a href="<?= BASE_URL ?>/field-updates?status=Perlu+Perbaikan" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 5px solid #dc2626; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; <?= $plStats['total_attention'] > 0 ? 'background: #fff5f5;' : '' ?>">
            <div class="stat-icon" style="background: rgba(220, 38, 38, 0.12); color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: <?= $plStats['total_attention'] > 0 ? '#dc2626' : '#1e293b' ?>;">
                    <?= number_format($plStats['total_attention'], 0, ',', '.') ?>
                </div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Perlu Perbaikan</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                    <?= $plStats['needs_correction_reports'] ?> laporan revisi &bull; <?= $plStats['delayed_activities'] ?> kegiatan terlambat
                </div>
            </div>
        </div>
    </a>
</div>
<?php else: ?>
<!-- KPI Stats Grid Standar (Staf MEL) -->
<div class="grid-cols-4">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-folder"></i></div>
        <div>
            <div class="stat-value"><?= count($projects) ?></div>
            <div class="stat-label">Proyek Dikelola</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div>
            <div class="stat-value"><?= count($recentUpdates) ?></div>
            <div class="stat-label">Laporan Masuk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-rotate-left"></i></div>
        <div>
            <div class="stat-value"><?= count($recentCorrections) ?></div>
            <div class="stat-label">Perbaikan Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-stamp"></i></div>
        <div>
            <div class="stat-value"><?= count($melResults) ?></div>
            <div class="stat-label">Hasil MEL</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid-cols-2">
    <!-- Projects Section -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Proyek & Program Berjalan</h2>
            <?php if (in_array($currentUser['role_id'], [2, 4])): // Hanya Staf MEL & Direktur ?>
                <a href="<?= BASE_URL ?>/projects/create" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Proyek
                </a>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Proyek</th>
                        <th class="text-center">Kegiatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada proyek.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $prj): ?>
                            <tr>
                                <td><span style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($prj['project_code']) ?></span></td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($prj['project_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($prj['creator_name'] ?? 'YNKI') ?></div>
                                </td>
                                <td class="text-center"><span class="badge badge-info"><?= $prj['total_activities'] ?> Kegiatan</span></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($prj['status']) ?></span></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/projects/detail?id=<?= $prj['project_id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Field Updates -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Laporan Lapangan Terkini</h2>
            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pelapor</th>
                        <th>Kegiatan</th>
                        <th>Capaian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentUpdates)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada laporan lapangan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentUpdates as $up): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($up['reporter_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('d M Y', strtotime($up['update_date'])) ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($up['activity_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #1e330c;"><?= number_format($up['realization_value'], 0, ',', '.') ?></span>
                                    <?php if ($up['progress_percentage']): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= $up['progress_percentage'] ?>%</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($up['status'] === 'Dievaluasi'): ?>
                                        <span class="badge badge-success"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php elseif ($up['status'] === 'Perlu Perbaikan'): ?>
                                        <span class="badge badge-danger"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?= htmlspecialchars($up['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

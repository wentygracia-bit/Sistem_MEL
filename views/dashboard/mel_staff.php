<?php
require_once __DIR__ . '/../layouts/header.php';
$currentUser = $currentUser ?? AuthHelper::getUser();
?>

<!-- Banner Sapaan Pengguna Staf MEL -->
<div style="background: linear-gradient(135deg, #ffffff 0%, #f4faeb 100%); border: 1px solid var(--border-color); border-left: 6px solid var(--primary-dark); border-radius: var(--radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px;">
                <i class="fa-solid fa-user-check"></i> Staf MEL
            </span>
            <span style="font-size: 0.82rem; color: var(--text-muted); font-weight: 500;">Yayasan Natural Kapital Indonesia</span>
        </div>
        <h1 style="font-size: 1.4rem; font-weight: 800; color: #1e330c; margin: 6px 0 2px 0;">
            Selamat datang kembali, <?= htmlspecialchars($currentUser['name'] ?? 'Staf MEL') ?>!
        </h1>
        <p style="font-size: 0.88rem; color: #475569; margin: 0;">
            Ringkasan pemantauan proyek, laporan lapangan masuk, tindak lanjut perbaikan, dan dokumen hasil MEL YNKI.
        </p>
    </div>
</div>

<!-- 4 Card Utama Dashboard Staf MEL -->
<div class="grid-cols-4" style="margin-bottom: 24px;">
    <!-- Card 1: Proyek Aktif -->
    <a href="<?= BASE_URL ?>/projects" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 6px solid #2e4a16; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background-color: #f4faeb; color: #2e4a16;">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($activeProjectsCount ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Proyek Aktif</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Status Aktif di Sistem</div>
            </div>
        </div>
    </a>

    <!-- Card 2: Laporan Masuk -->
    <a href="<?= BASE_URL ?>/field-updates" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 6px solid #0284c7; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background-color: #f0f9ff; color: #0284c7;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($totalReportsCount ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Laporan Masuk</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Total Laporan Lapangan</div>
            </div>
        </div>
    </a>

    <!-- Card 3: Perbaikan Aktif -->
    <a href="<?= BASE_URL ?>/evaluations?tab=perbaikan" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 6px solid <?= ($activeCorrectionsCount ?? 0) > 0 ? '#dc2626' : '#d97706' ?>; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background-color: <?= ($activeCorrectionsCount ?? 0) > 0 ? '#fef2f2' : '#fffbeb' ?>; color: <?= ($activeCorrectionsCount ?? 0) > 0 ? '#dc2626' : '#d97706' ?>;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <div class="stat-value" style="<?= ($activeCorrectionsCount ?? 0) > 0 ? 'color: #dc2626;' : '' ?>">
                    <?= number_format($activeCorrectionsCount ?? 0, 0, ',', '.') ?>
                </div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Perbaikan Aktif</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Menunggu Tindak Lanjut</div>
            </div>
        </div>
    </a>

    <!-- Card 4: Hasil MEL -->
    <a href="<?= BASE_URL ?>/mel-results" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="border-left: 6px solid #16a34a; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background-color: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($totalMelResultsCount ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label" style="font-weight: 600; color: #1e293b;">Hasil MEL</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Dokumen MEL Disusun</div>
            </div>
        </div>
    </a>
</div>

<!-- Baris 1: Layout Utama 2 Kolom (Kiri: Laporan Lapangan Terbaru, Kanan: Kegiatan Mendekati Tenggat) -->
<div style="display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr); gap: 20px; align-items: start; margin-bottom: 24px;">
    
    <!-- Kolom Kiri: Laporan Lapangan untuk Diperiksa -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-clipboard-list" style="color: var(--primary-dark);"></i> Laporan Lapangan Terbaru
                </h2>
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                    Laporan masuk dari Staf Lapangan yang siap diperiksa dan diverifikasi oleh Staf MEL.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                Lihat Semua <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pelapor & Tanggal</th>
                        <th>Kegiatan & Proyek</th>
                        <th>Capaian</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentUpdates)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 36px 20px;">
                                <i class="fa-solid fa-clipboard-check" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                Belum ada laporan lapangan yang siap diperiksa.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentUpdates as $up): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a; font-size: 0.88rem;"><?= htmlspecialchars($up['reporter_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('d M Y', strtotime($up['update_date'])) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($up['activity_name']) ?>">
                                        <?= htmlspecialchars($up['activity_name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($up['project_name']) ?>">
                                        <?= htmlspecialchars($up['project_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #1e330c; font-size: 0.9rem;">
                                        <?= number_format($up['realization_value'] ?? 0, 0, ',', '.') ?>
                                    </span>
                                    <?php if (isset($up['progress_percentage']) && $up['progress_percentage'] !== null): ?>
                                        <div style="font-size: 0.72rem; color: var(--text-muted);"><?= $up['progress_percentage'] ?>% progres</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($up['status'] === 'Dievaluasi'): ?>
                                        <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fa-solid fa-circle-check"></i> Dievaluasi</span>
                                    <?php elseif ($up['status'] === 'Perlu Perbaikan'): ?>
                                        <span class="badge badge-danger" style="font-size: 0.72rem;"><i class="fa-solid fa-triangle-exclamation"></i> Perlu Perbaikan</span>
                                    <?php elseif ($up['status'] === 'Draft'): ?>
                                        <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.72rem;"><i class="fa-solid fa-pen-ruler"></i> Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($up['status'] ?: 'Menunggu Verifikasi') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" title="Periksa Laporan Lapangan">
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

    <!-- Kolom Kanan: Panel Kegiatan Mendekati Tenggat -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-clock" style="color: #d97706;"></i> Kegiatan Mendekati Tenggat
                </h2>
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                    Kegiatan aktif dengan tanggal tenggat (deadline) terdekat untuk dipantau tepat waktu.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                Proyek <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
            </a>
        </div>

        <div style="padding: 16px 20px;">
            <?php if (empty($upcomingActivities)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 32px 16px;">
                    <i class="fa-solid fa-calendar-check" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                    Tidak ada kegiatan mendekati tenggat.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($upcomingActivities as $act): ?>
                        <?php
                        $diffDays = (int)($act['diff_days'] ?? 0);
                        $isOverdue = $diffDays < 0;
                        $cardBg = $isOverdue ? '#fef2f2' : ($diffDays <= 7 ? '#fffbeb' : '#f8fafc');
                        $cardBorder = $isOverdue ? '#fca5a5' : ($diffDays <= 7 ? '#fde68a' : 'var(--border-color)');
                        ?>
                        <div style="padding: 12px 14px; background: <?= $cardBg ?>; border: 1px solid <?= $cardBorder ?>; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-weight: 700; font-size: 0.88rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($act['activity_name']) ?>">
                                    <?= htmlspecialchars($act['activity_name']) ?>
                                </div>
                                <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 3px;">
                                    <?= htmlspecialchars($act['project_name']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: #475569; margin-top: 4px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span><i class="fa-regular fa-calendar" style="color: #64748b;"></i> Tenggat: <strong><?= date('d M Y', strtotime($act['end_date'])) ?></strong></span>
                                    <span>&bull;</span>
                                    <span>
                                        Status: 
                                        <span class="badge <?= $act['display_status'] === 'Terlambat' ? 'badge-danger' : ($act['display_status'] === 'Berjalan' ? 'badge-success' : 'badge-warning') ?>" style="font-size: 0.68rem; padding: 2px 6px;">
                                            <?= htmlspecialchars($act['display_status']) ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <?php if ($diffDays < 0): ?>
                                    <span class="badge badge-danger" style="font-size: 0.74rem;">
                                        Lewat <?= abs($diffDays) ?> hari
                                    </span>
                                <?php elseif ($diffDays === 0): ?>
                                    <span class="badge badge-warning" style="font-size: 0.74rem;">
                                        Hari ini!
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="font-size: 0.74rem;">
                                        <?= $diffDays ?> hari lagi
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Baris 2: Aktivitas Terbaru (Full Width / 100%) -->
<div style="width: 100%;">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-dark);"></i> Aktivitas Terbaru
                </h2>
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                    Kronologi pelaporan lapangan, evaluasi, dan keputusan pengesahan yang tercatat di sistem.
                </p>
            </div>
        </div>

        <div style="padding: 16px 20px;">
            <?php if (empty($recentActivities)): ?>
                <div style="text-align: center; color: var(--text-muted); padding: 24px;">
                    <i class="fa-solid fa-inbox" style="font-size: 1.6rem; color: #cbd5e1; margin-bottom: 6px; display: block;"></i>
                    Belum ada aktivitas yang tercatat di sistem.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <?php foreach ($recentActivities as $act): ?>
                        <?php
                        $iconColor = '#0284c7';
                        $iconBg = '#f0f9ff';
                        $iconClass = 'fa-clipboard-check';
                        $actionUrl = BASE_URL . '/field-updates';

                        if ($act['act_type'] === 'evaluasi') {
                            $iconColor = '#7c3aed';
                            $iconBg = '#f5f3ff';
                            $iconClass = 'fa-scale-balanced';
                            $actionUrl = BASE_URL . '/evaluations';
                        } elseif ($act['act_type'] === 'hasil_mel') {
                            $iconColor = '#16a34a';
                            $iconBg = '#f0fdf4';
                            $iconClass = 'fa-file-signature';
                            $actionUrl = BASE_URL . '/mel-results/detail?id=' . $act['ref_id'];
                        } elseif ($act['act_type'] === 'keputusan') {
                            $iconColor = $act['event_status'] === 'Disetujui' ? '#15803d' : '#b91c1c';
                            $iconBg = $act['event_status'] === 'Disetujui' ? '#f0fdf4' : '#fef2f2';
                            $iconClass = 'fa-stamp';
                            $actionUrl = BASE_URL . '/mel-results/detail?id=' . $act['ref_id'];
                        } elseif ($act['act_type'] === 'laporan') {
                            $actionUrl = BASE_URL . '/field-updates/detail?id=' . $act['ref_id'];
                        }
                        ?>
                        <div style="display: flex; gap: 14px; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: <?= $iconBg ?>; color: <?= $iconColor ?>; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; margin-top: 2px;">
                                <i class="fa-solid <?= $iconClass ?>"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 8px; flex-wrap: wrap;">
                                    <div style="font-size: 0.88rem; font-weight: 700; color: #0f172a;">
                                        <?= htmlspecialchars($act['act_category']) ?>: <?= htmlspecialchars($act['activity_name']) ?>
                                    </div>
                                    <span style="font-size: 0.74rem; color: var(--text-muted); white-space: nowrap;">
                                        <?= date('d M Y', strtotime($act['event_date'])) ?>
                                    </span>
                                </div>
                                <div style="font-size: 0.78rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span>
                                        Oleh: <strong style="color: #0f172a; font-weight: 700;"><?= htmlspecialchars($act['actor_name']) ?></strong>
                                    </span>
                                    <?php if (!empty($act['actor_role'])): ?>
                                        <?php
                                        $roleBadgeBg = '#f1f5f9';
                                        $roleBadgeColor = '#475569';
                                        $roleBadgeBorder = '#cbd5e1';
                                        if ($act['actor_role'] === 'Project Leader') {
                                            $roleBadgeBg = '#eff6ff';
                                            $roleBadgeColor = '#1d4ed8';
                                            $roleBadgeBorder = '#bfdbfe';
                                        } elseif ($act['actor_role'] === 'Direktur') {
                                            $roleBadgeBg = '#fdf4ff';
                                            $roleBadgeColor = '#86198f';
                                            $roleBadgeBorder = '#f5d0fe';
                                        } elseif ($act['actor_role'] === 'Staf MEL') {
                                            $roleBadgeBg = '#f0fdf4';
                                            $roleBadgeColor = '#15803d';
                                            $roleBadgeBorder = '#bbf7d0';
                                        } elseif ($act['actor_role'] === 'Staf Lapangan') {
                                            $roleBadgeBg = '#fffbeb';
                                            $roleBadgeColor = '#b45309';
                                            $roleBadgeBorder = '#fde68a';
                                        }
                                        ?>
                                        <span class="badge" style="background: <?= $roleBadgeBg ?>; color: <?= $roleBadgeColor ?>; border: 1px solid <?= $roleBadgeBorder ?>; font-size: 0.68rem; padding: 2px 7px; font-weight: 600;">
                                            <?= htmlspecialchars($act['actor_role']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span style="color: #cbd5e1;">&bull;</span>
                                    <span>
                                        Proyek: <strong style="color: #1e3a5f; font-weight: 700;"><?= htmlspecialchars($act['project_name']) ?></strong>
                                    </span>
                                </div>
                                <?php if (!empty($act['details'])): ?>
                                    <div style="font-size: 0.8rem; color: #475569; margin-top: 4px; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border-left: 3px solid <?= $iconColor ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($act['details']) ?>">
                                        <?= htmlspecialchars($act['details']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="<?= $actionUrl ?>" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 0.75rem;" title="Lihat">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

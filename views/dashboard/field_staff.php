<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- 4 Kartu Ringkasan (KPI Cards) Staf Lapangan -->
<div class="grid-cols-4" style="margin-bottom: 24px;">
    <!-- 1. Total Kegiatan -->
    <a href="<?= BASE_URL ?>/projects" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(43, 107, 43, 0.12); color: #2e4a16;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= $totalAssignedActivities ?></div>
                <div class="stat-label">Total Kegiatan Ditugaskan</div>
            </div>
        </div>
    </a>

    <!-- 2. Kegiatan Sedang Berjalan -->
    <a href="<?= BASE_URL ?>/projects?status=Berjalan" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="fa-solid fa-person-digging"></i>
            </div>
            <div>
                <div class="stat-value"><?= $runningActivitiesCount ?></div>
                <div class="stat-label">Sedang Berjalan</div>
            </div>
        </div>
    </a>

    <!-- 3. Laporan Belum Dikirim (Draft) -->
    <a href="<?= BASE_URL ?>/field-updates?status=Draft" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div>
                <div class="stat-value"><?= $draftReportsCount ?></div>
                <div class="stat-label">Laporan Draft / Belum Kirim</div>
            </div>
        </div>
    </a>

    <!-- 4. Permintaan Revisi -->
    <a href="<?= BASE_URL ?>/corrections" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; <?= $pendingRevisionsCount > 0 ? 'border: 1px solid #fca5a5; background: #fff5f5;' : '' ?>">
            <div class="stat-icon" style="background: rgba(220, 38, 38, 0.12); color: #dc2626;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <div class="stat-value" style="<?= $pendingRevisionsCount > 0 ? 'color: #dc2626;' : '' ?>"><?= $pendingRevisionsCount ?></div>
                <div class="stat-label">Permintaan Revisi</div>
            </div>
        </div>
    </a>
</div>

<!-- Layout 2 Kolom: Progress Kegiatan & Sisi Kanan (Deadline + Aktivitas Terbaru) -->
<div class="grid-cols-2" style="margin-bottom: 24px;">
    <!-- Bagian Kiri: Progress Kegiatan (Maks 5 kegiatan relevan) -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title">
                <i class="fa-solid fa-bars-progress" style="color: var(--primary-dark);"></i> Progress Kegiatan Lapangan
            </h2>
            <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm">
                Lihat Semua Kegiatan <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
            </a>
        </div>

        <?php if (empty($topActivities)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                Belum ada kegiatan yang ditugaskan ke Anda saat ini.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($topActivities as $act): ?>
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; transition: box-shadow 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
                            <div>
                                <a href="<?= BASE_URL ?>/projects/detail?id=<?= $act['project_id'] ?>#act-<?= $act['activity_id'] ?>" style="font-weight: 700; color: #0f172a; text-decoration: none; font-size: 0.95rem;">
                                    <?= htmlspecialchars($act['activity_name']) ?>
                                </a>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                    <i class="fa-solid fa-folder" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($act['project_name']) ?> &bull; 
                                    <i class="fa-solid fa-location-dot" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($act['location'] ?: 'Lapangan') ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($act['display_status'] === 'Selesai'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                                <?php elseif ($act['display_status'] === 'Terlambat'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat</span>
                                <?php elseif ($act['display_status'] === 'Belum Dimulai'): ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-clock"></i> Belum Dimulai</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-person-running"></i> Berjalan</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info Jadwal & Deadline -->
                        <div style="font-size: 0.8rem; color: #475569; margin-bottom: 12px; display: flex; gap: 16px; flex-wrap: wrap;">
                            <span>
                                <i class="fa-regular fa-calendar"></i> Mulai: <strong><?= $act['start_date'] ? date('d M Y', strtotime($act['start_date'])) : '-' ?></strong>
                            </span>
                            <span>
                                <i class="fa-solid fa-flag-checkered"></i> Deadline: <strong><?= $act['end_date'] ? date('d M Y', strtotime($act['end_date'])) : '-' ?></strong>
                            </span>
                        </div>

                        <!-- Progress Bar & Target/Realisasi Terverifikasi -->
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 0.82rem; margin-bottom: 6px;">
                                <span style="color: #475569;">
                                    Capaian Terverifikasi: 
                                    <strong style="color: #2e4a16; font-size: 0.95rem;"><?= number_format($act['verified_realization'], 0, ',', '.') ?></strong> / <?= number_format($act['total_target'], 0, ',', '.') ?> <?= htmlspecialchars($act['unit']) ?>
                                    <?php if ($act['total_submitted_realization'] > $act['verified_realization']): ?>
                                        <small style="color: #d97706; margin-left: 4px;" title="Realisasi diajukan yang belum dievaluasi resmi">(+<?= number_format($act['total_submitted_realization'] - $act['verified_realization'], 0, ',', '.') ?> diajukan)</small>
                                    <?php endif; ?>
                                </span>
                                <span style="font-weight: 800; color: #1e330c; font-size: 0.95rem;"><?= $act['calculated_progress'] ?>%</span>
                            </div>

                            <div class="progress-bar-bg" style="width: 100%; height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden;">
                                <div class="progress-bar-fill" style="width: <?= min(100, $act['calculated_progress']) ?>%; height: 100%; background: var(--primary); transition: width 0.4s ease;"></div>
                            </div>
                        </div>

                        <!-- Action Link -->
                        <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; padding-top: 10px; border-top: 1px dashed var(--border-color);">
                            <a href="<?= BASE_URL ?>/field-updates?search=<?= urlencode($act['activity_name']) ?>" class="btn btn-secondary btn-sm" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-list"></i> Lihat Laporan (<?= $act['total_reports'] ?>)
                            </a>
                            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-primary btn-sm" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-plus"></i> Input Laporan
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bagian Kanan: Deadline Terdekat & Aktivitas Terbaru -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Deadline Terdekat -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fa-solid fa-stopwatch" style="color: #d97706;"></i> Deadline Terdekat
                </h2>
            </div>
            <?php if (empty($upcomingDeadlines)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">Tidak ada kegiatan yang mendekati deadline dalam 14 hari ke depan.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($upcomingDeadlines as $dl): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: <?= $dl['diff_days'] < 0 ? '#fef2f2' : '#fffbeb' ?>; border: 1px solid <?= $dl['diff_days'] < 0 ? '#fca5a5' : '#fde68a' ?>; border-radius: var(--radius-sm);">
                            <div>
                                <div style="font-weight: 700; font-size: 0.88rem; color: #1e293b;"><?= htmlspecialchars($dl['activity_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    <?= htmlspecialchars($dl['project_name']) ?> &bull; Batas: <strong><?= date('d M Y', strtotime($dl['end_date'])) ?></strong>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <?php if ($dl['diff_days'] < 0): ?>
                                    <span class="badge badge-danger" style="font-size: 0.75rem;">Terlambat <?= abs($dl['diff_days']) ?> hari</span>
                                <?php elseif ($dl['diff_days'] === 0): ?>
                                    <span class="badge badge-warning" style="font-size: 0.75rem;">Hari ini!</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="font-size: 0.75rem;"><?= $dl['diff_days'] ?> hari lagi</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Aktivitas & Riwayat Terbaru -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-dark);"></i> Aktivitas Terbaru Anda
                </h2>
                <a href="<?= BASE_URL ?>/notifications" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Pusat Notifikasi</a>
            </div>

            <?php if (empty($recentStaffUpdates) && empty($recentNotifications)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada riwayat aktivitas terbaru.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($recentStaffUpdates as $up): ?>
                        <div style="display: flex; gap: 12px; align-items: flex-start; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; color: #1e293b;">
                                    Laporan <strong>#<?= $up['update_id'] ?></strong>: <?= htmlspecialchars($up['activity_name']) ?>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 3px;">
                                    <span class="badge <?= $up['status'] === 'Dievaluasi' ? 'badge-success' : ($up['status'] === 'Perlu Perbaikan' ? 'badge-danger' : 'badge-warning') ?>" style="font-size: 0.68rem;">
                                        <?= htmlspecialchars($up['status']) ?>
                                    </span>
                                    <span style="font-size: 0.74rem; color: var(--text-muted);">
                                        <?= date('d M Y', strtotime($up['update_date'])) ?> &bull; Capaian: <?= number_format($up['realization_value'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="Lihat">
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

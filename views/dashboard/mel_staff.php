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
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-clipboard-check"></i> Laporan Lapangan
        </a>
        <a href="<?= BASE_URL ?>/mel-results" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-stamp"></i> Hasil MEL
        </a>
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

<!-- Layout Utama 2 Kolom: Kiri Laporan Lapangan Terbaru, Kanan Permintaan Perbaikan & Hasil MEL -->
<div style="display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); gap: 20px; align-items: start;">
    
    <!-- Kolom Kiri: Laporan Lapangan Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-clipboard-list" style="color: var(--primary-dark);"></i> Laporan Lapangan Terbaru
                </h2>
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                    Laporan masuk dari Staf Lapangan yang siap ditinjau atau dievaluasi.
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
                                Belum ada laporan lapangan.
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
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($up['activity_name']) ?>">
                                        <?= htmlspecialchars($up['activity_name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($up['project_name']) ?>">
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
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($up['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Rincian Laporan">
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

    <!-- Kolom Kanan: Permintaan Perbaikan & Ringkasan Hasil MEL -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- Bagian 1: Permintaan Perbaikan Terbaru -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-rotate-left" style="color: var(--primary-dark);"></i> Permintaan Perbaikan Terbaru
                    </h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                        Catatan revisi dari Project Leader kepada Staf Lapangan.
                    </p>
                </div>
                <a href="<?= BASE_URL ?>/evaluations?tab=perbaikan" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                    Lihat Semua <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kegiatan & Tanggal</th>
                            <th>Ditugaskan ke</th>
                            <th>Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentCorrections)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 28px 16px;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 1.6rem; color: #cbd5e1; margin-bottom: 6px; display: block;"></i>
                                    Tidak ada permintaan perbaikan aktif.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentCorrections as $c): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 0.85rem; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c['activity_name']) ?>">
                                            <?= htmlspecialchars($c['activity_name']) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('d M Y', strtotime($c['request_date'])) ?></div>
                                        <?php if (!empty($c['description'])): ?>
                                            <div style="font-size: 0.74rem; color: #64748b; margin-top: 2px; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c['description']) ?>">
                                                <?= htmlspecialchars($c['description']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.82rem; font-weight: 500; color: #334155;"><?= htmlspecialchars($c['assigned_to_name']) ?></div>
                                        <div style="font-size: 0.72rem; color: var(--text-muted);">Oleh: <?= htmlspecialchars($c['requested_by_name']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($c['status'] === 'Direspons'): ?>
                                            <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fa-solid fa-reply"></i> Direspons</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fa-solid fa-clock"></i> Diminta</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?= BASE_URL ?>/corrections/detail?id=<?= $c['correction_id'] ?>" class="btn btn-secondary btn-sm" title="Rincian Permintaan">
                                            <i class="fa-solid fa-comments"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian 2: Ringkasan Hasil MEL -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-stamp" style="color: var(--primary-dark);"></i> Ringkasan Hasil MEL
                    </h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                        Dokumen evaluasi MEL dan status pengesahan Direktur.
                    </p>
                </div>
                <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                    Lihat Semua <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kegiatan & Pengajuan</th>
                            <th>Status Pengesahan</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentMelResults)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 28px 16px;">
                                    <i class="fa-solid fa-file-contract" style="font-size: 1.6rem; color: #cbd5e1; margin-bottom: 6px; display: block;"></i>
                                    Belum ada berkas hasil MEL yang disusun.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentMelResults as $m): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 0.85rem; max-width: 190px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($m['activity_name']) ?>">
                                            <?= htmlspecialchars($m['activity_name']) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?= date('d M Y', strtotime($m['submission_date'])) ?> &bull; <?= htmlspecialchars($m['project_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($m['status'] === 'Disetujui'): ?>
                                            <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fa-solid fa-stamp"></i> Disetujui</span>
                                        <?php elseif ($m['status'] === 'Ditolak'): ?>
                                            <span class="badge badge-danger" style="font-size: 0.72rem;"><i class="fa-solid fa-ban"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fa-solid fa-clock"></i> Diajukan</span>
                                        <?php endif; ?>

                                        <?php if (!empty($m['approver_name'])): ?>
                                            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">
                                                Oleh: <?= htmlspecialchars($m['approver_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?= BASE_URL ?>/mel-results/detail?id=<?= $m['mel_result_id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Dokumen Hasil MEL">
                                            <i class="fa-solid fa-file-contract"></i>
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
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

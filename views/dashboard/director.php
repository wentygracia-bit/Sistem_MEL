<?php
require_once __DIR__ . '/../layouts/header.php';
$currentUser = $currentUser ?? AuthHelper::getUser();


// Kalkulasi persentase status kegiatan proyek
$totalAct = $activityStats['Total'];
$cntBerjalan = $activityStats['Berjalan'];
$cntSelesai = $activityStats['Selesai'];
$cntPerencanaan = $activityStats['Perencanaan'];

$pctBerjalan = $totalAct > 0 ? round(($cntBerjalan / $totalAct) * 100, 1) : 0;
$pctSelesai = $totalAct > 0 ? round(($cntSelesai / $totalAct) * 100, 1) : 0;
$pctPerencanaan = $totalAct > 0 ? round(($cntPerencanaan / $totalAct) * 100, 1) : 0;

// Parameter SVG Donut Chart (Radius = 60, Keliling = 2 * PI * 60 ~= 376.991)
$circ = 2 * M_PI * 60;
$lenBerjalan = $totalAct > 0 ? ($cntBerjalan / $totalAct) * $circ : 0;
$lenSelesai = $totalAct > 0 ? ($cntSelesai / $totalAct) * $circ : 0;
$lenPerencanaan = $totalAct > 0 ? ($cntPerencanaan / $totalAct) * $circ : 0;

$offsetBerjalan = 0;
$offsetSelesai = -$lenBerjalan;
$offsetPerencanaan = -($lenBerjalan + $lenSelesai);
?>

<!-- Banner Sambutan Eksekutif Direktur -->
<div style="background: linear-gradient(135deg, #ffffff 0%, #f4faeb 100%); border: 1px solid var(--border-color); border-left: 6px solid var(--primary-dark); border-radius: var(--radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px;">
                <i class="fa-solid fa-crown"></i> Direktur
            </span>
            <span style="font-size: 0.82rem; color: var(--text-muted); font-weight: 500;">Yayasan Natural Kapital Indonesia</span>
        </div>
        <h1 style="font-size: 1.4rem; font-weight: 800; color: #1e330c; margin: 6px 0 2px 0;">
            Selamat datang kembali, <?= htmlspecialchars($currentUser['name'] ?? 'Direktur') ?>!
        </h1>
        <p style="font-size: 0.88rem; color: #475569; margin: 0;">
            Ringkasan capaian proyek organisasi dan antrean pengesahan hasil MEL.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-stamp"></i> Verifikasi MEL
        </a>
        <a href="<?= BASE_URL ?>/evaluations" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-chart-line"></i> Ringkasan Capaian
        </a>
    </div>
</div>

<!-- 4 Kartu Metrik Direktur -->
<div class="grid-cols-4">
    <!-- Kartu 1: Proyek Aktif -->
    <div class="stat-card" style="border-left: 6px solid #2e4a16;">
        <div class="stat-icon" style="background-color: #f4faeb; color: #2e4a16;">
            <i class="fa-solid fa-briefcase"></i>
        </div>
        <div>
            <div class="stat-value"><?= number_format($activeProjectsCount, 0, ',', '.') ?></div>
            <div class="stat-label" style="font-weight: 600; color: #1e293b;">Proyek Aktif</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Status Aktif di Sistem</div>
        </div>
    </div>

    <!-- Kartu 2: Hasil MEL Menunggu Pengesahan -->
    <div class="stat-card" style="border-left: 6px solid #eab308;">
        <div class="stat-icon" style="background-color: #fefce8; color: #a16207;">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <div>
            <div class="stat-value" style="color: <?= $pendingMelCount > 0 ? '#a16207' : '#0f172a' ?>;">
                <?= number_format($pendingMelCount, 0, ',', '.') ?>
            </div>
            <div class="stat-label" style="font-weight: 600; color: #1e293b;">Hasil MEL Menunggu Pengesahan</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Butuh Keputusan Direksi</div>
        </div>
    </div>

    <!-- Kartu 3: Hasil MEL Disahkan -->
    <div class="stat-card" style="border-left: 6px solid #16a34a;">
        <div class="stat-icon" style="background-color: #f0fdf4; color: #15803d;">
            <i class="fa-solid fa-stamp"></i>
        </div>
        <div>
            <div class="stat-value" style="color: <?= $approvedMelCount > 0 ? '#15803d' : '#0f172a' ?>;">
                <?= number_format($approvedMelCount, 0, ',', '.') ?>
            </div>
            <div class="stat-label" style="font-weight: 600; color: #1e293b;">Hasil MEL Disahkan</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Telah Disetujui Direktur</div>
        </div>
    </div>

    <!-- Kartu 4: Hasil MEL Perlu Tindak Lanjut -->
    <div class="stat-card" style="border-left: 6px solid #dc2626;">
        <div class="stat-icon" style="background-color: #fef2f2; color: #b91c1c;">
            <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div>
            <div class="stat-value" style="color: <?= $needsFollowUpCount > 0 ? '#b91c1c' : '#0f172a' ?>;">
                <?= number_format($needsFollowUpCount, 0, ',', '.') ?>
            </div>
            <div class="stat-label" style="font-weight: 600; color: #1e293b;">Hasil MEL Perlu Tindak Lanjut</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Berkas Dikembalikan / Direvisi</div>
        </div>
    </div>
</div>

<!-- Layout 2 Kolom: Kolom Kiri Pengesahan MEL, Kolom Kanan Diagram Kegiatan -->
<div class="grid-cols-2">
    <!-- Kolom Kiri: Hasil MEL yang Membutuhkan Pengesahan (Menggantikan Proyek & Program Berjalan) -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clipboard-check" style="color: var(--primary-dark);"></i>
                    Hasil MEL yang Membutuhkan Pengesahan
                </h2>
                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 3px;">
                    Daftar dokumen laporan akhir MEL yang diajukan untuk ditinjau dan disahkan Direktur.
                </div>
            </div>
            <?php if (!empty($pendingMelList)): ?>
                <span class="badge badge-warning">
                    <?= count($pendingMelList) ?> Menunggu
                </span>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Kegiatan & Proyek</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingMelList)): ?>
                        <tr>
                            <td colspan="6" style="padding: 40px 20px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background-color: #f4faeb; color: var(--primary-dark); font-size: 1.5rem; margin-bottom: 12px;">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;">
                                    Tidak Ada Hasil MEL yang Menunggu Pengesahan
                                </div>
                                <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px; max-width: 380px; margin-left: auto; margin-right: auto;">
                                    Seluruh berkas Hasil MEL yang masuk telah ditinjau atau belum ada pengajuan baru dari Staf MEL.
                                </div>
                                <div style="margin-top: 14px;">
                                    <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-folder-open"></i> Buka Arsip Hasil MEL
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingMelList as $item): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #334155;">#<?= $item['mel_result_id'] ?></span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a; line-height: 1.3;">
                                        <?= htmlspecialchars($item['activity_name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                        <i class="fa-solid fa-folder" style="font-size: 0.7rem;"></i> <?= htmlspecialchars($item['project_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">
                                        <?= htmlspecialchars($item['submitter_name']) ?>
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">Staf MEL</div>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: #475569;">
                                        <?= date('d M Y', strtotime($item['submission_date'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-warning">
                                        <i class="fa-solid fa-hourglass-half"></i> Diajukan
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>/mel-results/detail?id=<?= $item['mel_result_id'] ?>" class="btn btn-primary btn-sm" style="white-space: nowrap;">
                                        <i class="fa-solid fa-signature"></i> Tinjau
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Kanan: Diagram Lingkaran Status Kegiatan Proyek (3 Kategori: Berjalan, Selesai, Perencanaan) -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-chart-pie" style="color: var(--primary-dark);"></i>
                    Status Kegiatan Proyek
                </h2>
                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 3px;">
                    Distribusi kegiatan berdasarkan status aktual pada tabel kegiatan.
                </div>
            </div>
            <span class="badge badge-info">
                Total: <?= $totalAct ?> Kegiatan
            </span>
        </div>

        <div style="padding: 10px 0;">
            <!-- Visual Donut Chart & Ringkasan Berdampingan -->
            <div style="display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; gap: 20px; padding: 10px 0;">
                
                <!-- SVG Donut Chart -->
                <div style="position: relative; width: 170px; height: 170px; display: flex; align-items: center; justify-content: center;">
                    <svg width="170" height="170" viewBox="0 0 170 170" style="transform: rotate(-90deg); border-radius: 50%;">
                        <!-- Track Background -->
                        <circle cx="85" cy="85" r="60" fill="none" stroke="#f1f5f9" stroke-width="22"></circle>
                        
                        <?php if ($totalAct > 0): ?>
                            <!-- Segmen 1: Berjalan (#2e4a16) -->
                            <?php if ($cntBerjalan > 0): ?>
                            <circle cx="85" cy="85" r="60" fill="none" stroke="#2e4a16" stroke-width="22"
                                    stroke-dasharray="<?= $lenBerjalan ?> <?= $circ ?>"
                                    stroke-dashoffset="<?= $offsetBerjalan ?>">
                                <title>Berjalan: <?= $cntBerjalan ?> (<?= $pctBerjalan ?>%)</title>
                            </circle>
                            <?php endif; ?>

                            <!-- Segmen 2: Selesai (#84cc16) -->
                            <?php if ($cntSelesai > 0): ?>
                            <circle cx="85" cy="85" r="60" fill="none" stroke="#84cc16" stroke-width="22"
                                    stroke-dasharray="<?= $lenSelesai ?> <?= $circ ?>"
                                    stroke-dashoffset="<?= $offsetSelesai ?>">
                                <title>Selesai: <?= $cntSelesai ?> (<?= $pctSelesai ?>%)</title>
                            </circle>
                            <?php endif; ?>

                            <!-- Segmen 3: Perencanaan (#f59e0b) -->
                            <?php if ($cntPerencanaan > 0): ?>
                            <circle cx="85" cy="85" r="60" fill="none" stroke="#f59e0b" stroke-width="22"
                                    stroke-dasharray="<?= $lenPerencanaan ?> <?= $circ ?>"
                                    stroke-dashoffset="<?= $offsetPerencanaan ?>">
                                <title>Perencanaan: <?= $cntPerencanaan ?> (<?= $pctPerencanaan ?>%)</title>
                            </circle>
                            <?php endif; ?>
                        <?php endif; ?>
                    </svg>

                    <!-- Center Label dalam Donut -->
                    <div style="position: absolute; text-align: center; pointer-events: none;">
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                            <?= $totalAct ?>
                        </div>
                        <div style="font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-top: 2px;">
                            Kegiatan
                        </div>
                    </div>
                </div>

                <!-- Legend Kategori & Rincian -->
                <div style="flex: 1; min-width: 190px; display: flex; flex-direction: column; gap: 10px;">
                    <!-- Berjalan -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: var(--radius-sm); border-left: 4px solid #2e4a16;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background-color: #2e4a16; display: inline-block;"></span>
                            <span style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">Berjalan</span>
                        </div>
                        <div style="text-align: right;">
                            <strong style="font-size: 0.95rem; color: #2e4a16;"><?= $cntBerjalan ?></strong>
                            <span style="font-size: 0.72rem; color: var(--text-muted); margin-left: 4px;">(<?= $pctBerjalan ?>%)</span>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: var(--radius-sm); border-left: 4px solid #84cc16;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background-color: #84cc16; display: inline-block;"></span>
                            <span style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">Selesai</span>
                        </div>
                        <div style="text-align: right;">
                            <strong style="font-size: 0.95rem; color: #4d7c0f;"><?= $cntSelesai ?></strong>
                            <span style="font-size: 0.72rem; color: var(--text-muted); margin-left: 4px;">(<?= $pctSelesai ?>%)</span>
                        </div>
                    </div>

                    <!-- Perencanaan -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8fafc; border-radius: var(--radius-sm); border-left: 4px solid #f59e0b;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background-color: #f59e0b; display: inline-block;"></span>
                            <span style="font-size: 0.85rem; font-weight: 600; color: #1e293b;">Perencanaan</span>
                        </div>
                        <div style="text-align: right;">
                            <strong style="font-size: 0.95rem; color: #b45309;"><?= $cntPerencanaan ?></strong>
                            <span style="font-size: 0.72rem; color: var(--text-muted); margin-left: 4px;">(<?= $pctPerencanaan ?>%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Metrik Dinamis -->
            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted);">
                <span><i class="fa-solid fa-circle-info"></i> Sumber: status pada tabel kegiatan</span>
                <span>Terakhir diperbarui: <?= date('d M Y, H:i') ?></span>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<?php if ($currentUser['role_id'] == 4): ?>
    <?php
    $totalEval = count($evaluations);
    $sesuaiCount = 0;
    $tidakSesuaiCount = 0;
    $approvedMelCount = 0;
    foreach ($evaluations as $item) {
        if (($item['result'] ?? '') === 'Sesuai') $sesuaiCount++;
        if (($item['result'] ?? '') === 'Tidak Sesuai') $tidakSesuaiCount++;
        if (!empty($item['approval_decision']) && $item['approval_decision'] === 'Disetujui') $approvedMelCount++;
    }
    ?>

    <!-- Stat Ringkasan Pemantauan Direktur -->
    <div class="grid-cols-4" style="margin-bottom: 24px;">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #f1f5f9; color: #334155;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <div class="stat-value"><?= $totalEval ?></div>
                <div class="stat-label">Total Dievaluasi PL</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--primary-light); color: #2e4a16;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #2e4a16;"><?= $sesuaiCount ?></div>
                <div class="stat-label">Capaian Sesuai Target</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--danger-light); color: var(--danger);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="stat-value" style="color: var(--danger);"><?= $tidakSesuaiCount ?></div>
                <div class="stat-label">Perlu Perbaikan / Tidak Sesuai</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning-light); color: #92400e;">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <div class="stat-value" style="color: #92400e;"><?= $approvedMelCount ?></div>
                <div class="stat-label">Hasil MEL Telah Disahkan</div>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Capaian Direktur -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title"><i class="fa-solid fa-chart-line" style="color: var(--primary-dark);"></i> Ringkasan Capaian Indikator & Proyek</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Pemantauan capaian realisasi kegiatan, hasil evaluasi Project Leader, dan status pengesahan MEL.</p>
            </div>
            <div>
                <span class="badge badge-info"><i class="fa-solid fa-eye"></i> Mode Pemantauan Direktur</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kegiatan & Proyek</th>
                        <th>Indikator & Target</th>
                        <th>Realisasi Lapangan</th>
                        <th>% Capaian</th>
                        <th>Evaluator & Tanggal</th>
                        <th>Hasil Evaluasi (PL)</th>
                        <th>Status Laporan & MEL</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($evaluations)): ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada data evaluasi capaian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($evaluations as $e): 
                            $target = (float)($e['target_value'] ?? 0);
                            $realization = (float)($e['realization_value'] ?? 0);
                            $pct = ($target > 0) ? round(($realization / $target) * 100, 1) : null;
                            $progressFisik = (float)($e['progress_percentage'] ?? 0);
                        ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; max-width: 220px;"><?= htmlspecialchars($e['activity_name']) ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($e['project_name']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.88rem;"><?= htmlspecialchars($e['indicator_name']) ?></div>
                                    <div style="font-size: 0.8rem; color: #334155; margin-top: 2px;">
                                        Target: <strong><?= number_format($target, 0, ',', '.') ?></strong> <?= htmlspecialchars($e['unit'] ?? '') ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 0.95rem; color: #1e330c;">
                                        <?= number_format($realization, 0, ',', '.') ?> <?= htmlspecialchars($e['unit'] ?? '') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($pct !== null): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="flex: 1; min-width: 55px; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                                                <div style="width: <?= min(100, $pct) ?>%; height: 100%; background: <?= $pct >= 100 ? '#10b981' : ($pct >= 70 ? 'var(--primary-dark)' : 'var(--warning)') ?>; border-radius: 9999px;"></div>
                                            </div>
                                            <span style="font-size: 0.82rem; font-weight: 700; color: #0f172a;"><?= $pct ?>%</span>
                                        </div>
                                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Fisik: <?= $progressFisik ?>%</div>
                                    <?php else: ?>
                                        <span style="font-size: 0.82rem; color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem;"><?= htmlspecialchars($e['evaluator_name']) ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?= date('d M Y', strtotime($e['evaluation_date'])) ?></div>
                                </td>
                                <td>
                                    <?php if ($e['result'] === 'Sesuai'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> Sesuai</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Tidak Sesuai</span>
                                    <?php endif; ?>
                                    <?php if (!empty($e['notes'])): ?>
                                        <div style="font-size: 0.78rem; color: #475569; margin-top: 4px; max-width: 200px; line-height: 1.3;" title="<?= htmlspecialchars($e['notes']) ?>">
                                            <?= htmlspecialchars(mb_strimwidth($e['notes'], 0, 60, '...')) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div>
                                            <span class="badge <?= $e['report_status'] === 'Dievaluasi' ? 'badge-success' : ($e['report_status'] === 'Perlu Perbaikan' ? 'badge-danger' : 'badge-warning') ?>" style="font-size: 0.72rem;">
                                                Laporan: <?= htmlspecialchars($e['report_status'] ?? 'Diajukan') ?>
                                            </span>
                                        </div>
                                        <div>
                                            <?php if ($e['approval_decision'] === 'Disetujui'): ?>
                                                <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fa-solid fa-stamp"></i> MEL Disetujui</span>
                                            <?php elseif ($e['approval_decision'] === 'Ditolak'): ?>
                                                <span class="badge badge-danger" style="font-size: 0.72rem;"><i class="fa-solid fa-rotate-left"></i> MEL Dikembalikan</span>
                                            <?php elseif (!empty($e['mel_result_id'])): ?>
                                                <span class="badge badge-warning" style="font-size: 0.72rem;"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi MEL</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary" style="font-size: 0.72rem; color: #64748b; background: #f1f5f9;">Belum Disusun MEL</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $e['update_id'] ?>" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                                            <i class="fa-solid fa-eye"></i> Detail Laporan
                                        </a>
                                        <?php if (!empty($e['mel_result_id'])): ?>
                                            <a href="<?= BASE_URL ?>/mel-results/detail?id=<?= $e['mel_result_id'] ?>" class="btn btn-primary btn-sm" style="white-space: nowrap;">
                                                <i class="fa-solid fa-stamp"></i> Verifikasi MEL
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <!-- Halaman Evaluasi Kinerja untuk Role Project Leader & Staf MEL -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-scale-balanced" style="color: var(--primary-dark);"></i> Rekapitulasi Evaluasi Capaian</h2>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Eval</th>
                        <th>Kegiatan & Proyek</th>
                        <th>Indikator Target</th>
                        <th>Evaluator (PL)</th>
                        <th>Tanggal Evaluasi</th>
                        <th>Kesesuaian</th>
                        <th>Catatan Evaluasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($evaluations)): ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada data evaluasi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($evaluations as $e): ?>
                            <tr>
                                <td><strong>#<?= $e['evaluation_id'] ?></strong></td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; max-width: 220px;"><?= htmlspecialchars($e['activity_name']) ?></div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($e['project_name']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($e['indicator_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($e['unit']) ?></div>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($e['evaluator_name']) ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;"><?= date('d M Y', strtotime($e['evaluation_date'])) ?></div>
                                </td>
                                <td>
                                    <?php if ($e['result'] === 'Sesuai'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> Sesuai</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Tidak Sesuai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 260px;"><?= htmlspecialchars($e['notes']) ?></div>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $e['update_id'] ?>" class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-eye"></i> Laporan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

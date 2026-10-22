<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<?php if ($currentUser['role_id'] == 4): ?>
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
<?php elseif ($currentUser['role_id'] == 2): ?>
    <!-- Halaman Evaluasi & Tindak Lanjut untuk Role Staf MEL (2 Tab) -->
    <div style="display: flex; gap: 8px; margin-bottom: 18px; border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">
        <a href="<?= BASE_URL ?>/evaluations?tab=evaluasi" class="btn <?= ($activeTab ?? 'evaluasi') === 'evaluasi' ? 'btn-primary' : 'btn-secondary' ?> btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-scale-balanced"></i>
            <span>Hasil Evaluasi PL</span>
            <span class="badge" style="background: <?= ($activeTab ?? 'evaluasi') === 'evaluasi' ? 'rgba(255,255,255,0.25)' : '#e2e8f0' ?>; color: <?= ($activeTab ?? 'evaluasi') === 'evaluasi' ? '#ffffff' : '#475569' ?>; padding: 2px 8px; font-size: 0.75rem;">
                <?= count($evaluations ?? []) ?>
            </span>
        </a>
        <a href="<?= BASE_URL ?>/evaluations?tab=perbaikan" class="btn <?= ($activeTab ?? 'evaluasi') === 'perbaikan' ? 'btn-primary' : 'btn-secondary' ?> btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-rotate-left"></i>
            <span>Permintaan Perbaikan</span>
            <span class="badge" style="background: <?= ($activeTab ?? 'evaluasi') === 'perbaikan' ? 'rgba(255,255,255,0.25)' : '#e2e8f0' ?>; color: <?= ($activeTab ?? 'evaluasi') === 'perbaikan' ? '#ffffff' : '#475569' ?>; padding: 2px 8px; font-size: 0.75rem;">
                <?= count($corrections ?? []) ?>
            </span>
        </a>
    </div>

    <?php if (($activeTab ?? 'evaluasi') === 'perbaikan'): ?>
        <!-- Tab 2: Permintaan Perbaikan untuk Staf MEL -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title"><i class="fa-solid fa-rotate-left" style="color: var(--primary-dark);"></i> Permintaan Perbaikan & Tindak Lanjut</h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Pemantauan permintaan klarifikasi/revisi dari Project Leader dan status respons Staf Lapangan.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kegiatan & Proyek</th>
                            <th>Pemohon (PL)</th>
                            <th>Ditugaskan ke (Staf)</th>
                            <th>Tanggal Permintaan</th>
                            <th>Uraian Catatan Perbaikan</th>
                            <th>Status Tindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($corrections)): ?>
                            <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada permintaan perbaikan aktif.</td></tr>
                        <?php else: ?>
                            <?php foreach ($corrections as $c): ?>
                                <tr>
                                    <td><strong>#<?= $c['correction_id'] ?></strong></td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; max-width: 200px;"><?= htmlspecialchars($c['activity_name']) ?></div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($c['project_name']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($c['requested_by_name']) ?></td>
                                    <td><?= htmlspecialchars($c['assigned_to_name']) ?></td>
                                    <td><?= date('d M Y', strtotime($c['request_date'])) ?></td>
                                    <td>
                                        <div style="font-size: 0.85rem; max-width: 250px;"><?= htmlspecialchars($c['description']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($c['status'] === 'Direspons'): ?>
                                            <span class="badge badge-success"><i class="fa-solid fa-reply"></i> Direspons</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Diminta</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/corrections/detail?id=<?= $c['correction_id'] ?>" class="btn btn-secondary btn-sm">
                                            <i class="fa-solid fa-comments"></i> Rincian & Respons
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Tab 1: Hasil Evaluasi PL untuk Staf MEL -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title"><i class="fa-solid fa-scale-balanced" style="color: var(--primary-dark);"></i> Hasil Evaluasi Kinerja (Project Leader)</h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Daftar penilaian kesesuaian target indikator yang dilakukan oleh Project Leader sebagai dasar penyusunan hasil MEL.</p>
                </div>
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
<?php else: ?>
    <!-- Halaman Evaluasi Kinerja untuk Role Project Leader -->
    <!-- 1. Fitur Pencarian & Pemantauan Laporan Lapangan Masuk -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
            <div>
                <h2 class="card-title"><i class="fa-solid fa-magnifying-glass-chart" style="color: var(--primary-dark);"></i> Pencarian & Pemantauan Laporan Lapangan</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Cari, temukan, dan evaluasi laporan lapangan yang telah dikirim oleh pelapor untuk proyek di bawah kewenangan Anda.</p>
            </div>
            
            <!-- Form Pencarian Laporan Lapangan -->
            <form action="<?= BASE_URL ?>/evaluations" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari nama pelapor, proyek, atau kegiatan..." class="form-control" style="padding-left: 32px; width: 320px; height: 38px;">
                </div>

                <select name="status" class="form-control" style="width: 170px; height: 38px;" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="Diajukan" <?= (isset($_GET['status']) && $_GET['status'] === 'Diajukan') ? 'selected' : '' ?>>Diajukan (Perlu Evaluasi)</option>
                    <option value="Dievaluasi" <?= (isset($_GET['status']) && $_GET['status'] === 'Dievaluasi') ? 'selected' : '' ?>>Dievaluasi / Sesuai</option>
                    <option value="Perlu Perbaikan" <?= (isset($_GET['status']) && $_GET['status'] === 'Perlu Perbaikan') ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    <option value="Draft" <?= (isset($_GET['status']) && $_GET['status'] === 'Draft') ? 'selected' : '' ?>>Draft</option>
                </select>

                <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;" title="Cari Laporan">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>

                <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
                    <a href="<?= BASE_URL ?>/evaluations" class="btn btn-secondary btn-sm" style="height: 38px; display: inline-flex; align-items: center;" title="Reset Pencarian">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
            <div style="padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: #475569;">
                Hasil pencarian untuk: 
                <?php if (!empty($_GET['search'])): ?><strong>"<?= htmlspecialchars($_GET['search']) ?>"</strong><?php endif; ?>
                <?php if (!empty($_GET['status'])): ?><span class="badge badge-info" style="margin-left: 6px;">Status: <?= htmlspecialchars($_GET['status']) ?></span><?php endif; ?>
                &bull; Ditemukan <strong><?= count($fieldUpdates ?? []) ?></strong> laporan.
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Lapor</th>
                        <th>Pelapor</th>
                        <th>Proyek & Kode</th>
                        <th>Nama Kegiatan</th>
                        <th>Realisasi & Progres</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fieldUpdates)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 35px 20px;">
                                <i class="fa-solid fa-folder-open" style="font-size: 2rem; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                                Tidak ditemukan laporan lapangan yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fieldUpdates as $fu): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a;">
                                        <?= date('d M Y', strtotime($fu['update_date'])) ?>
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                                        #<?= $fu['update_id'] ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #1e330c;"><?= htmlspecialchars($fu['reporter_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($fu['reporter_email'] ?? '') ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem; color: #0f172a; max-width: 200px;"><?= htmlspecialchars($fu['project_name']) ?></div>
                                    <span style="font-family: monospace; font-size: 0.75rem; color: var(--text-muted); font-weight: 600;"><?= htmlspecialchars($fu['project_code']) ?></span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; font-weight: 600; max-width: 240px; color: #334155;">
                                        <?= htmlspecialchars($fu['activity_name']) ?>
                                    </div>
                                    <?php if (!empty($fu['description'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
                                            <?= htmlspecialchars($fu['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong style="color: #1e330c; font-size: 0.9rem;"><?= number_format((float)($fu['realization_value'] ?? 0), 0, ',', '.') ?></strong>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        Progres: <strong><?= (float)($fu['progress_percentage'] ?? 0) ?>%</strong>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($fu['status'] === 'Dievaluasi'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> Dievaluasi</span>
                                    <?php elseif ($fu['status'] === 'Perlu Perbaikan'): ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Perlu Perbaikan</span>
                                    <?php elseif ($fu['status'] === 'Draft'): ?>
                                        <span class="badge" style="background: #f1f5f9; color: #475569;"><i class="fa-solid fa-pen-ruler"></i> Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Diajukan</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $fu['update_id'] ?>" class="btn <?= $fu['status'] === 'Diajukan' ? 'btn-primary' : 'btn-secondary' ?> btn-sm" style="white-space: nowrap;">
                                            <i class="fa-solid <?= $fu['status'] === 'Diajukan' ? 'fa-scale-balanced' : 'fa-eye' ?>"></i>
                                            <?= $fu['status'] === 'Diajukan' ? 'Evaluasi' : 'Detail' ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Rekapitulasi Evaluasi Capaian yang Pernah Diberikan -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title"><i class="fa-solid fa-scale-balanced" style="color: var(--primary-dark);"></i> Riwayat Evaluasi Kinerja (Project Leader)</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Daftar penilaian kesesuaian target indikator yang telah selesai dievaluasi.</p>
            </div>
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

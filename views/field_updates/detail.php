<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Laporan
    </a>
    <div>
        <?php if ($update['status'] === 'Dievaluasi'): ?>
            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Sudah Dievaluasi</span>
        <?php elseif ($update['status'] === 'Perlu Perbaikan'): ?>
            <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Menunggu Perbaikan Staf</span>
        <?php else: ?>
            <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($update['status']) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Report Details -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-file-invoice" style="color: var(--primary-dark);"></i> Laporan Lapangan #<?= $update['update_id'] ?></h2>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="font-size: 0.8rem; color: var(--text-muted);">Proyek & Program</div>
            <div style="font-weight: 700; font-size: 1rem; color: #0f172a;"><?= htmlspecialchars($update['project_name']) ?></div>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="font-size: 0.8rem; color: var(--text-muted);">Nama Kegiatan</div>
            <div style="font-weight: 700; font-size: 1.05rem; color: #1e330c;"><?= htmlspecialchars($update['activity_name']) ?></div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Pelapor Lapangan</div>
                <div style="font-weight: 600;"><?= htmlspecialchars($update['reporter_name']) ?></div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Lapor</div>
                <div style="font-weight: 600;"><?= date('d F Y', strtotime($update['update_date'])) ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; background: #f8fafc; padding: 14px; border-radius: var(--radius-sm);">
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Realisasi Angka</div>
                <div style="font-weight: 800; font-size: 1.25rem; color: #2e4a16;">
                    <?= number_format($update['realization_value'], 0, ',', '.') ?>
                </div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Progres Fisik / Lapangan</div>
                <div style="font-weight: 800; font-size: 1.25rem; color: #0f172a;">
                    <?= $update['progress_percentage'] ?? 0 ?>%
                </div>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Deskripsi Hasil Pekerjaan:</div>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; padding: 12px; font-size: 0.9rem;">
                <?= nl2br(htmlspecialchars($update['description'] ?? '')) ?>
            </div>
        </div>

        <?php if (!empty($update['evidence_file'])): ?>
            <div style="margin-top: 16px;">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Berkas Bukti / Dokumentasi:</div>
                <a href="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($update['evidence_file']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-file-arrow-down"></i> Unduh File Bukti (<?= htmlspecialchars($update['evidence_file']) ?>)
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Evaluation Section & Loop -->
    <div>
        <!-- Evaluasi Form for Project Leader (PRD 3.3 & 4.5) -->
        <?php if ($currentUser['role_id'] == 3): // Only Project Leader ?>
        <div class="card" style="border: 2px solid var(--primary);">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-scale-balanced" style="color: var(--primary-dark);"></i> Formulir Evaluasi (Project Leader)</h2>
            </div>

            <form action="<?= BASE_URL ?>/evaluations/submit" method="POST">
                <input type="hidden" name="update_id" value="<?= $update['update_id'] ?>">
                
                <div class="form-group">
                    <label class="form-label">Bandingkan dengan Indikator Kinerja *</label>
                    <select name="indicator_id" class="form-control" required>
                        <?php foreach ($indicators as $ind): ?>
                            <option value="<?= $ind['indicator_id'] ?>">
                                <?= htmlspecialchars($ind['indicator_name']) ?> (Target: <?= number_format($ind['target_value'], 0, ',', '.') ?> <?= htmlspecialchars($ind['unit']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Hasil Kesesuaian Kinerja *</label>
                    <select name="result" class="form-control" required>
                        <option value="Sesuai">Sesuai (Target tercapai & data valid)</option>
                        <option value="Tidak Sesuai">Tidak Sesuai (Picu Permintaan Perbaikan / Klarifikasi)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Evaluasi / Instruksi Perbaikan *</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Berikan catatan rinci mengenai capaian atau bagian yang wajib diperbaiki oleh Staf Lapangan" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-check-double"></i> Simpan & Kirim Evaluasi
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- History of Evaluations for this report -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-timeline"></i> Riwayat Evaluasi Laporan Ini</h2>
                <span class="badge badge-info"><?= count($evaluations) ?> Evaluasi</span>
            </div>

            <?php if (empty($evaluations)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada hasil evaluasi untuk laporan ini.</p>
            <?php else: ?>
                <?php foreach ($evaluations as $ev): ?>
                    <div style="border-left: 4px solid <?= $ev['result'] === 'Sesuai' ? 'var(--primary)' : 'var(--danger)' ?>; padding: 14px 16px; background: #f8fafc; border-radius: 4px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="badge <?= $ev['result'] === 'Sesuai' ? 'badge-success' : 'badge-danger' ?>">
                                    <?= htmlspecialchars($ev['result']) ?>
                                </span>
                                <span style="font-size: 0.82rem; color: var(--text-muted); margin-left: 6px;">
                                    oleh <strong><?= htmlspecialchars($ev['evaluator_name']) ?></strong> (<?= date('d M Y', strtotime($ev['evaluation_date'])) ?>)
                                </span>
                            </div>
                        </div>
                        <div style="font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-top: 6px;">
                            Indikator: <?= htmlspecialchars($ev['indicator_name']) ?> (Target: <?= number_format($ev['target_value'], 0, ',', '.') ?> <?= htmlspecialchars($ev['unit']) ?>)
                        </div>
                        <div style="font-size: 0.88rem; color: #475569; margin-top: 4px;">
                            <?= nl2br(htmlspecialchars($ev['notes'] ?? '')) ?>
                        </div>
                        <?php if ($ev['correction_count'] > 0): ?>
                            <div style="margin-top: 8px;">
                                <a href="<?= BASE_URL ?>/corrections" class="badge badge-warning">
                                    <i class="fa-solid fa-rotate-left"></i> Ada perbaikan terkait (Lihat Permintaan)
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

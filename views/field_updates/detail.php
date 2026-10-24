<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <?php if ($currentUser['role_id'] == 4): ?>
        <a href="<?= BASE_URL ?>/evaluations" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Ringkasan Capaian
        </a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Laporan
        </a>
    <?php endif; ?>
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

<?php if ((int)$currentUser['role_id'] === 4): ?>
    <!-- Layout Vertikal Full-Width Khusus Direktur -->
    <div style="display: flex; flex-direction: column; gap: 24px; width: 100%;">
        <!-- Card 1: Laporan Lapangan -->
        <div class="card" style="width: 100%;">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-file-invoice" style="color: var(--primary-dark);"></i> Laporan Lapangan #<?= $update['update_id'] ?></h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Proyek & Program</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #0f172a;"><?= htmlspecialchars($update['project_name']) ?></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Nama Kegiatan</div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #1e330c;"><?= htmlspecialchars($update['activity_name']) ?></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Pelapor Lapangan</div>
                    <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($update['reporter_name']) ?></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Lapor</div>
                    <div style="font-weight: 600; font-size: 0.95rem;"><?= date('d F Y', strtotime($update['update_date'])) ?></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; background: #f8fafc; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Realisasi Angka</div>
                    <div style="font-weight: 800; font-size: 1.35rem; color: #2e4a16;">
                        <?= number_format($update['realization_value'], 0, ',', '.') ?>
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Progres Fisik / Lapangan</div>
                    <div style="font-weight: 800; font-size: 1.35rem; color: #0f172a;">
                        <?= $update['progress_percentage'] ?? 0 ?>%
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.82rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">Deskripsi Hasil Pekerjaan:</div>
                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; padding: 14px 16px; font-size: 0.92rem; line-height: 1.6; color: #334155;">
                    <?= nl2br(htmlspecialchars($update['description'] ?? '')) ?>
                </div>
            </div>

            <?php if (!empty($update['evidence_file'])): ?>
                <div style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px;">Berkas Bukti / Dokumentasi:</div>
                    <a href="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($update['evidence_file']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-arrow-down"></i> Unduh File Bukti (<?= htmlspecialchars($update['evidence_file']) ?>)
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Card 2: Riwayat Evaluasi Laporan Ini -->
        <div class="card" style="width: 100%;">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-timeline"></i> Riwayat Evaluasi Laporan Ini</h2>
                <span class="badge badge-info"><?= count($evaluations) ?> Evaluasi</span>
            </div>

            <?php if (empty($evaluations)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada hasil evaluasi untuk laporan ini.</p>
            <?php else: ?>
                <?php foreach ($evaluations as $ev): ?>
                    <div style="border-left: 4px solid <?= $ev['result'] === 'Sesuai' ? 'var(--primary)' : 'var(--danger)' ?>; padding: 16px 18px; background: #f8fafc; border-radius: 6px; margin-bottom: 14px; border: 1px solid #e2e8f0; border-left-width: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <span class="badge <?= $ev['result'] === 'Sesuai' ? 'badge-success' : 'badge-danger' ?>">
                                    <?= htmlspecialchars($ev['result']) ?>
                                </span>
                                <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: 8px;">
                                    oleh <strong><?= htmlspecialchars($ev['evaluator_name']) ?></strong> (<?= date('d M Y', strtotime($ev['evaluation_date'])) ?>)
                                </span>
                            </div>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: #1e293b; margin-top: 8px;">
                            Indikator: <?= htmlspecialchars($ev['indicator_name']) ?> (Target: <?= number_format($ev['target_value'], 0, ',', '.') ?> <?= htmlspecialchars($ev['unit']) ?>)
                        </div>
                        <div style="font-size: 0.9rem; color: #475569; margin-top: 6px; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($ev['notes'] ?? '')) ?>
                        </div>
                        <?php if ($ev['correction_count'] > 0): ?>
                            <div style="margin-top: 12px;">
                                <?php
                                require_once __DIR__ . '/../../models/CorrectionRequest.php';
                                $corrModel = new CorrectionRequest();
                                $corrections = $corrModel->getByEvaluationId((int)$ev['evaluation_id']);
                                ?>
                                <div style="padding: 12px 14px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: var(--radius-sm);">
                                    <div style="font-size: 0.82rem; font-weight: 700; color: #92400e; margin-bottom: 8px;">
                                        <i class="fa-solid fa-rotate-left"></i> Riwayat Permintaan Perbaikan (<?= count($corrections) ?>)
                                    </div>
                                    <?php foreach ($corrections as $cr): ?>
                                        <div style="font-size: 0.85rem; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #fde68a;">
                                            <div><strong>Catatan Perbaikan:</strong> <?= htmlspecialchars($cr['description']) ?></div>
                                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 3px;">
                                                Diminta oleh <strong><?= htmlspecialchars($cr['requested_by_name']) ?></strong> kepada <strong><?= htmlspecialchars($cr['assigned_to_name']) ?></strong> (<?= date('d M Y', strtotime($cr['request_date'])) ?>) &bull; Status: <span class="badge <?= $cr['status'] === 'Selesai' ? 'badge-success' : 'badge-warning' ?>" style="font-size: 0.72rem;"><?= htmlspecialchars($cr['status']) ?></span>
                                            </div>
                                            <?php if (!empty($cr['response'])): ?>
                                                <div style="margin-top: 4px; color: #1e3a8a; font-size: 0.82rem;">
                                                    <strong>Tanggapan Staf Lapangan (<?= !empty($cr['response_date']) ? date('d M Y', strtotime($cr['response_date'])) : '' ?>):</strong> <?= htmlspecialchars($cr['response']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Card 3: Catatan & Arahan Direktur -->
        <div class="card" style="border: 2px solid #2e4a16; width: 100%; background: #ffffff;">
            <div class="card-header" style="background: linear-gradient(135deg, #f0fdf4, #e8f5e9); border-bottom: 1px solid #bbf7d0;">
                <div>
                    <h2 class="card-title" style="color: #1e330c; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-note-sticky" style="color: #2e4a16;"></i> Catatan & Arahan Direktur
                    </h2>
                    <p style="font-size: 0.8rem; color: #475569; margin-top: 3px; margin-bottom: 0;">
                        Opsional: Berikan catatan jika terdapat hal yang kurang tepat atau memerlukan atensi tim pelaksana.
                    </p>
                </div>
                <span class="badge badge-success" style="font-size: 0.72rem;"><i class="fa-solid fa-crown"></i> Direksi</span>
            </div>
            <div style="padding: 16px;">
                <form action="<?= BASE_URL ?>/field-updates/save-director-note" method="POST">
                    <input type="hidden" name="update_id" value="<?= (int)$update['update_id'] ?>">
                    
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label" style="font-size: 0.82rem; font-weight: 600; color: #1e293b;">
                            Isi Catatan / Catatan Perhatian (Opsional):
                        </label>
                        <textarea name="director_notes" class="form-control" rows="4" 
                                  placeholder="Tuliskan catatan, arahan tindak lanjut, atau hal yang perlu dicermati oleh Project Leader dan Staf Lapangan..."><?= htmlspecialchars($update['director_notes'] ?? '') ?></textarea>
                        <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 4px;">
                            <i class="fa-solid fa-circle-info"></i> Catatan ini akan otomatis terintegrasi dan ternotifikasi ke Staf Lapangan, Project Leader, dan Staf MEL.
                        </small>
                    </div>

                    <?php if (!empty($update['director_notes_updated_at'])): ?>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-regular fa-clock"></i> Terakhir disimpan: <strong><?= date('d M Y, H:i', strtotime($update['director_notes_updated_at'])) ?> WIB</strong>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                        <?php if (!empty($update['director_notes'])): ?>
                            <button type="submit" name="action" value="clear" onclick="if(confirm('Hapus catatan Direktur untuk laporan ini?')){ this.form.querySelector('textarea').value=''; return true; } return false;" class="btn btn-secondary btn-sm" style="color: #dc2626; border-color: #fca5a5;">
                                <i class="fa-solid fa-trash"></i> Hapus Catatan
                            </button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Layout Default (2 Kolom) untuk Aktor Non-Direktur -->
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
                                    <?= htmlspecialchars($ind['indicator_name']) ?> 
                                    <?php if (($ind['activity_type'] ?? 'Kuantitatif') === 'Kualitatif'): ?>
                                        [Kualitatif: <?= htmlspecialchars(mb_strimwidth($ind['target_description'] ?: ($ind['description'] ?: 'Deskriptif'), 0, 45, '...')) ?>]
                                    <?php else: ?>
                                        (Target: <?= number_format($ind['target_value'] ?? 0, 0, ',', '.') ?> <?= htmlspecialchars($ind['unit'] ?? '') ?>)
                                    <?php endif; ?>
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

            <?php if (!empty($update['director_notes'])): ?>
            <!-- Tampilan Catatan Direktur untuk Staf Lapangan, Project Leader, dan Staf MEL -->
            <div class="card" style="border-left: 5px solid #2e4a16; background: #f0fdf4; margin-bottom: 20px; border-radius: var(--radius-sm); border-top: 1px solid #bbf7d0; border-right: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0;">
                <div style="padding: 14px 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="badge badge-success" style="font-size: 0.72rem; padding: 3px 8px;">
                                <i class="fa-solid fa-crown"></i> Arahan Direktur
                            </span>
                            <span style="font-weight: 700; font-size: 0.9rem; color: #1e330c;">
                                Catatan Atensi Direksi
                            </span>
                        </div>
                        <?php if (!empty($update['director_notes_updated_at'])): ?>
                            <span style="font-size: 0.74rem; color: var(--text-muted);">
                                <?= date('d M Y, H:i', strtotime($update['director_notes_updated_at'])) ?> WIB
                            </span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 0.88rem; color: #1e293b; line-height: 1.5; background: #ffffff; padding: 10px 14px; border-radius: 6px; border: 1px solid #dcfce7;">
                        <?= nl2br(htmlspecialchars($update['director_notes'])) ?>
                    </div>
                    <div style="font-size: 0.74rem; color: #4b7a1e; margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Harap perhatikan arahan ini dalam proses verifikasi dan tindak lanjut laporan.
                    </div>
                </div>
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
                                <div style="margin-top: 10px;">
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
<?php endif; ?>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

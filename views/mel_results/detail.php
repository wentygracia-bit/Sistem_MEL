<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Hasil MEL
    </a>
    <div>
        <?php if ($result['status'] === 'Disetujui'): ?>
            <span class="badge badge-success"><i class="fa-solid fa-stamp"></i> Resmi Disahkan Direktur</span>
        <?php elseif ($result['status'] === 'Ditolak'): ?>
            <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Keputusan: Ditolak</span>
        <?php else: ?>
            <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Menunggu Keputusan Direktur</span>
        <?php endif; ?>
    </div>
</div>

<div class="grid-cols-2">
    <!-- Dokumen Hasil MEL -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-file-contract" style="color: var(--primary-dark);"></i> Dokumen Hasil MEL #<?= $result['mel_result_id'] ?></h2>
        </div>

        <div style="margin-bottom: 14px;">
            <div style="font-size: 0.8rem; color: var(--text-muted);">Program / Proyek</div>
            <div style="font-weight: 700; font-size: 1.1rem; color: #0f172a;"><?= htmlspecialchars($result['project_name']) ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace;"><?= htmlspecialchars($result['project_code']) ?></div>
        </div>

        <div style="margin-bottom: 14px;">
            <div style="font-size: 0.8rem; color: var(--text-muted);">Nama Kegiatan</div>
            <div style="font-weight: 700; font-size: 1.05rem; color: #1e330c;"><?= htmlspecialchars($result['activity_name']) ?></div>
            <?php if ($result['location']): ?>
                <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($result['location']) ?></div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Penyusun Dokumen (Staf MEL)</div>
                <div style="font-weight: 600;"><?= htmlspecialchars($result['submitter_name']) ?></div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Tanggal Diajukan</div>
                <div style="font-weight: 600;"><?= date('d F Y', strtotime($result['submission_date'])) ?></div>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-sm); padding: 14px; margin-bottom: 16px;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Ringkasan Hasil Evaluasi Capaian:</div>
            <div style="font-size: 0.95rem; color: #1e293b; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($result['result_summary'])) ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 6px; padding: 12px; margin-bottom: 16px;">
            <div>
                <div style="font-size: 0.78rem; color: var(--text-muted);">Target KPI Indikator:</div>
                <div style="font-weight: 700;"><?= number_format($result['target_value'] ?? 0, 0, ',', '.') ?> <?= htmlspecialchars($result['unit'] ?? '') ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($result['indicator_name'] ?? '') ?></div>
            </div>
            <div>
                <div style="font-size: 0.78rem; color: var(--text-muted);">Realisasi Lapangan:</div>
                <div style="font-weight: 700; color: #2e4a16; font-size: 1.1rem;"><?= number_format($result['realization_value'] ?? 0, 0, ',', '.') ?> <?= htmlspecialchars($result['unit'] ?? '') ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Progres Fisik: <?= $result['progress_percentage'] ?? 0 ?>%</div>
            </div>
        </div>

        <?php if (!empty($result['evidence_file'])): ?>
            <div>
                <a href="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($result['evidence_file']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-paperclip"></i> Lihat Bukti Terlampir Lapangan
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Director Approval Section (PRD 3.4, 4.6 & 5.5) -->
    <div>
        <div class="card" style="border: 2px solid <?= $result['status'] === 'Disetujui' ? 'var(--primary)' : 'var(--border-color)' ?>;">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-signature" style="color: var(--primary-dark);"></i> Pengesahan & Keputusan Direksi</h2>
            </div>

            <?php if (!empty($result['approval_decision'])): ?>
                <!-- Sudah ada keputusan -->
                <div style="background: <?= $result['approval_decision'] === 'Disetujui' ? '#f4faeb' : '#fff1f2' ?>; border: 1px solid <?= $result['approval_decision'] === 'Disetujui' ? '#b5ce88' : '#fecdd3' ?>; border-radius: var(--radius-sm); padding: 18px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="badge <?= $result['approval_decision'] === 'Disetujui' ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.9rem; padding: 6px 14px;">
                            <i class="fa-solid fa-stamp"></i> Keputusan: <?= htmlspecialchars($result['approval_decision']) ?>
                        </span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                            <?= date('d F Y', strtotime($result['approval_date'])) ?>
                        </span>
                    </div>

                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">Disahkan oleh:</div>
                    <div style="font-weight: 700; font-size: 1rem; color: #0f172a; margin-bottom: 12px;">
                        <?= htmlspecialchars($result['approver_name']) ?> (Direktur)
                    </div>

                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">Catatan / Arahan Direktur:</div>
                    <div style="font-size: 0.95rem; color: #1e293b; background: #ffffff; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <?= nl2br(htmlspecialchars($result['approval_notes'] ?? 'Tidak ada catatan tambahan.')) ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="padding: 16px; background: #fefce8; border: 1px solid #fef08a; border-radius: var(--radius-sm); margin-bottom: 20px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ca8a04;"></i>
                    <span style="font-size: 0.88rem; color: #854d0e; font-weight: 500;">
                        Dokumen ini sedang menunggu keputusan dan tanda tangan pengesahan dari Direktur.
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($currentUser['role_id'] == 4): // Form khusus Direktur ?>
                <div style="margin-top: 20px; padding-top: 18px; border-top: 1px dashed var(--border-color);">
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 12px;">Tindakan Pengesahan (Khusus Direktur):</h3>
                    
                    <form action="<?= BASE_URL ?>/mel-results/approve" method="POST">
                        <input type="hidden" name="mel_result_id" value="<?= $result['mel_result_id'] ?>">
                        
                        <div class="form-group">
                            <label class="form-label">Keputusan Direktur *</label>
                            <select name="decision" class="form-control" required>
                                <option value="Disetujui" <?= ($result['approval_decision'] ?? '') === 'Disetujui' ? 'selected' : '' ?>>Disetujui (Sahkan & Publikasikan)</option>
                                <option value="Ditolak" <?= ($result['approval_decision'] ?? '') === 'Ditolak' ? 'selected' : '' ?>>Ditolak (Kembalikan untuk evaluasi ulang)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Catatan Arahan Direksi *</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Tuliskan arahan pelaksanaan fase selanjutnya atau alasan penolakan..." required><?= htmlspecialchars($result['approval_notes'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-stamp"></i> Simpan Keputusan Direksi
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

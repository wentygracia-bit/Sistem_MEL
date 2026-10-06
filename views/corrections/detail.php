<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>/corrections" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Permintaan Perbaikan
    </a>
</div>

<div class="grid-cols-2">
    <!-- Correction Request Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-circle-question" style="color: var(--primary-dark);"></i> Detail Permintaan Perbaikan #<?= $correction['correction_id'] ?></h2>
            <span class="badge <?= $correction['status'] === 'Direspons' ? 'badge-success' : 'badge-warning' ?>">
                <?= htmlspecialchars($correction['status']) ?>
            </span>
        </div>

        <div style="margin-bottom: 14px;">
            <div style="font-size: 0.8rem; color: var(--text-muted);">Kegiatan Terkait</div>
            <div style="font-weight: 700; font-size: 1.05rem; color: #0f172a;"><?= htmlspecialchars($correction['activity_name']) ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($correction['project_name']) ?></div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Diajukan oleh (PL)</div>
                <div style="font-weight: 600;"><?= htmlspecialchars($correction['requested_by_name']) ?></div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Ditugaskan kepada</div>
                <div style="font-weight: 600;"><?= htmlspecialchars($correction['assigned_to_name']) ?></div>
            </div>
        </div>

        <div style="margin-bottom: 16px; background: #fff1f2; border: 1px solid #fecdd3; padding: 14px; border-radius: var(--radius-sm);">
            <div style="font-size: 0.82rem; font-weight: 700; color: #9f1239; margin-bottom: 4px;">Uraian Catatan Perbaikan dari Project Leader:</div>
            <div style="font-size: 0.9rem; color: #881337;">
                <?= nl2br(htmlspecialchars($correction['description'])) ?>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $correction['update_id'] ?>" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Laporan Lapangan Terkait
            </a>
        </div>
    </div>

    <!-- Response Card -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-reply-all" style="color: var(--primary-dark);"></i> Tanggapan / Klarifikasi Staf Lapangan</h2>
        </div>

        <?php if (!empty($correction['response'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-weight: 700; color: #166534; font-size: 0.88rem;">Tanggapan Tersimpan:</span>
                    <span style="font-size: 0.78rem; color: #15803d;"><?= date('d M Y', strtotime($correction['response_date'])) ?></span>
                </div>
                <div style="font-size: 0.92rem; color: #14532d;">
                    <?= nl2br(htmlspecialchars($correction['response'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($currentUser['user_id'] == $correction['assigned_to'] || $currentUser['role_id'] == 1): ?>
            <form action="<?= BASE_URL ?>/corrections/respond" method="POST">
                <input type="hidden" name="correction_id" value="<?= $correction['correction_id'] ?>">
                
                <div class="form-group">
                    <label class="form-label">Kirim Tanggapan / Klarifikasi Baru *</label>
                    <textarea name="response" class="form-control" rows="4" placeholder="Jelaskan klarifikasi data, penyesuaian angka di lapangan, atau lampiran tindak lanjut..." required><?= htmlspecialchars($correction['response'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Tanggapan Perbaikan
                </button>
            </form>
        <?php else: ?>
            <p style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">
                Menunggu tanggapan dari staf lapangan yang ditugaskan (<?= htmlspecialchars($correction['assigned_to_name']) ?>).
            </p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

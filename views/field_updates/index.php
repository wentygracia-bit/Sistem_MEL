<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-clipboard-list" style="color: var(--primary-dark);"></i> Laporan Lapangan (Field Updates)</h2>
        <button onclick="toggleModal('modalSubmitReport')" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Input Laporan Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Pelapor (Staf)</th>
                    <th>Tanggal Lapor</th>
                    <th>Realisasi Lapangan</th>
                    <th>Progres</th>
                    <th>Bukti</th>
                    <th>Status Verifikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($updates)): ?>
                    <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada laporan dari lapangan.</td></tr>
                <?php else: ?>
                    <?php foreach ($updates as $up): ?>
                        <tr>
                            <td><strong>#<?= $up['update_id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; max-width: 250px;"><?= htmlspecialchars($up['activity_name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($up['project_name']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($up['reporter_name']) ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;"><?= date('d M Y', strtotime($up['update_date'])) ?></div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #2e4a16; font-size: 0.95rem;">
                                    <?= number_format($up['realization_value'], 0, ',', '.') ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 0.85rem;"><?= $up['progress_percentage'] ?? 0 ?>%</div>
                                <div class="progress-bar-bg" style="width: 70px;">
                                    <div class="progress-bar-fill" style="width: <?= min(100, $up['progress_percentage'] ?? 0) ?>%;"></div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($up['evidence_file'])): ?>
                                    <a href="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($up['evidence_file']) ?>" target="_blank" class="badge badge-info">
                                        <i class="fa-solid fa-file"></i> File Bukti
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($up['status'] === 'Dievaluasi'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Dievaluasi</span>
                                <?php elseif ($up['status'] === 'Perlu Perbaikan'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Perlu Perbaikan</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($up['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> Rincian
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Input Laporan Lapangan -->
<div id="modalSubmitReport" class="modal" style="display: none;">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Input Laporan Lapangan</h3>
            <button onclick="toggleModal('modalSubmitReport')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/submit" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Pilih Kegiatan *</label>
                <select name="activity_id" class="form-control" required>
                    <option value="">-- Pilih Kegiatan yang Dilaporkan --</option>
                    <?php foreach ($activities as $act): ?>
                        <option value="<?= $act['activity_id'] ?>"><?= htmlspecialchars($act['project_name']) ?> - <?= htmlspecialchars($act['activity_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Lapor *</label>
                    <input type="date" name="update_date" value="<?= date('Y-m-d') ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Angka Capaian</label>
                    <input type="number" step="any" name="realization_value" class="form-control" placeholder="cth: 38 (peserta / bibit)">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Persentase Progres (%)</label>
                    <input type="number" step="any" name="progress_percentage" class="form-control" placeholder="cth: 100">
                </div>
                <div class="form-group">
                    <label class="form-label">Unggah Bukti / Dokumentasi</label>
                    <input type="file" name="evidence_file" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi & Narasi Lapangan *</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan pelaksanaan kegiatan, kehadiran peserta, kendala, atau capaian" required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalSubmitReport')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

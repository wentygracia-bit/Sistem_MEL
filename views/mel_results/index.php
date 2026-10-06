<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-stamp" style="color: var(--primary-dark);"></i> Dokumen Hasil Akhir MEL & Pengesahan Direksi</h2>
        <?php if ($currentUser['role_id'] == 2): // Staf MEL menyusun hasil ?>
            <button onclick="toggleModal('modalCreateMEL')" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Susun Hasil MEL Baru
            </button>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Penyusun (Staf MEL)</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status Pengesahan</th>
                    <th>Disahkan Oleh</th>
                    <th>Keputusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada berkas hasil MEL yang disusun.</td></tr>
                <?php else: ?>
                    <?php foreach ($results as $res): ?>
                        <tr>
                            <td><strong>#<?= $res['mel_result_id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; max-width: 240px;"><?= htmlspecialchars($res['activity_name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= htmlspecialchars($res['project_name']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($res['submitter_name']) ?></td>
                            <td><?= date('d M Y', strtotime($res['submission_date'])) ?></td>
                            <td>
                                <?php if ($res['status'] === 'Disetujui'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-stamp"></i> Disetujui</span>
                                <?php elseif ($res['status'] === 'Ditolak'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Ditolak</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Diajukan</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($res['approver_name'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($res['approval_decision'])): ?>
                                    <strong style="color: <?= $res['approval_decision'] === 'Disetujui' ? '#2e4a16' : '#991b1b' ?>;">
                                        <?= htmlspecialchars($res['approval_decision']) ?>
                                    </strong>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">Menunggu Keputusan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/mel-results/detail?id=<?= $res['mel_result_id'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-file-contract"></i> Dokumen
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Susun Hasil MEL -->
<?php if ($currentUser['role_id'] == 2): ?>
<div id="modalCreateMEL" class="modal" style="display: none;">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Susun Hasil Akhir MEL untuk Direksi</h3>
            <button onclick="toggleModal('modalCreateMEL')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/mel-results/create" method="POST">
            <div class="form-group">
                <label class="form-label">Pilih Evaluasi Terkait (Project Leader) *</label>
                <select name="evaluation_id" id="evalSelect" class="form-control" onchange="updateActivityId()" required>
                    <option value="">-- Pilih Hasil Evaluasi PL --</option>
                    <?php foreach ($evaluations as $ev): ?>
                        <option value="<?= $ev['evaluation_id'] ?>" data-activity="<?= $ev['activity_id'] ?>">
                            #<?= $ev['evaluation_id'] ?>: <?= htmlspecialchars($ev['activity_name']) ?> (<?= htmlspecialchars($ev['result']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="activity_id" id="activityIdInput">

            <div class="form-group">
                <label class="form-label">Ringkasan Hasil Evaluasi & Rekomendasi Program *</label>
                <textarea name="result_summary" class="form-control" rows="4" placeholder="Tuliskan rangkuman capaian indikator, persentase keberhasilan program, dan usulan tindak lanjut kepada Direktur..." required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="toggleModal('modalCreateMEL')" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Ajukan ke Direktur</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}

function updateActivityId() {
    const sel = document.getElementById('evalSelect');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('activityIdInput').value = opt.getAttribute('data-activity') || '';
}
</script>
<?php endif; ?>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

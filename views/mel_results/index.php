<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
        <h2 class="card-title"><i class="fa-solid fa-stamp" style="color: var(--primary-dark);"></i> Hasil Akhir MEL & Pengesahan Direksi</h2>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Form Pencarian & Filter Status Hasil MEL -->
            <form action="<?= BASE_URL ?>/mel-results" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari kegiatan, proyek, staf..." class="form-control" style="padding-left: 32px; width: 220px; height: 38px;">
                </div>

                <select name="status" class="form-control" style="width: 160px; height: 38px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Diajukan" <?= (isset($_GET['status']) && $_GET['status'] === 'Diajukan') ? 'selected' : '' ?>>Diajukan</option>
                    <option value="Disetujui" <?= (isset($_GET['status']) && $_GET['status'] === 'Disetujui') ? 'selected' : '' ?>>Disetujui</option>
                    <option value="Ditolak" <?= (isset($_GET['status']) && $_GET['status'] === 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;" title="Cari data">
                    <i class="fa-solid fa-search"></i>
                </button>
                <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
                    <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm" style="height: 38px; display: inline-flex; align-items: center;" title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>

            <?php if ($currentUser['role_id'] == 2): // Staf MEL menyusun hasil ?>
                <button onclick="toggleModal('modalCreateMEL')" class="btn btn-primary" style="height: 38px; display: inline-flex; align-items: center;">
                    <i class="fa-solid fa-plus"></i> Susun Hasil MEL Baru
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
        <div style="padding: 10px 16px; background-color: #f8fafc; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: #475569; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <span>
                Filter aktif: 
                <?php if (!empty($_GET['search'])): ?><strong>"<?= htmlspecialchars($_GET['search']) ?>"</strong> <?php endif; ?>
                <?php if (!empty($_GET['status'])): ?><span class="badge badge-info" style="margin-left: 4px;"><?= htmlspecialchars($_GET['status']) ?></span><?php endif; ?>
                (<?= count($results) ?> berkas ditemukan)
            </span>
            <a href="<?= BASE_URL ?>/mel-results" style="color: #274312; font-weight: 600; text-decoration: underline;">Reset semua filter</a>
        </div>
    <?php endif; ?>

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
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
                                Tidak ada berkas hasil MEL yang sesuai dengan filter Anda.
                                <div style="margin-top: 8px;">
                                    <a href="<?= BASE_URL ?>/mel-results" class="btn btn-secondary btn-sm">Reset Filter</a>
                                </div>
                            <?php else: ?>
                                Belum ada berkas hasil MEL yang disusun.
                            <?php endif; ?>
                        </td>
                    </tr>
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
                    <?php if (!empty($evaluations)): ?>
                        <?php foreach ($evaluations as $ev): ?>
                            <option value="<?= $ev['evaluation_id'] ?? '' ?>" data-activity="<?= $ev['activity_id'] ?? '' ?>">
                                #<?= $ev['evaluation_id'] ?? '' ?>: <?= htmlspecialchars($ev['activity_name'] ?? '') ?> (<?= htmlspecialchars($ev['result'] ?? '') ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

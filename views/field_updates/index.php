<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
        <h2 class="card-title"><i class="fa-solid fa-clipboard-list" style="color: var(--primary-dark);"></i> Laporan Lapangan (Field Updates)</h2>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <!-- Form Pencarian & Filter Status -->
            <form action="<?= BASE_URL ?>/field-updates" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari kegiatan, staf, proyek..." class="form-control" style="padding-left: 32px; width: 220px; height: 38px;">
                </div>

                <select name="status" class="form-control" style="width: 170px; height: 38px;" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="Draft" <?= (isset($_GET['status']) && $_GET['status'] === 'Draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="Diajukan" <?= (isset($_GET['status']) && $_GET['status'] === 'Diajukan') ? 'selected' : '' ?>>Diajukan / Menunggu</option>
                    <option value="Perlu Perbaikan" <?= (isset($_GET['status']) && $_GET['status'] === 'Perlu Perbaikan') ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    <option value="Dievaluasi" <?= (isset($_GET['status']) && $_GET['status'] === 'Dievaluasi') ? 'selected' : '' ?>>Terverifikasi / Sesuai</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;" title="Cari data">
                    <i class="fa-solid fa-search"></i>
                </button>
                <?php if (!empty($_GET['search']) || !empty($_GET['status']) || !empty($_GET['activity_id'])): ?>
                    <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm" style="height: 38px; display: inline-flex; align-items: center;" title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>

            <?php if ((int)$currentUser['role_id'] === 1): ?>
            <button onclick="toggleModal('modalSubmitReport')" class="btn btn-primary" style="height: 38px;">
                <i class="fa-solid fa-plus"></i> Input Laporan Baru
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($filteredActivity)): ?>
        <div style="padding: 14px 20px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-bottom: 2px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #166534;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Laporan Lapangan Terkait Kegiatan
                </div>
                <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-top: 2px;">
                    <?= htmlspecialchars($filteredActivity['activity_name']) ?>
                </div>
                <div style="font-size: 0.8rem; color: #475569; margin-top: 2px;">
                    Proyek: <strong style="color: #1e293b;"><?= htmlspecialchars($filteredActivity['project_name']) ?></strong> (<?= htmlspecialchars($filteredActivity['project_code']) ?>) |
                    Status Kegiatan: <span class="badge badge-success" style="font-size: 0.72rem; padding: 2px 6px;"><?= htmlspecialchars($filteredActivity['status']) ?></span>
                </div>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="<?= BASE_URL ?>/projects/detail?id=<?= $filteredActivity['project_id'] ?>#act-<?= $filteredActivity['activity_id'] ?>" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Rincian Kegiatan
                </a>
                <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm" title="Tampilkan Semua Laporan">
                    <i class="fa-solid fa-list"></i> Semua Laporan
                </a>
            </div>
        </div>
    <?php elseif (!empty($_GET['search']) || !empty($_GET['status'])): ?>
        <div style="padding: 10px 16px; background-color: #f8fafc; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: #475569; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <span>
                Filter aktif: 
                <?php if (!empty($_GET['search'])): ?><strong>"<?= htmlspecialchars($_GET['search']) ?>"</strong> <?php endif; ?>
                <?php if (!empty($_GET['status'])): ?><span class="badge badge-info" style="margin-left: 4px;"><?= htmlspecialchars($_GET['status']) ?></span><?php endif; ?>
                (<?= count($updates) ?> data ditemukan)
            </span>
            <a href="<?= BASE_URL ?>/field-updates" style="color: #274312; font-weight: 600; text-decoration: underline;">Reset semua filter</a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Pelapor (Staf)</th>
                    <th>Tanggal Lapor</th>
                    <th>Realisasi Lapangan</th>
                    <th>Progres Fisik</th>
                    <th>Bukti</th>
                    <th>Status Laporan</th>
                    <th style="min-width: 170px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($updates)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                            <i class="fa-solid fa-circle-info" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                            <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
                                Tidak ditemukan data pelaporan yang cocok dengan kriteria filter.
                                <div style="margin-top: 10px;">
                                    <a href="<?= BASE_URL ?>/field-updates" class="btn btn-secondary btn-sm">Reset Filter</a>
                                </div>
                            <?php else: ?>
                                Belum ada laporan dari lapangan yang terdaftar.
                            <?php endif; ?>
                        </td>
                    </tr>
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
                                    <a href="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($up['evidence_file']) ?>" target="_blank" class="badge badge-info" title="Lihat Dokumen / Foto Bukti">
                                        <i class="fa-solid fa-paperclip"></i> Bukti
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($up['status'] === 'Dievaluasi'): ?>
                                    <span class="badge badge-success" title="Realisasi telah diverifikasi oleh evaluator"><i class="fa-solid fa-circle-check"></i> Terverifikasi</span>
                                <?php elseif ($up['status'] === 'Perlu Perbaikan'): ?>
                                    <span class="badge badge-danger" title="Laporan dikembalikan untuk revisi"><i class="fa-solid fa-triangle-exclamation"></i> Perlu Revisi</span>
                                <?php elseif ($up['status'] === 'Draft'): ?>
                                    <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" title="Belum dikirim untuk verifikasi"><i class="fa-solid fa-pen-ruler"></i> Draft</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" title="Menunggu pemeriksaan pihak evaluator"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Rincian Laporan">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <?php if ($up['status'] === 'Draft' && ($currentUser['user_id'] == $up['user_id'] || in_array($currentUser['role_id'], [2, 3]))): ?>
                                        <!-- Tombol Kirim Draft untuk Verifikasi -->
                                        <form action="<?= BASE_URL ?>/field-updates/publish" method="POST" style="display: inline;" onsubmit="return confirm('Kirim laporan #<?= $up['update_id'] ?> untuk proses verifikasi?');">
                                            <input type="hidden" name="update_id" value="<?= $up['update_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" style="background: #274312; padding: 4px 8px; font-size: 0.78rem;" title="Kirim untuk Verifikasi">
                                                <i class="fa-solid fa-paper-plane"></i> Kirim
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($currentUser['user_id'] == $up['user_id'] || in_array($currentUser['role_id'], [2, 3])): ?>
                                        <?php if ($up['status'] !== 'Dievaluasi' || in_array($currentUser['role_id'], [2, 3])): ?>
                                            <button type="button" class="btn btn-secondary btn-sm" title="Edit Laporan" onclick='openEditModal(<?= json_encode($up, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" title="Hapus Laporan" onclick="openDeleteModal(<?= $up['update_id'] ?>, '<?= htmlspecialchars(addslashes($up['activity_name'])) ?>')">
                                                <i class="fa-solid fa-trash" style="color: #dc2626;"></i>
                                            </button>
                                        <?php else: ?>
                                            <span title="Laporan sudah diverifikasi dan tidak dapat diubah" style="color: #94a3b8; font-size: 0.75rem; padding: 2px 4px;">
                                                <i class="fa-solid fa-lock"></i>
                                            </span>
                                        <?php endif; ?>
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

<!-- Modal Input Laporan Lapangan Baru (Tambah) -->
<div id="modalSubmitReport" class="modal" style="display: none;">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-plus-circle" style="color: var(--primary-dark);"></i> Input Laporan Lapangan Baru</h3>
            <button onclick="toggleModal('modalSubmitReport')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/submit" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Pilih Kegiatan Terdaftar *</label>
                <select name="activity_id" id="submit_activity_id" class="form-control" required>
                    <option value="">-- Pilih Kegiatan Proyek --</option>
                    <?php foreach ($activities as $act): ?>
                        <option value="<?= $act['activity_id'] ?>">
                            <?= htmlspecialchars($act['activity_name']) ?> (<?= htmlspecialchars($act['project_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 4px;">Pilih kegiatan yang telah didaftarkan dalam modul Proyek & Kegiatan.</small>
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
                    <input type="number" step="any" min="0" max="100" name="progress_percentage" class="form-control" placeholder="cth: 100" oninput="if(parseFloat(this.value) > 100) this.value = 100; if(parseFloat(this.value) < 0) this.value = 0;">
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

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                <button type="button" onclick="toggleModal('modalSubmitReport')" class="btn btn-secondary">Batal</button>
                <button type="submit" name="action_type" value="save_draft" class="btn btn-secondary" style="border: 1px solid #cbd5e1; background: #f8fafc; color: #475569;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Draft
                </button>
                <button type="submit" name="action_type" value="submit_verification" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Kirim untuk Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Laporan Lapangan -->
<div id="modalEditReport" class="modal" style="display: none;">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;"><i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i> Edit Laporan Lapangan</h3>
            <button onclick="toggleModal('modalEditReport')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_id" id="edit_update_id">

            <div class="form-group">
                <label class="form-label">Kegiatan Proyek *</label>
                <select name="activity_id" id="edit_activity_id" class="form-control" required>
                    <option value="">-- Pilih Kegiatan Proyek --</option>
                    <?php foreach ($activities as $act): ?>
                        <option value="<?= $act['activity_id'] ?>">
                            <?= htmlspecialchars($act['activity_name']) ?> (<?= htmlspecialchars($act['project_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Lapor *</label>
                    <input type="date" name="update_date" id="edit_update_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Angka Capaian</label>
                    <input type="number" step="any" name="realization_value" id="edit_realization_value" class="form-control" placeholder="cth: 38">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Persentase Progres (%)</label>
                    <input type="number" step="any" min="0" max="100" name="progress_percentage" id="edit_progress_percentage" class="form-control" placeholder="cth: 100">
                </div>
                <div class="form-group">
                    <label class="form-label">Ganti Bukti / Dokumentasi (Opsional)</label>
                    <input type="file" name="evidence_file" class="form-control">
                    <small id="edit_evidence_info" style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 4px;"></small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi & Narasi Lapangan *</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                <button type="button" onclick="toggleModal('modalEditReport')" class="btn btn-secondary">Batal</button>
                <button type="submit" name="action_type" value="save_only" class="btn btn-secondary" style="border: 1px solid #cbd5e1; background: #f8fafc; color: #475569;">
                    <i class="fa-solid fa-save"></i> Simpan Saja
                </button>
                <button type="submit" name="action_type" value="submit_verification" class="btn btn-primary" id="btn_edit_resubmit">
                    <i class="fa-solid fa-paper-plane"></i> Simpan & Kirim Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Laporan -->
<div id="modalDeleteReport" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 440px;">
        <div style="text-align: center; padding: 10px 0 20px 0;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 16px auto;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Konfirmasi Hapus Laporan</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 4px;">Apakah Anda yakin ingin menghapus data laporan pelaporan ini?</p>
            <p id="delete_activity_preview" style="font-weight: 600; color: #1e293b; font-size: 0.95rem;"></p>
            <p style="color: #dc2626; font-size: 0.8rem; margin-top: 6px;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/delete" method="POST">
            <input type="hidden" name="update_id" id="delete_update_id">
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button type="button" onclick="toggleModal('modalDeleteReport')" class="btn btn-secondary" style="min-width: 100px;">Batal</button>
                <button type="submit" class="btn btn-danger" style="min-width: 100px;"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}

function openEditModal(data) {
    document.getElementById('edit_update_id').value = data.update_id || '';
    if (document.getElementById('edit_activity_id')) {
        document.getElementById('edit_activity_id').value = data.activity_id || '';
    }
    if (document.getElementById('edit_activity_name')) {
        document.getElementById('edit_activity_name').value = data.activity_name || '';
    }
    document.getElementById('edit_update_date').value = data.update_date || '';
    document.getElementById('edit_realization_value').value = data.realization_value || '';
    document.getElementById('edit_progress_percentage').value = data.progress_percentage || '';
    document.getElementById('edit_description').value = data.description || '';
    
    const infoEl = document.getElementById('edit_evidence_info');
    if (data.evidence_file) {
        infoEl.textContent = 'File saat ini: ' + data.evidence_file;
    } else {
        infoEl.textContent = 'Belum ada bukti yang diunggah.';
    }

    toggleModal('modalEditReport');
}

function openDeleteModal(id, activityName) {
    document.getElementById('delete_update_id').value = id;
    document.getElementById('delete_activity_preview').textContent = 'Laporan #' + id + ': ' + activityName;
    toggleModal('modalDeleteReport');
}
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

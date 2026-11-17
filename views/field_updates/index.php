<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div class="card-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
        <h2 class="card-title"><i class="fa-solid fa-clipboard-list" style="color: var(--primary-dark);"></i> Laporan Lapangan</h2>
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
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Kegiatan & Proyek</th>
                    <th>Pelapor (Staf)</th>
                    <th>Tanggal Lapor</th>
                    <th>Realisasi Lapangan</th>
                    <th>Progres Fisik</th>
                    <th style="text-align: center; min-width: 100px;">Bukti</th>
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
                    <?php 
                    $no = 1;
                    foreach ($updates as $up): 
                    ?>
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: #475569;"><?= $no++ ?></td>
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
                            <td style="text-align: center; vertical-align: middle;">
                                <?php 
                                $evCat = FileHelper::categorizeFiles($up['evidence_file']);
                                if ($evCat['total'] > 0): 
                                ?>
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: center; justify-content: center;">
                                        <?php if (count($evCat['images']) > 0): ?>
                                            <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>#evidence-section" class="badge" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; text-decoration: none; font-size: 0.72rem; padding: 4px 8px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; white-space: nowrap;" title="<?= count($evCat['images']) ?> gambar bukti terlampir">
                                                <i class="fa-solid fa-images"></i>
                                                <span><?= count($evCat['images']) ?> Gambar</span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (count($evCat['docs']) > 0): ?>
                                            <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>#evidence-section" class="badge" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; text-decoration: none; font-size: 0.72rem; padding: 4px 8px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; white-space: nowrap;" title="<?= count($evCat['docs']) ?> dokumen pendukung terlampir">
                                                <i class="fa-solid fa-file-lines"></i>
                                                <span><?= count($evCat['docs']) ?> Dokumen</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 0.85rem; color: var(--text-muted); display: inline-block; text-align: center;">-</span>
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
                                <?php if (!empty($up['director_notes'])): ?>
                                    <div style="margin-top: 4px;">
                                        <span class="badge" style="font-size: 0.68rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;" title="Catatan Direktur: <?= htmlspecialchars($up['director_notes']) ?>">
                                            <i class="fa-solid fa-crown"></i> Catatan Direktur
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: nowrap;">
                                    <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Detail Laporan">
                                        <i class="fa-solid fa-eye" style="color: #2e4a16;"></i>
                                    </a>

                                    <?php if ($up['status'] === 'Draft' && (int)$currentUser['role_id'] === 1 && (int)$currentUser['user_id'] === (int)$up['user_id']): ?>
                                        <!-- Tombol Kirim Draft untuk Verifikasi (Hanya Staf Lapangan Pemilik) -->
                                        <form action="<?= BASE_URL ?>/field-updates/publish" method="POST" style="display: inline;" onsubmit="return confirm('Kirim laporan #<?= $up['update_id'] ?> untuk proses verifikasi?');">
                                            <input type="hidden" name="update_id" value="<?= $up['update_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" style="background: #274312; padding: 4px 8px; font-size: 0.78rem;" title="Kirim untuk Verifikasi">
                                                <i class="fa-solid fa-paper-plane"></i> Kirim
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php 
                                    // Tombol Edit dan Hapus HANYA untuk Staf Lapangan (role_id == 1) pada laporan miliknya sendiri
                                    // Staf MEL (role_id == 2) hanya memiliki hak baca (read-only) via tombol Detail
                                    $canManage = ((int)$currentUser['role_id'] === 1 && (int)$currentUser['user_id'] === (int)$up['user_id']);
                                    if ($canManage): 
                                    ?>
                                        <?php if ($up['status'] !== 'Dievaluasi'): ?>
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

<!-- Modal Input Laporan Lapangan Baru (Modular Component) -->
<?php include __DIR__ . '/_modal_submit.php'; ?>

<!-- Modal Edit Laporan Lapangan -->
<div id="modalEditReport" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 680px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #0284c7; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i> Edit Laporan Lapangan
            </h3>
            <button type="button" onclick="toggleModal('modalEditReport')" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer; line-height: 1; padding: 4px;" title="Tutup">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/edit" method="POST" enctype="multipart/form-data" id="formEditReport">
            <input type="hidden" name="update_id" id="edit_update_id">
            <?php
            $editRedirectUri = $_SERVER['REQUEST_URI'] ?? '/field-updates';
            if (!empty(BASE_URL) && strpos($editRedirectUri, BASE_URL) === 0) {
                $editRedirectUri = substr($editRedirectUri, strlen(BASE_URL));
            }
            ?>
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($editRedirectUri) ?>">

            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Kegiatan Proyek *</label>
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
                    <label class="form-label" style="font-weight: 600;">Tanggal Lapor *</label>
                    <input type="date" name="update_date" id="edit_update_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600;">Realisasi Angka Capaian</label>
                    <input type="number" step="any" name="realization_value" id="edit_realization_value" class="form-control" placeholder="Angka capaian">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Persentase Progres (%)</label>
                <input type="number" step="any" min="0" max="100" name="progress_percentage" id="edit_progress_percentage" class="form-control" placeholder="0 - 100">
            </div>

            <!-- BAGIAN BUKTI & DOKUMEN PENDUKUNG SAAT EDIT -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-paperclip" style="color: #0284c7;"></i> Kelola Gambar Bukti & Dokumen Pendukung
                    </div>
                    <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 0.72rem; border: 1px solid #e2e8f0;">
                        <i class="fa-regular fa-circle-question"></i> Opsional
                    </span>
                </div>

                <!-- List Berkas yang Sedang Terlampir Sebelumnya -->
                <div id="edit_existing_files_section" style="margin-bottom: 14px;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 6px;">
                        Berkas yang Sedang Terlampir:
                    </div>
                    <div id="edit_existing_files_list" style="display: flex; flex-direction: column; gap: 6px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Tidak ada berkas terlampir.</span>
                    </div>
                </div>

                <div style="font-size: 0.78rem; color: #64748b; margin-bottom: 10px;">
                    Tambah berkas baru (bisa pilih lebih dari satu berkas):
                </div>

                <!-- Grid 2 Kolom untuk Upload Tambahan -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <!-- Area Upload Gambar Tambahan -->
                    <div style="border: 1px dashed #cbd5e1; border-radius: var(--radius-sm); padding: 12px; background: #fafafa; text-align: center;">
                        <div style="font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                            <i class="fa-solid fa-images" style="color: #7c3aed;"></i> Tambah Gambar
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 8px;">
                            Pilih >1 foto (JPG, PNG, WEBP)
                        </div>
                        <input type="file" name="evidence_images[]" id="edit_evidence_images" accept="image/jpeg,image/png,image/webp,image/jpg" multiple style="display: none;" onchange="handleEvidenceSelect(this, 'edit', 'images')">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit_evidence_images').click()" style="width: 100%; font-size: 0.78rem; padding: 6px 10px; background: #ffffff;">
                            <i class="fa-solid fa-plus" style="color: #7c3aed;"></i> Pilih Gambar...
                        </button>
                    </div>

                    <!-- Area Upload Dokumen Tambahan -->
                    <div style="border: 1px dashed #cbd5e1; border-radius: var(--radius-sm); padding: 12px; background: #fafafa; text-align: center;">
                        <div style="font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 2px;">
                            <i class="fa-solid fa-file-lines" style="color: #2563eb;"></i> Tambah Dokumen
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 8px;">
                            Pilih >1 berkas (PDF, DOC, XLS, ZIP)
                        </div>
                        <input type="file" name="evidence_docs[]" id="edit_evidence_docs" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.rar,.7z,.ppt,.pptx,.txt" multiple style="display: none;" onchange="handleEvidenceSelect(this, 'edit', 'docs')">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit_evidence_docs').click()" style="width: 100%; font-size: 0.78rem; padding: 6px 10px; background: #ffffff;">
                            <i class="fa-solid fa-plus" style="color: #2563eb;"></i> Pilih Dokumen...
                        </button>
                    </div>
                </div>

                <!-- Live Preview List Gambar Tambahan -->
                <div id="edit_images_preview_container" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-images" style="color: #7c3aed; margin-right: 4px;"></i> List Gambar Baru (<span id="edit_images_count">0</span>):</span>
                        <button type="button" onclick="clearEvidenceSelection('edit', 'images')" style="background: none; border: none; font-size: 0.72rem; color: #dc2626; cursor: pointer; text-decoration: underline;">Hapus Semua</button>
                    </div>
                    <div id="edit_images_preview_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px;"></div>
                </div>

                <!-- Live Preview List Dokumen Tambahan -->
                <div id="edit_docs_preview_container" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-file-lines" style="color: #2563eb; margin-right: 4px;"></i> List Dokumen Baru (<span id="edit_docs_count">0</span>):</span>
                        <button type="button" onclick="clearEvidenceSelection('edit', 'docs')" style="background: none; border: none; font-size: 0.72rem; color: #dc2626; cursor: pointer; text-decoration: underline;">Hapus Semua</button>
                    </div>
                    <div id="edit_docs_preview_list" style="display: flex; flex-direction: column; gap: 6px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Deskripsi & Narasi Lapangan *</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                <button type="button" onclick="toggleModal('modalEditReport')" class="btn btn-secondary">Batal</button>
                <button type="submit" name="action_type" value="save_only" class="btn btn-secondary" style="border: 1px solid #cbd5e1; background: #f8fafc; color: #475569;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Saja
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
    const isOpening = (modal.style.display === 'none' || modal.style.display === '');
    modal.style.display = isOpening ? 'flex' : 'none';
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

    // Render list existing files
    const existingListEl = document.getElementById('edit_existing_files_list');
    if (existingListEl) {
        existingListEl.innerHTML = '';
        let existingFiles = [];
        if (data.evidence_file) {
            try {
                if (typeof data.evidence_file === 'string' && data.evidence_file.trim().startsWith('[')) {
                    existingFiles = JSON.parse(data.evidence_file);
                } else if (typeof data.evidence_file === 'string') {
                    existingFiles = [data.evidence_file];
                } else if (Array.isArray(data.evidence_file)) {
                    existingFiles = data.evidence_file;
                }
            } catch(e) {
                existingFiles = [data.evidence_file];
            }
        }

        if (!existingFiles || existingFiles.length === 0) {
            existingListEl.innerHTML = '<span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Tidak ada berkas terlampir sebelumnya.</span>';
        } else {
            existingFiles.forEach(function(fn) {
                if (!fn) return;
                const iconInfo = getFileIconClassJS(fn);
                const item = document.createElement('div');
                item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem;';
                item.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                        <input type="hidden" name="existing_files[]" value="${fn}">
                        <i class="${iconInfo.icon}" style="font-size: 1rem; color: ${iconInfo.color}; flex-shrink: 0;"></i>
                        <span style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.8rem;" title="${fn}">
                            ${fn}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                        <a href="<?= BASE_URL ?>/public/uploads/${encodeURIComponent(fn)}" target="_blank" style="font-size: 0.75rem; color: #0284c7; text-decoration: none;" title="Buka berkas">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <button type="button" onclick="this.closest('div[style]').remove(); if(document.querySelectorAll('#edit_existing_files_list > div').length===0){document.getElementById('edit_existing_files_list').innerHTML='<span style=\\'font-size: 0.75rem; color: var(--text-muted); font-style: italic;\\'>Semua berkas lama dihapus.</span>';}" style="background: none; border: none; color: #ef4444; font-size: 0.85rem; cursor: pointer; padding: 2px 4px;" title="Hapus berkas ini">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
                existingListEl.appendChild(item);
            });
        }
    }

    // Reset newly selected files for edit modal
    clearEvidenceSelection('edit', 'images');
    clearEvidenceSelection('edit', 'docs');

    toggleModal('modalEditReport');
}

function openDeleteModal(id, activityName) {
    document.getElementById('delete_update_id').value = id;
    document.getElementById('delete_activity_preview').textContent = 'Laporan #' + id + ': ' + activityName;
    toggleModal('modalDeleteReport');
}

// Auto open submit modal jika terdapat query parameter ?action=new atau ?activity_id=...
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'new' || urlParams.get('new_report') === '1') {
        const actId = urlParams.get('activity_id');
        if (actId && window.availableActivitiesData) {
            const foundAct = window.availableActivitiesData.find(function(a) { return a.id === parseInt(actId); });
            if (foundAct) {
                window.openSubmitReportModalForActivity(foundAct);
            } else {
                toggleModal('modalSubmitReport');
            }
        } else {
            toggleModal('modalSubmitReport');
        }
    }
});
</script>
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

<?php
require_once __DIR__ . '/../layouts/header.php';
$currentUser = $currentUser ?? AuthHelper::getUser();
?>

<!-- Card Header Sapaan Pengguna Staf Lapangan -->
<div class="dashboard-banner" style="background: linear-gradient(135deg, #ffffff 0%, #f4faeb 100%); border: 1px solid var(--border-color); border-left: 6px solid var(--primary-dark); border-radius: var(--radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px;">
                <i class="fa-solid fa-person-digging"></i> Staf Lapangan
            </span>
            <span style="font-size: 0.82rem; color: var(--text-muted); font-weight: 500;">Yayasan Natural Kapital Indonesia</span>
        </div>
        <h1 style="font-size: 1.4rem; font-weight: 800; color: #1e330c; margin: 6px 0 2px 0;">
            Selamat datang kembali, <?= htmlspecialchars($currentUser['name'] ?? 'Staf Lapangan') ?>!
        </h1>
        <p style="font-size: 0.88rem; color: #475569; margin: 0;">
            Ringkasan tugas lapangan, pengiriman laporan, dan respons perbaikan.
        </p>
    </div>
</div>

<!-- 4 Kartu Ringkasan (KPI Cards) Staf Lapangan -->
<div class="grid-cols-4" style="margin-bottom: 24px;">
    <!-- 1. Total Kegiatan -->
    <a href="<?= BASE_URL ?>/projects" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(43, 107, 43, 0.12); color: #2e4a16;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= $totalAssignedActivities ?></div>
                <div class="stat-label">Total Kegiatan Ditugaskan</div>
            </div>
        </div>
    </a>

    <!-- 2. Kegiatan Sedang Berjalan -->
    <a href="<?= BASE_URL ?>/projects?status=Berjalan" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                <i class="fa-solid fa-person-digging"></i>
            </div>
            <div>
                <div class="stat-value"><?= $runningActivitiesCount ?></div>
                <div class="stat-label">Sedang Berjalan</div>
            </div>
        </div>
    </a>

    <!-- 3. Laporan Belum Dikirim (Draft) -->
    <a href="<?= BASE_URL ?>/field-updates?status=Draft" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div class="stat-icon" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div>
                <div class="stat-value"><?= $draftReportsCount ?></div>
                <div class="stat-label">Laporan Draft / Belum Kirim</div>
            </div>
        </div>
    </a>

    <!-- 4. Permintaan Revisi -->
    <a href="<?= BASE_URL ?>/corrections" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; <?= $pendingRevisionsCount > 0 ? 'border: 1px solid #fca5a5; background: #fff5f5;' : '' ?>">
            <div class="stat-icon" style="background: rgba(220, 38, 38, 0.12); color: #dc2626;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <div class="stat-value" style="<?= $pendingRevisionsCount > 0 ? 'color: #dc2626;' : '' ?>"><?= $pendingRevisionsCount ?></div>
                <div class="stat-label">Permintaan Revisi</div>
            </div>
        </div>
    </a>
</div>

<!-- Layout 2 Kolom: Progress Kegiatan & Sisi Kanan (Deadline + Aktivitas Terbaru) -->
<div class="grid-cols-2" style="margin-bottom: 24px;">
    <!-- Bagian Kiri: Progress Kegiatan (Maks 5 kegiatan relevan) -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title">
                <i class="fa-solid fa-bars-progress" style="color: var(--primary-dark);"></i> Progress Kegiatan Lapangan
            </h2>
            <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm">
                Lihat Semua Kegiatan <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i>
            </a>
        </div>

        <?php if (empty($topActivities)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                Belum ada kegiatan yang ditugaskan ke Anda saat ini.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($topActivities as $act): ?>
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; transition: box-shadow 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
                            <div>
                                <a href="<?= BASE_URL ?>/projects/detail?id=<?= $act['project_id'] ?>#act-<?= $act['activity_id'] ?>" style="font-weight: 700; color: #0f172a; text-decoration: none; font-size: 0.95rem;">
                                    <?= htmlspecialchars($act['activity_name']) ?>
                                </a>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">
                                    <i class="fa-solid fa-folder" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($act['project_name']) ?> &bull; 
                                    <i class="fa-solid fa-location-dot" style="font-size: 0.72rem;"></i> <?= htmlspecialchars($act['location'] ?: 'Lapangan') ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($act['display_status'] === 'Selesai'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Selesai</span>
                                <?php elseif ($act['display_status'] === 'Terlambat'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat</span>
                                <?php elseif ($act['display_status'] === 'Belum Dimulai'): ?>
                                    <span class="badge badge-secondary"><i class="fa-solid fa-clock"></i> Belum Dimulai</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-person-running"></i> Berjalan</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info Jadwal & Deadline -->
                        <div style="font-size: 0.8rem; color: #475569; margin-bottom: 12px; display: flex; gap: 16px; flex-wrap: wrap;">
                            <span>
                                <i class="fa-regular fa-calendar"></i> Mulai: <strong><?= $act['start_date'] ? date('d M Y', strtotime($act['start_date'])) : '-' ?></strong>
                            </span>
                            <span>
                                <i class="fa-solid fa-flag-checkered"></i> Deadline: <strong><?= $act['end_date'] ? date('d M Y', strtotime($act['end_date'])) : '-' ?></strong>
                            </span>
                        </div>

                        <!-- Progress Bar & Target/Realisasi Terverifikasi -->
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 0.82rem; margin-bottom: 6px;">
                                <span style="color: #475569;">
                                    Capaian Terverifikasi: 
                                    <strong style="color: #2e4a16; font-size: 0.95rem;"><?= number_format($act['verified_realization'], 0, ',', '.') ?></strong> / <?= number_format($act['total_target'], 0, ',', '.') ?> <?= htmlspecialchars($act['unit']) ?>
                                    <?php if ($act['total_submitted_realization'] > $act['verified_realization']): ?>
                                        <small style="color: #d97706; margin-left: 4px;" title="Realisasi diajukan yang belum dievaluasi resmi">(+<?= number_format($act['total_submitted_realization'] - $act['verified_realization'], 0, ',', '.') ?> diajukan)</small>
                                    <?php endif; ?>
                                </span>
                                <span style="font-weight: 800; color: #1e330c; font-size: 0.95rem;"><?= $act['calculated_progress'] ?>%</span>
                            </div>

                            <div class="progress-bar-bg" style="width: 100%; height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden;">
                                <div class="progress-bar-fill" style="width: <?= min(100, $act['calculated_progress']) ?>%; height: 100%; background: var(--primary); transition: width 0.4s ease;"></div>
                            </div>
                        </div>

                        <!-- Action Link -->
                        <div class="activity-actions" style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; padding-top: 10px; border-top: 1px dashed var(--border-color);">
                            <a href="<?= BASE_URL ?>/field-updates?search=<?= urlencode($act['activity_name']) ?>" class="btn btn-secondary btn-sm" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-list"></i> Lihat Laporan (<?= $act['total_reports'] ?>)
                            </a>
                            <a href="<?= BASE_URL ?>/field-updates" class="btn btn-primary btn-sm" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-plus"></i> Input Laporan
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bagian Kanan: Deadline Terdekat & Aktivitas Terbaru -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Deadline Terdekat -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fa-solid fa-stopwatch" style="color: #d97706;"></i> Deadline Terdekat
                </h2>
            </div>
            <?php if (empty($upcomingDeadlines)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">Tidak ada kegiatan yang mendekati deadline dalam 14 hari ke depan.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($upcomingDeadlines as $dl): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: <?= $dl['diff_days'] < 0 ? '#fef2f2' : '#fffbeb' ?>; border: 1px solid <?= $dl['diff_days'] < 0 ? '#fca5a5' : '#fde68a' ?>; border-radius: var(--radius-sm);">
                            <div>
                                <div style="font-weight: 700; font-size: 0.88rem; color: #1e293b;"><?= htmlspecialchars($dl['activity_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    <?= htmlspecialchars($dl['project_name']) ?> &bull; Batas: <strong><?= date('d M Y', strtotime($dl['end_date'])) ?></strong>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <?php if ($dl['diff_days'] < 0): ?>
                                    <span class="badge badge-danger" style="font-size: 0.75rem;">Terlambat <?= abs($dl['diff_days']) ?> hari</span>
                                <?php elseif ($dl['diff_days'] === 0): ?>
                                    <span class="badge badge-warning" style="font-size: 0.75rem;">Hari ini!</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="font-size: 0.75rem;"><?= $dl['diff_days'] ?> hari lagi</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Aktivitas & Riwayat Terbaru -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-dark);"></i> Aktivitas Terbaru Anda
                </h2>
                <a href="<?= BASE_URL ?>/notifications" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">Pusat Notifikasi</a>
            </div>

            <?php if (empty($recentStaffUpdates) && empty($recentNotifications)): ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada riwayat aktivitas terbaru.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($recentStaffUpdates as $up): ?>
                        <div style="display: flex; gap: 12px; align-items: flex-start; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; color: #1e293b;">
                                    Laporan <strong>#<?= $up['update_id'] ?></strong>: <?= htmlspecialchars($up['activity_name']) ?>
                                </div>
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 3px;">
                                    <span class="badge <?= $up['status'] === 'Dievaluasi' ? 'badge-success' : ($up['status'] === 'Perlu Perbaikan' ? 'badge-danger' : 'badge-warning') ?>" style="font-size: 0.68rem;">
                                        <?= htmlspecialchars($up['status']) ?>
                                    </span>
                                    <span style="font-size: 0.74rem; color: var(--text-muted);">
                                        <?= date('d M Y', strtotime($up['update_date'])) ?> &bull; Capaian: <?= number_format($up['realization_value'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; gap: 4px; align-items: center; flex-wrap: nowrap;">
                                <a href="<?= BASE_URL ?>/field-updates/detail?id=<?= $up['update_id'] ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="Lihat Laporan">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($up['status'] !== 'Dievaluasi'): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="Edit Laporan" onclick='openEditModal(<?= json_encode($up, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <i class="fa-solid fa-pen-to-square" style="color: #0284c7;"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;" title="Hapus Laporan" onclick="openDeleteModal(<?= $up['update_id'] ?>, '<?= htmlspecialchars(addslashes($up['activity_name'])) ?>')">
                                        <i class="fa-solid fa-trash" style="color: #dc2626;"></i>
                                    </button>
                                <?php else: ?>
                                    <span title="Laporan sudah diverifikasi dan tidak dapat diubah" style="color: #94a3b8; font-size: 0.75rem; padding: 2px 4px;">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
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

            <div class="modal-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Lapor *</label>
                    <input type="date" name="update_date" id="edit_update_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Realisasi Angka Capaian</label>
                    <input type="number" step="any" name="realization_value" id="edit_realization_value" class="form-control" placeholder="Angka capaian">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Persentase Progres (%)</label>
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
    const isOpening = (modal.style.display === 'none' || modal.style.display === '');
    modal.style.display = isOpening ? 'flex' : 'none';
}

window.evidenceHolders = window.evidenceHolders || {
    edit: {
        imagesDT: new DataTransfer(),
        docsDT: new DataTransfer()
    }
};

function formatFileSizeJS(bytes) {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(1) + ' MB';
    } else if (bytes >= 1024) {
        return Math.round(bytes / 1024) + ' KB';
    }
    return bytes + ' B';
}

function getFileIconClassJS(filename) {
    const ext = (filename.split('.').pop() || '').toLowerCase();
    switch (ext) {
        case 'pdf': return { icon: 'fa-solid fa-file-pdf', color: '#ef4444' };
        case 'doc':
        case 'docx': return { icon: 'fa-solid fa-file-word', color: '#2563eb' };
        case 'xls':
        case 'xlsx':
        case 'csv': return { icon: 'fa-solid fa-file-excel', color: '#16a34a' };
        case 'ppt':
        case 'pptx': return { icon: 'fa-solid fa-file-powerpoint', color: '#ea580c' };
        case 'zip':
        case 'rar':
        case '7z': return { icon: 'fa-solid fa-file-zipper', color: '#d97706' };
        case 'txt': return { icon: 'fa-solid fa-file-lines', color: '#64748b' };
        default: return { icon: 'fa-solid fa-file', color: '#64748b' };
    }
}

function handleEvidenceSelect(inputEl, prefix, type) {
    if (!window.evidenceHolders[prefix]) {
        window.evidenceHolders[prefix] = { imagesDT: new DataTransfer(), docsDT: new DataTransfer() };
    }
    const dt = (type === 'images') ? window.evidenceHolders[prefix].imagesDT : window.evidenceHolders[prefix].docsDT;
    const newFiles = inputEl.files;

    for (let i = 0; i < newFiles.length; i++) {
        let exists = false;
        for (let j = 0; j < dt.items.length; j++) {
            const f = dt.files[j];
            if (f.name === newFiles[i].name && f.size === newFiles[i].size) {
                exists = true;
                break;
            }
        }
        if (!exists) {
            dt.items.add(newFiles[i]);
        }
    }

    inputEl.files = dt.files;
    renderEvidencePreview(prefix, type);
}

function removeEvidenceFile(prefix, type, index) {
    const dt = (type === 'images') ? window.evidenceHolders[prefix].imagesDT : window.evidenceHolders[prefix].docsDT;
    const inputEl = document.getElementById(prefix + '_evidence_' + type);
    const newDT = new DataTransfer();

    for (let i = 0; i < dt.files.length; i++) {
        if (i !== index) {
            newDT.items.add(dt.files[i]);
        }
    }

    if (type === 'images') {
        window.evidenceHolders[prefix].imagesDT = newDT;
    } else {
        window.evidenceHolders[prefix].docsDT = newDT;
    }

    if (inputEl) {
        inputEl.files = newDT.files;
    }

    renderEvidencePreview(prefix, type);
}

function clearEvidenceSelection(prefix, type) {
    if (!window.evidenceHolders[prefix]) {
        window.evidenceHolders[prefix] = { imagesDT: new DataTransfer(), docsDT: new DataTransfer() };
    }
    if (type === 'images') {
        window.evidenceHolders[prefix].imagesDT = new DataTransfer();
    } else {
        window.evidenceHolders[prefix].docsDT = new DataTransfer();
    }
    const inputEl = document.getElementById(prefix + '_evidence_' + type);
    if (inputEl) {
        inputEl.value = '';
    }
    renderEvidencePreview(prefix, type);
}

function renderEvidencePreview(prefix, type) {
    if (!window.evidenceHolders[prefix]) return;
    const dt = (type === 'images') ? window.evidenceHolders[prefix].imagesDT : window.evidenceHolders[prefix].docsDT;
    const container = document.getElementById(prefix + '_' + type + '_preview_container');
    const countEl = document.getElementById(prefix + '_' + type + '_count');

    if (!container) return;

    if (dt.files.length === 0) {
        container.style.display = 'none';
        if (countEl) countEl.textContent = '0';
        return;
    }

    container.style.display = 'block';
    if (countEl) countEl.textContent = dt.files.length;

    if (type === 'images') {
        const grid = document.getElementById(prefix + '_images_preview_grid');
        if (!grid) return;
        grid.innerHTML = '';

        for (let i = 0; i < dt.files.length; i++) {
            const file = dt.files[i];
            const thumbUrl = URL.createObjectURL(file);
            const card = document.createElement('div');
            card.style.cssText = 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px; position: relative; font-size: 0.72rem; box-shadow: var(--shadow-sm);';
            card.innerHTML = `
                <div style="width: 100%; height: 75px; border-radius: 4px; overflow: hidden; background: #f1f5f9; margin-bottom: 4px;">
                    <img src="${thumbUrl}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${file.name}">
                    ${file.name}
                </div>
                <div style="color: #64748b; font-size: 0.68rem; margin-top: 2px;">
                    ${formatFileSizeJS(file.size)}
                </div>
                <button type="button" onclick="removeEvidenceFile('${prefix}', 'images', ${i})" style="position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; border-radius: 50%; background: #ef4444; color: #ffffff; border: 2px solid #ffffff; font-size: 0.65rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus gambar ini">&times;</button>
            `;
            grid.appendChild(card);
        }
    } else {
        const list = document.getElementById(prefix + '_docs_preview_list');
        if (!list) return;
        list.innerHTML = '';

        for (let i = 0; i < dt.files.length; i++) {
            const file = dt.files[i];
            const iconInfo = getFileIconClassJS(file.name);
            const item = document.createElement('div');
            item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem;';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                    <i class="${iconInfo.icon}" style="font-size: 1.1rem; color: ${iconInfo.color}; flex-shrink: 0;"></i>
                    <div style="min-width: 0;">
                        <div style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.8rem;" title="${file.name}">
                            ${file.name}
                        </div>
                        <div style="font-size: 0.7rem; color: #64748b;">
                            ${formatFileSizeJS(file.size)}
                        </div>
                    </div>
                </div>
                <button type="button" onclick="removeEvidenceFile('${prefix}', 'docs', ${i})" style="background: none; border: none; color: #ef4444; font-size: 0.85rem; cursor: pointer; padding: 4px 6px; margin-left: 8px;" title="Hapus dokumen ini">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            list.appendChild(item);
        }
    }
}

function openEditModal(data) {
    document.getElementById('edit_update_id').value = data.update_id || '';
    if (document.getElementById('edit_activity_id')) {
        document.getElementById('edit_activity_id').value = data.activity_id || '';
    }
    document.getElementById('edit_update_date').value = data.update_date || '';
    document.getElementById('edit_realization_value').value = data.realization_value || '';
    document.getElementById('edit_progress_percentage').value = data.progress_percentage || '';
    document.getElementById('edit_description').value = data.description || '';
    
    // Render existing files
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

    clearEvidenceSelection('edit', 'images');
    clearEvidenceSelection('edit', 'docs');

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

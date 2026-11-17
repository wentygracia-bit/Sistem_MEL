<?php
// views/field_updates/_modal_submit.php
// Reusable Modal Component: Input Laporan Lapangan Baru
// Digunakan di: Pelaporan Lapangan, Proyek & Kegiatan (Index & Detail)

$modalActivities = $activities ?? ($fieldActivities ?? []);
$rawUri = $_SERVER['REQUEST_URI'] ?? '/field-updates';
if (!empty(BASE_URL) && strpos($rawUri, BASE_URL) === 0) {
    $currentRedirectUrl = substr($rawUri, strlen(BASE_URL));
} else {
    $currentRedirectUrl = $rawUri;
}
?>

<!-- Modal Input Laporan Lapangan Baru -->
<div id="modalSubmitReport" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 680px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e330c; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary-dark);"></i> Input Laporan Lapangan Baru
            </h3>
            <button type="button" onclick="toggleModal('modalSubmitReport')" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer; line-height: 1; padding: 4px;" title="Tutup">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/field-updates/submit" method="POST" enctype="multipart/form-data" id="formSubmitReport">
            <input type="hidden" name="redirect_to" id="submit_redirect_to" value="<?= htmlspecialchars($currentRedirectUrl) ?>">

            <!-- Kegiatan Proyek -->
            <div class="form-group" style="position: relative;">
                <label class="form-label" style="font-weight: 600;">Kegiatan Proyek *</label>
                <!-- Hidden input untuk menyimpan activity_id yang dipilih secara valid -->
                <input type="hidden" name="activity_id" id="submit_activity_id" value="" required>

                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; pointer-events: none;"></i>
                    <input type="text" id="submit_activity_search" class="form-control" 
                           placeholder="Ketik untuk mencari kegiatan proyek..." 
                           autocomplete="off" 
                           style="padding-left: 38px; padding-right: 36px; cursor: pointer;"
                           required>
                    <button type="button" id="submit_activity_clear_btn" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; font-size: 0.95rem; cursor: pointer; padding: 4px;" title="Hapus pilihan">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <!-- Petunjuk & Indikator Validasi Terpilih -->
                <div id="submit_activity_selected_badge" style="display: none; margin-top: 6px; font-size: 0.82rem; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 10px; border-radius: var(--radius-sm); align-items: center; justify-content: space-between;">
                    <div>
                        <i class="fa-solid fa-circle-check"></i> Kegiatan Terpilih: <strong id="submit_activity_selected_text"></strong>
                    </div>
                </div>

                <small id="submit_activity_help_text" style="color: var(--text-muted); font-size: 0.75rem; display: block; margin-top: 4px;">
                    <i class="fa-solid fa-circle-info"></i> Ketik nama kegiatan yang ingin dilaporkan lalu pilih dari daftar saran.
                </small>

                <!-- Dropdown Saran Hasil Pencarian Autocomplete -->
                <div id="submit_activity_dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); max-height: 220px; overflow-y: auto; margin-top: 4px;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Tanggal Lapor *</label>
                <input type="date" name="update_date" value="<?= date('Y-m-d') ?>" class="form-control" required>
            </div>

            <!-- Input Angka Capaian & Akumulasi Persentase Otomatis Sistem -->
            <div class="form-group" style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: center;">
                    <div>
                        <label class="form-label" style="margin-bottom: 4px; font-weight: 600;">
                            Realisasi Angka Capaian * 
                            <span id="submit_unit_badge" style="font-weight: 500; font-size: 0.78rem; color: var(--text-muted);">(Satuan)</span>
                        </label>
                        <input type="number" step="any" min="0" name="realization_value" id="submit_realization_value" class="form-control" placeholder="Masukkan angka capaian" required>
                        <small style="color: var(--text-muted); font-size: 0.72rem; display: block; margin-top: 4px;">
                            Cukup inputkan angka capaian lapangan Anda.
                        </small>
                    </div>

                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: var(--radius-sm); padding: 10px 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.78rem; font-weight: 600; color: #475569;">
                                <i class="fa-solid fa-chart-pie" style="color: #2e4a16;"></i> Persentase Capaian:
                            </span>
                            <span id="submit_calc_percent_text" style="font-size: 1.1rem; font-weight: 800; color: #1e330c;">
                                0%
                            </span>
                        </div>
                        <div class="progress-bar-bg" style="height: 6px; margin-top: 6px; background-color: #f1f5f9;">
                            <div id="submit_calc_percent_bar" class="progress-bar-fill" style="width: 0%; transition: width 0.3s ease;"></div>
                        </div>
                        <div id="submit_target_info_text" style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">
                            Target kegiatan: -
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN UPLOAD BUKTI GAMBAR & DOKUMEN PENDUKUNG (OPSIONAL & BISA LEBIH DARI SATU) -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-paperclip" style="color: var(--primary-dark);"></i> Upload Gambar Bukti & Dokumen Pendukung
                    </div>
                    <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 0.72rem; border: 1px solid #e2e8f0;">
                        <i class="fa-regular fa-circle-question"></i> Opsional
                    </span>
                </div>
                <p style="font-size: 0.78rem; color: #64748b; margin: 0 0 14px 0; line-height: 1.4;">
                    Lampiran bersifat <strong>opsional</strong>. Anda dapat mengunggah satu atau <strong>lebih dari satu</strong> gambar bukti kegiatan dan dokumen pendukung, atau melanjutkan simpan tanpa lampiran.
                </p>

                <!-- Grid 2 Kolom untuk Area Upload -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <!-- Area Upload Gambar Bukti -->
                    <div style="border: 1px dashed #cbd5e1; border-radius: var(--radius-sm); padding: 14px; background: #fafafa; text-align: center; transition: all 0.2s ease;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; font-size: 1.1rem;">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 2px;">
                            Gambar Bukti Lapangan
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 10px;">
                            Pilih >1 foto (JPG, PNG, WEBP)
                        </div>
                        <input type="file" name="evidence_images[]" id="submit_evidence_images" accept="image/jpeg,image/png,image/webp,image/jpg" multiple style="display: none;" onchange="handleEvidenceSelect(this, 'submit', 'images')">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('submit_evidence_images').click()" style="width: 100%; font-size: 0.78rem; padding: 6px 12px; background: #ffffff;">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #7c3aed; margin-right: 4px;"></i> Pilih Gambar...
                        </button>
                    </div>

                    <!-- Area Upload Dokumen Pendukung -->
                    <div style="border: 1px dashed #cbd5e1; border-radius: var(--radius-sm); padding: 14px; background: #fafafa; text-align: center; transition: all 0.2s ease;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; font-size: 1.1rem;">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 2px;">
                            Dokumen Pendukung
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 10px;">
                            Pilih >1 berkas (PDF, Word, Excel, ZIP)
                        </div>
                        <input type="file" name="evidence_docs[]" id="submit_evidence_docs" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.rar,.7z,.ppt,.pptx,.txt" multiple style="display: none;" onchange="handleEvidenceSelect(this, 'submit', 'docs')">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('submit_evidence_docs').click()" style="width: 100%; font-size: 0.78rem; padding: 6px 12px; background: #ffffff;">
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb; margin-right: 4px;"></i> Pilih Dokumen...
                        </button>
                    </div>
                </div>

                <!-- Live Preview List Gambar yang Dipilih -->
                <div id="submit_images_preview_container" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-images" style="color: #7c3aed; margin-right: 4px;"></i> List Gambar yang Dipilih (<span id="submit_images_count">0</span>):</span>
                        <button type="button" onclick="clearEvidenceSelection('submit', 'images')" style="background: none; border: none; font-size: 0.72rem; color: #dc2626; cursor: pointer; text-decoration: underline;">
                            <i class="fa-solid fa-trash-can"></i> Hapus Semua Gambar
                        </button>
                    </div>
                    <div id="submit_images_preview_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px;"></div>
                </div>

                <!-- Live Preview List Dokumen yang Dipilih -->
                <div id="submit_docs_preview_container" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-file-lines" style="color: #2563eb; margin-right: 4px;"></i> List Dokumen yang Dipilih (<span id="submit_docs_count">0</span>):</span>
                        <button type="button" onclick="clearEvidenceSelection('submit', 'docs')" style="background: none; border: none; font-size: 0.72rem; color: #dc2626; cursor: pointer; text-decoration: underline;">
                            <i class="fa-solid fa-trash-can"></i> Hapus Semua Dokumen
                        </button>
                    </div>
                    <div id="submit_docs_preview_list" style="display: flex; flex-direction: column; gap: 6px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Deskripsi & Narasi Lapangan *</label>
                <textarea name="description" id="submit_description" class="form-control" rows="3" placeholder="Jelaskan pelaksanaan kegiatan, kehadiran peserta, kendala, atau capaian" required></textarea>
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

<script>
// Data kegiatan untuk autocomplete modal input laporan
window.availableActivitiesData = <?= json_encode(array_map(function($a) {
    return [
        'id' => (int)$a['activity_id'],
        'name' => (string)$a['activity_name'],
        'project_name' => (string)($a['project_name'] ?? ''),
        'project_code' => (string)($a['project_code'] ?? ''),
        'location' => (string)($a['location'] ?? ''),
        'total_target' => (float)($a['total_target'] ?? 0),
        'unit' => (string)($a['unit'] ?? 'Satuan')
    ];
}, $modalActivities), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

// DataTransfer holder untuk akumulasi file tanpa hilang saat user memilih berkali-kali
window.evidenceHolders = {
    submit: {
        imagesDT: new DataTransfer(),
        docsDT: new DataTransfer()
    },
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
    const dt = (type === 'images') ? window.evidenceHolders[prefix].imagesDT : window.evidenceHolders[prefix].docsDT;
    const newFiles = inputEl.files;

    for (let i = 0; i < newFiles.length; i++) {
        // Cek duplikasi nama dan ukuran
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
                <div style="width: 100%; height: 75px; border-radius: 4px; overflow: hidden; background: #f1f5f9; margin-bottom: 4px; position: relative;">
                    <img src="${thumbUrl}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${file.name}">
                    ${file.name}
                </div>
                <div style="color: #64748b; font-size: 0.68rem; margin-top: 2px;">
                    ${formatFileSizeJS(file.size)}
                </div>
                <button type="button" onclick="removeEvidenceFile('${prefix}', 'images', ${i})" style="position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; border-radius: 50%; background: #ef4444; color: #ffffff; border: 2px solid #ffffff; font-size: 0.65rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="Hapus gambar ini">&times;</button>
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

// Helper untuk membuka modal langsung dengan kegiatan yang sudah dipilih
window.openSubmitReportModalForActivity = function(act) {
    if (!act) return;
    
    const hiddenIdInput = document.getElementById('submit_activity_id');
    const searchInput = document.getElementById('submit_activity_search');
    const selectedBadge = document.getElementById('submit_activity_selected_badge');
    const selectedText = document.getElementById('submit_activity_selected_text');
    const clearBtn = document.getElementById('submit_activity_clear_btn');
    const helpText = document.getElementById('submit_activity_help_text');
    const realizationInput = document.getElementById('submit_realization_value');

    if (hiddenIdInput) hiddenIdInput.value = act.activity_id;
    if (searchInput) {
        searchInput.value = act.activity_name;
        searchInput.setCustomValidity('');
    }
    if (selectedBadge) selectedBadge.style.display = 'flex';
    if (selectedText) selectedText.textContent = act.activity_name + (act.project_name ? ' (' + act.project_name + ')' : '');
    if (clearBtn) clearBtn.style.display = 'block';
    if (helpText) helpText.style.display = 'none';

    // Tambahkan atau perbarui kegiatan di daftar autocomplete jika belum ada
    if (Array.isArray(window.availableActivitiesData)) {
        const found = window.availableActivitiesData.find(function(a) { return a.id === parseInt(act.activity_id); });
        if (!found) {
            window.availableActivitiesData.push({
                id: parseInt(act.activity_id),
                name: act.activity_name,
                project_name: act.project_name || '',
                project_code: act.project_code || '',
                total_target: parseFloat(act.total_target) || 0,
                unit: act.unit || 'Satuan'
            });
        }
    }

    // Set currentSelectedActivity pada scope autocomplete
    if (typeof window.setCurrentSelectedActivityJS === 'function') {
        window.setCurrentSelectedActivityJS(act);
    }

    if (realizationInput) {
        realizationInput.value = '';
    }

    // Reset file upload state
    clearEvidenceSelection('submit', 'images');
    clearEvidenceSelection('submit', 'docs');

    // Buka modal
    const modal = document.getElementById('modalSubmitReport');
    if (modal) {
        modal.style.display = 'flex';
    }
};

// Inisialisasi Autocomplete saat DOM Siap
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('submit_activity_search');
    const hiddenIdInput = document.getElementById('submit_activity_id');
    const dropdown = document.getElementById('submit_activity_dropdown');
    const clearBtn = document.getElementById('submit_activity_clear_btn');
    const selectedBadge = document.getElementById('submit_activity_selected_badge');
    const selectedText = document.getElementById('submit_activity_selected_text');
    const helpText = document.getElementById('submit_activity_help_text');
    const submitForm = searchInput ? searchInput.closest('form') : null;
    const realizationInput = document.getElementById('submit_realization_value');
    const percentText = document.getElementById('submit_calc_percent_text');
    const percentBar = document.getElementById('submit_calc_percent_bar');
    const targetInfoText = document.getElementById('submit_target_info_text');
    const unitBadge = document.getElementById('submit_unit_badge');

    let currentSelectedActivity = null;

    window.setCurrentSelectedActivityJS = function(act) {
        currentSelectedActivity = {
            id: parseInt(act.activity_id),
            name: act.activity_name,
            total_target: parseFloat(act.total_target) || 0,
            unit: act.unit || 'Satuan'
        };
        recalculateProgress();
    };

    function recalculateProgress() {
        if (!currentSelectedActivity) {
            if (percentText) percentText.textContent = '0%';
            if (percentBar) percentBar.style.width = '0%';
            if (targetInfoText) targetInfoText.textContent = 'Pilih kegiatan terlebih dahulu';
            if (unitBadge) unitBadge.textContent = '(Satuan)';
            return;
        }

        const target = parseFloat(currentSelectedActivity.total_target) || 0;
        const val = parseFloat(realizationInput ? realizationInput.value : 0) || 0;
        const unit = currentSelectedActivity.unit || 'Satuan';

        if (unitBadge) unitBadge.textContent = '(' + unit + ')';

        if (target > 0) {
            let percentage = (val / target) * 100;
            let displayPercent = Math.min(100, Math.max(0, percentage)).toFixed(1);
            if (displayPercent.endsWith('.0')) {
                displayPercent = parseInt(displayPercent);
            }
            if (percentText) {
                percentText.textContent = displayPercent + '%';
                if (percentage >= 100) {
                    percentText.style.color = '#15803d';
                } else {
                    percentText.style.color = '#1e330c';
                }
            }
            if (percentBar) {
                percentBar.style.width = Math.min(100, Math.max(0, percentage)) + '%';
            }
            if (targetInfoText) {
                targetInfoText.innerHTML = `Target: <strong>${target.toLocaleString('id-ID')} ${escapeHtml(unit)}</strong> (Realisasi: ${val.toLocaleString('id-ID')} ${escapeHtml(unit)})`;
            }
        } else {
            const percentage = val > 0 ? 100 : 0;
            if (percentText) percentText.textContent = percentage + '%';
            if (percentBar) percentBar.style.width = percentage + '%';
            if (targetInfoText) targetInfoText.textContent = 'Target kegiatan fleksibel (belum diset target angka)';
        }
    }

    if (realizationInput) {
        realizationInput.addEventListener('input', recalculateProgress);
    }

    function renderSuggestions(query) {
        if (!dropdown) return;
        const cleanQuery = (query || '').trim().toLowerCase();

        if (cleanQuery.length === 0) {
            dropdown.innerHTML = `
                <div style="padding: 12px 14px; font-size: 0.85rem; color: #64748b; text-align: center;">
                    <i class="fa-solid fa-keyboard" style="margin-right: 6px; color: #94a3b8;"></i>
                    Ketik nama kegiatan yang ingin dilaporkan
                </div>
            `;
            dropdown.style.display = 'block';
            return;
        }

        const activitiesList = window.availableActivitiesData || [];
        const filtered = activitiesList.filter(function(item) {
            const actName = (item.name || '').toLowerCase();
            const prjName = (item.project_name || '').toLowerCase();
            const prjCode = (item.project_code || '').toLowerCase();
            return actName.includes(cleanQuery) || prjName.includes(cleanQuery) || prjCode.includes(cleanQuery);
        });

        if (filtered.length === 0) {
            dropdown.innerHTML = `
                <div style="padding: 14px; font-size: 0.85rem; color: #dc2626; text-align: center; background: #fffaf0;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i>
                    Kegiatan tidak ditemukan
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Pastikan Anda ditugaskan pada proyek kegiatan tersebut.</div>
                </div>
            `;
            dropdown.style.display = 'block';
            return;
        }

        let html = '';
        filtered.forEach(function(item) {
            const escapedName = escapeHtml(item.name);
            const escapedProject = escapeHtml(item.project_name);
            const escapedCode = escapeHtml(item.project_code);

            html += `
                <div class="autocomplete-item" data-id="${item.id}" data-name="${escapedName}" data-project="${escapedProject}" style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.15s ease;">
                    <div style="font-weight: 600; color: #0f172a; font-size: 0.88rem; display: flex; align-items: center; justify-content: space-between;">
                        <span>${escapedName}</span>
                        ${item.location ? `<span style="font-size: 0.72rem; color: #64748b; font-weight: 400;"><i class="fa-solid fa-location-dot"></i> ${escapeHtml(item.location)}</span>` : ''}
                    </div>
                    ${item.project_name ? `
                        <div style="font-size: 0.75rem; color: #475569; margin-top: 2px;">
                            <span style="font-family: monospace; font-weight: 600; color: #2e4a16;">${escapedCode}</span> &bull; ${escapedProject}
                        </div>
                    ` : ''}
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.style.display = 'block';

        dropdown.querySelectorAll('.autocomplete-item').forEach(function(el) {
            el.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f4faeb';
            });
            el.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#ffffff';
            });
            el.addEventListener('click', function(e) {
                e.stopPropagation();
                selectActivity(this.getAttribute('data-id'), this.getAttribute('data-name'), this.getAttribute('data-project'));
            });
        });
    }

    function selectActivity(id, name, projectName) {
        if (!hiddenIdInput || !searchInput) return;
        hiddenIdInput.value = id;
        searchInput.value = name;
        searchInput.setCustomValidity('');

        const activitiesList = window.availableActivitiesData || [];
        currentSelectedActivity = activitiesList.find(function(a) { return a.id === parseInt(id); }) || null;

        if (selectedText) selectedText.textContent = name + (projectName ? ' (' + projectName + ')' : '');
        if (selectedBadge) selectedBadge.style.display = 'flex';
        if (clearBtn) clearBtn.style.display = 'block';
        if (dropdown) dropdown.style.display = 'none';
        if (helpText) helpText.style.display = 'none';

        recalculateProgress();
    }

    function clearSelection() {
        if (!hiddenIdInput || !searchInput) return;
        hiddenIdInput.value = '';
        searchInput.value = '';
        currentSelectedActivity = null;
        if (selectedBadge) selectedBadge.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';
        if (dropdown) dropdown.style.display = 'none';
        if (helpText) helpText.style.display = 'block';
        recalculateProgress();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (hiddenIdInput.value !== '') {
                hiddenIdInput.value = '';
                if (selectedBadge) selectedBadge.style.display = 'none';
                if (helpText) helpText.style.display = 'block';
            }

            if (this.value.trim() !== '') {
                if (clearBtn) clearBtn.style.display = 'block';
            } else {
                if (clearBtn) clearBtn.style.display = 'none';
            }
            renderSuggestions(this.value);
        });

        searchInput.addEventListener('focus', function() {
            renderSuggestions(this.value);
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            clearSelection();
            if (searchInput) searchInput.focus();
        });
    }

    document.addEventListener('click', function(e) {
        if (dropdown && searchInput && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
            if (hiddenIdInput && hiddenIdInput.value === '' && searchInput.value.trim() !== '') {
                searchInput.setCustomValidity('Pilih kegiatan dari daftar hasil pencarian.');
            }
        }
    });

    if (submitForm) {
        submitForm.addEventListener('submit', function(e) {
            if (!hiddenIdInput.value || parseInt(hiddenIdInput.value) <= 0) {
                e.preventDefault();
                searchInput.focus();
                searchInput.setCustomValidity('Silakan ketik dan pilih kegiatan yang valid dari hasil pencarian.');
                searchInput.reportValidity();
                renderSuggestions(searchInput.value);
                return false;
            }
            searchInput.setCustomValidity('');
        });
    }
});
</script>

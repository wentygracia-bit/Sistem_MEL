<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div style="margin-bottom: 20px;">
    <a href="<?= BASE_URL ?>/projects" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Proyek
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Form Tambah Proyek Baru</h2>
    </div>

    <form action="<?= BASE_URL ?>/projects/create" method="POST" style="width: 100%;">
        <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 24px; align-items: start;">
            
            <!-- KOLOM KIRI: INFORMASI PROYEK -->
            <div>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Kode Proyek *</label>
                        <input type="text" id="projectCodeInput" name="project_code" class="form-control" 
                               value="<?= htmlspecialchars($autoProjectCode ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               style="font-family: monospace; font-weight: 600; text-transform: uppercase;" 
                               placeholder="Contoh: PRJ-YNKI-2026-001-MGR" required>
                        <small style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px; display: block; line-height: 1.3;">
                            <i class="fa-solid fa-circle-info"></i> Kode otomatis dari sistem. Anda dapat menambahkan kata kunci proyek di belakangnya (misal: <code>-MGR</code>).
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Proyek / Program *</label>
                        <input type="text" name="project_name" class="form-control" placeholder="Nama program lengkap" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Lengkap</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Tujuan, sasaran wilayah, dan latar belakang program"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Status Proyek</label>
                        <select name="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Perencanaan">Perencanaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditunda">Ditunda</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Visibilitas Publik</label>
                        <select name="public_status" class="form-control">
                            <option value="Dipublikasikan">Dipublikasikan (Dapat dilihat umum di Portal Publik)</option>
                            <option value="Draft">Draft / Internal (Hanya internal tim)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: PENUGASAN TIM & ANGGOTA PROYEK -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <div style="margin-bottom: 16px;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 6px 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-users" style="color: var(--primary-dark, #274312);"></i> Penugasan Tim & Anggota Proyek
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted, #64748b); margin: 0; line-height: 1.4;">
                        Pilih Project Leader (PL) dan Staf Lapangan yang terlibat. Anggota yang dipilih akan memiliki hak akses penuh terhadap proyek ini.
                    </p>
                </div>

                <!-- 1. MULTI-SELECT DROPDOWN: PROJECT LEADER (PL) -->
                <div class="form-group" style="margin-bottom: 20px; position: relative;">
                    <label class="form-label" style="font-weight: 600; display: flex; align-items: center;">
                        <span><i class="fa-solid fa-user-tie" style="color: #0284c7; margin-right: 5px;"></i> Project Leader (PL)</span>
                    </label>

                    <!-- Trigger Input / Display Area -->
                    <div id="plDropdownTrigger" class="multi-select-box" tabindex="0">
                        <div id="plSelectedBadges" class="badges-container">
                            <span class="placeholder-text">-- Klik untuk memilih Project Leader --</span>
                        </div>
                        <div class="dropdown-arrow">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Dropdown Menu -->
                    <div id="plDropdownMenu" class="multi-select-menu" style="display: none;">
                        <?php if (empty($projectLeaders)): ?>
                            <div style="padding: 12px; font-size: 0.85rem; color: #94a3b8; text-align: center; font-style: italic;">
                                Belum ada pengguna aktif berstatus Project Leader.
                            </div>
                        <?php else: ?>
                            <div class="options-list">
                                <?php foreach ($projectLeaders as $pl): ?>
                                    <label class="dropdown-item-row" data-user-id="<?= (int)$pl['user_id'] ?>" data-name="<?= htmlspecialchars($pl['name']) ?>">
                                        <input type="checkbox" name="project_leaders[]" value="<?= (int)$pl['user_id'] ?>" class="pl-checkbox">
                                        <div class="user-meta">
                                            <div class="user-name"><?= htmlspecialchars($pl['name']) ?></div>
                                            <div class="user-sub"><?= htmlspecialchars($pl['email']) ?><?= !empty($pl['organization']) ? ' &bull; ' . htmlspecialchars($pl['organization']) : '' ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. MULTI-SELECT DROPDOWN DENGAN SEARCH: STAF LAPANGAN -->
                <div class="form-group" style="margin-bottom: 0; position: relative;">
                    <label class="form-label" style="font-weight: 600; display: flex; align-items: center;">
                        <span><i class="fa-solid fa-person-digging" style="color: #16a34a; margin-right: 5px;"></i> Staf Lapangan</span>
                    </label>

                    <!-- Trigger Input / Display Area -->
                    <div id="fieldStaffDropdownTrigger" class="multi-select-box" tabindex="0">
                        <div id="fieldStaffSelectedBadges" class="badges-container">
                            <span class="placeholder-text">-- Klik untuk memilih Staf Lapangan --</span>
                        </div>
                        <div class="dropdown-arrow">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Dropdown Menu with Search Input -->
                    <div id="fieldStaffDropdownMenu" class="multi-select-menu" style="display: none;">
                        <!-- Kotak Pencarian Nama -->
                        <div style="padding: 8px 10px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; position: sticky; top: 0; z-index: 2;">
                            <div style="position: relative;">
                                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 10px; color: #94a3b8; font-size: 0.85rem;"></i>
                                <input type="text" id="staffSearchInput" placeholder="Ketik untuk mencari nama atau email staf..." 
                                       style="width: 100%; padding: 6px 10px 6px 30px; font-size: 0.82rem; border: 1px solid #cbd5e1; border-radius: 4px; outline: none; background: #fff;" autocomplete="off">
                            </div>
                        </div>

                        <?php if (empty($fieldStaffs)): ?>
                            <div style="padding: 12px; font-size: 0.85rem; color: #94a3b8; text-align: center; font-style: italic;">
                                Belum ada pengguna aktif berstatus Staf Lapangan.
                            </div>
                        <?php else: ?>
                            <div class="options-list" id="staffOptionsList">
                                <?php foreach ($fieldStaffs as $staff): ?>
                                    <label class="dropdown-item-row staff-option-item" 
                                           data-user-id="<?= (int)$staff['user_id'] ?>" 
                                           data-search="<?= strtolower(htmlspecialchars($staff['name'] . ' ' . $staff['email'] . ' ' . ($staff['organization'] ?? ''))) ?>"
                                           data-name="<?= htmlspecialchars($staff['name']) ?>">
                                        <input type="checkbox" name="field_staffs[]" value="<?= (int)$staff['user_id'] ?>" class="staff-checkbox">
                                        <div class="user-meta">
                                            <div class="user-name"><?= htmlspecialchars($staff['name']) ?></div>
                                            <div class="user-sub"><?= htmlspecialchars($staff['email']) ?><?= !empty($staff['organization']) ? ' &bull; ' . htmlspecialchars($staff['organization']) : '' ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div id="noStaffFound" style="display: none; padding: 12px; font-size: 0.85rem; color: #94a3b8; text-align: center; font-style: italic;">
                                Tidak ada staf lapangan yang cocok dengan pencarian.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <style>
            .multi-select-box {
                min-height: 42px;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                background: #ffffff;
                padding: 6px 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .multi-select-box:focus, .multi-select-box.active {
                border-color: #274312;
                box-shadow: 0 0 0 2px rgba(39, 67, 18, 0.15);
                outline: none;
            }
            .badges-container {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                align-items: center;
                flex-grow: 1;
            }
            .placeholder-text {
                color: #94a3b8;
                font-size: 0.85rem;
                user-select: none;
            }
            .user-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: #eef2ff;
                color: #3730a3;
                border: 1px solid #c7d2fe;
                padding: 2px 8px;
                border-radius: 9999px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .user-badge.staff-badge {
                background: #f0fdf4;
                color: #166534;
                border-color: #bbf7d0;
            }
            .user-badge .remove-btn {
                cursor: pointer;
                color: inherit;
                opacity: 0.6;
                transition: opacity 0.15s;
                margin-left: 2px;
            }
            .user-badge .remove-btn:hover {
                opacity: 1;
            }
            .dropdown-arrow {
                color: #64748b;
                font-size: 0.8rem;
                transition: transform 0.2s;
                margin-left: 8px;
            }
            .multi-select-box.active .dropdown-arrow {
                transform: rotate(180deg);
            }
            .multi-select-menu {
                position: absolute;
                top: calc(100% + 4px);
                left: 0;
                right: 0;
                background: #ffffff;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                z-index: 100;
                max-height: 250px;
                display: flex;
                flex-direction: column;
            }
            .options-list {
                overflow-y: auto;
                max-height: 210px;
                padding: 4px;
            }
            .dropdown-item-row {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 10px;
                border-radius: 4px;
                cursor: pointer;
                transition: background 0.15s ease;
                margin-bottom: 2px;
                user-select: none;
            }
            .dropdown-item-row:hover {
                background: #f1f5f9;
            }
            .dropdown-item-row input[type="checkbox"] {
                cursor: pointer;
                width: 15px;
                height: 15px;
            }
            .user-meta .user-name {
                font-weight: 600;
                font-size: 0.86rem;
                color: #0f172a;
            }
            .user-meta .user-sub {
                font-size: 0.74rem;
                color: #64748b;
            }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper untuk menginisialisasi Multi-Select
            function setupMultiSelect(config) {
                const trigger = document.getElementById(config.triggerId);
                const menu = document.getElementById(config.menuId);
                const badgesContainer = document.getElementById(config.badgesId);
                const placeholder = config.placeholder;
                const badgeClass = config.badgeClass || '';
                const checkboxes = menu.querySelectorAll('input[type="checkbox"]');

                // Buka/Tutup Menu
                trigger.addEventListener('click', function (e) {
                    // Jangan toggle jika klik tombol silang (remove badge)
                    if (e.target.closest('.remove-btn')) return;
                    const isOpen = menu.style.display === 'block';
                    closeAllDropdowns();
                    if (!isOpen) {
                        menu.style.display = 'block';
                        trigger.classList.add('active');
                        if (config.searchInputId) {
                            setTimeout(() => {
                                const input = document.getElementById(config.searchInputId);
                                if (input) input.focus();
                            }, 50);
                        }
                    }
                });

                // Update Badges Tampilan
                function updateBadges() {
                    const selected = [];
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            const row = cb.closest('.dropdown-item-row');
                            selected.push({
                                id: cb.value,
                                name: row.getAttribute('data-name')
                            });
                        }
                    });

                    if (selected.length === 0) {
                        badgesContainer.innerHTML = `<span class="placeholder-text">${placeholder}</span>`;
                    } else {
                        badgesContainer.innerHTML = selected.map(item => `
                            <span class="user-badge ${badgeClass}">
                                ${item.name}
                                <span class="remove-btn" data-id="${item.id}" title="Hapus">&times;</span>
                            </span>
                        `).join('');

                        // Pasang event listener untuk remove badge
                        badgesContainer.querySelectorAll('.remove-btn').forEach(btn => {
                            btn.addEventListener('click', function (e) {
                                e.stopPropagation();
                                const idToRemove = this.getAttribute('data-id');
                                const targetCb = menu.querySelector(`input[type="checkbox"][value="${idToRemove}"]`);
                                if (targetCb) {
                                    targetCb.checked = false;
                                    updateBadges();
                                }
                            });
                        });
                    }
                }

                // Event listener checkbox saat dicentang / diubah
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateBadges);
                });

                // Inisialisasi awal
                updateBadges();
            }

            // Setup 1: Project Leader Multi-Select
            setupMultiSelect({
                triggerId: 'plDropdownTrigger',
                menuId: 'plDropdownMenu',
                badgesId: 'plSelectedBadges',
                placeholder: '-- Klik untuk memilih Project Leader --',
                badgeClass: ''
            });

            // Setup 2: Staf Lapangan Multi-Select (dengan Fitur Search)
            setupMultiSelect({
                triggerId: 'fieldStaffDropdownTrigger',
                menuId: 'fieldStaffDropdownMenu',
                badgesId: 'fieldStaffSelectedBadges',
                searchInputId: 'staffSearchInput',
                placeholder: '-- Klik untuk memilih Staf Lapangan --',
                badgeClass: 'staff-badge'
            });

            // Pencarian nama Staf Lapangan
            const searchInput = document.getElementById('staffSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const term = this.value.toLowerCase().trim();
                    const items = document.querySelectorAll('.staff-option-item');
                    let visibleCount = 0;

                    items.forEach(item => {
                        const searchData = item.getAttribute('data-search') || '';
                        if (searchData.includes(term)) {
                            item.style.display = 'flex';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    const noFound = document.getElementById('noStaffFound');
                    if (noFound) {
                        noFound.style.display = (visibleCount === 0) ? 'block' : 'none';
                    }
                });

                // Cegah penutupan menu saat klik pada input search
                searchInput.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            // Fungsi menutup seluruh dropdown
            function closeAllDropdowns() {
                const plMenu = document.getElementById('plDropdownMenu');
                const plTrig = document.getElementById('plDropdownTrigger');
                const staffMenu = document.getElementById('fieldStaffDropdownMenu');
                const staffTrig = document.getElementById('fieldStaffDropdownTrigger');

                if (plMenu) plMenu.style.display = 'none';
                if (plTrig) plTrig.classList.remove('active');
                if (staffMenu) staffMenu.style.display = 'none';
                if (staffTrig) staffTrig.classList.remove('active');
            }

            // Tutup dropdown jika klik di luar area
            document.addEventListener('click', function (e) {
                const insidePl = e.target.closest('#plDropdownTrigger') || e.target.closest('#plDropdownMenu');
                const insideStaff = e.target.closest('#fieldStaffDropdownTrigger') || e.target.closest('#fieldStaffDropdownMenu');
                if (!insidePl && !insideStaff) {
                    closeAllDropdowns();
                }
            });
        });
        </script>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Proyek
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

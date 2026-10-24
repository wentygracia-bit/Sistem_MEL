<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.notif-card {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 20px;
    border-radius: var(--radius-md);
    position: relative;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.notif-card.unread {
    background: #f0f7e8;
    border: 1px solid #a4be75;
    box-shadow: 0 2px 8px rgba(181, 206, 136, 0.25);
}
.notif-card.unread:hover {
    background: #e6f2da;
    border-color: #8fae5c;
    box-shadow: 0 6px 16px rgba(181, 206, 136, 0.4);
    transform: translateY(-2px);
}
.notif-card.read {
    background: #ffffff;
    border: 1px solid var(--border-color);
    box-shadow: none;
}
.notif-card.read:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}
.notif-card:focus-visible {
    outline: 2px solid var(--primary-dark);
    outline-offset: 2px;
}
</style>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fa-solid fa-bell" style="color: var(--primary-dark);"></i> Notifikasi & Pengingat Jadwal (Reminders)</h2>
        <div>
            <a href="<?= BASE_URL ?>/notifications/read?id=0" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-check-double"></i> Tandai Semua Sudah Dibaca
            </a>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php if (empty($notifications)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada notifikasi saat ini.</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <?php
                // Tentukan target URL spesifik berdasarkan kategori dan konteks notifikasi
                $redirectUrl = '';
                if ($n['notification_type'] === 'Pengesahan MEL') {
                    if (!empty($n['mel_result_id'])) {
                        $redirectUrl = '/mel-results/detail?id=' . $n['mel_result_id'];
                    } else {
                        $redirectUrl = '/mel-results';
                    }
                } elseif ($n['notification_type'] === 'Keputusan Direktur') {
                    if (!empty($n['mel_result_id'])) {
                        $redirectUrl = '/mel-results/detail?id=' . $n['mel_result_id'];
                    } else {
                        $redirectUrl = '/mel-results';
                    }
                } elseif ($n['notification_type'] === 'Permintaan Perbaikan' || $n['notification_type'] === 'Tanggapan Perbaikan') {
                    if (!empty($n['correction_id'])) {
                        $redirectUrl = '/corrections/detail?id=' . $n['correction_id'];
                    } else {
                        $redirectUrl = '/corrections';
                    }
                } elseif ($n['notification_type'] === 'Laporan Baru') {
                    if (!empty($n['update_id'])) {
                        $redirectUrl = '/field-updates/detail?id=' . $n['update_id'];
                    } else {
                        $redirectUrl = '/field-updates';
                    }
                } elseif ($n['notification_type'] === 'Reminder') {
                    $redirectUrl = ($currentUser['role_id'] == 4) ? '/evaluations' : (($currentUser['role_id'] == 1) ? '/field-updates' : '/projects');
                }

                // Rute klik selalu melalui /notifications/read agar status di database langsung terupdate menjadi sudah dibaca
                $cardUrl = BASE_URL . '/notifications/read?id=' . $n['notification_id'];
                if (!empty($redirectUrl)) {
                    $cardUrl .= '&redirect=' . urlencode($redirectUrl);
                }
                ?>
                <a href="<?= $cardUrl ?>" class="notif-card <?= $n['is_read'] ? 'read' : 'unread' ?>" title="Buka detail notifikasi">
                    <div style="display: flex; gap: 14px; align-items: flex-start; flex: 1;">
                        <div style="position: relative;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; color: #2e4a16; border: 1px solid <?= $n['is_read'] ? '#e2e8f0' : '#b5ce88' ?>; box-shadow: var(--shadow-sm); flex-shrink: 0;">
                                <?php if ($n['notification_type'] === 'Reminder'): ?>
                                    <i class="fa-solid fa-clock" style="color: #d97706;"></i>
                                <?php elseif ($n['notification_type'] === 'Permintaan Perbaikan'): ?>
                                    <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i>
                                <?php elseif ($n['notification_type'] === 'Pengesahan MEL'): ?>
                                    <i class="fa-solid fa-stamp" style="color: #1e3a8a;"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-bell" style="color: #15803d;"></i>
                                <?php endif; ?>
                            </div>
                            <?php if (!$n['is_read']): ?>
                                <span style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; border-radius: 50%; background: #ef4444; border: 2px solid #ffffff;" title="Belum dibaca"></span>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <?php if (!$n['is_read']): ?>
                                    <span class="badge" style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Belum Dibaca
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                                        <i class="fa-solid fa-check"></i> Sudah Dibaca
                                    </span>
                                <?php endif; ?>

                                <span class="badge <?= $n['notification_type'] === 'Reminder' ? 'badge-warning' : ($n['notification_type'] === 'Permintaan Perbaikan' ? 'badge-danger' : ($n['notification_type'] === 'Pengesahan MEL' ? 'badge-primary' : 'badge-info')) ?>">
                                    <?= htmlspecialchars($n['notification_type']) ?>
                                </span>

                                <?php if (!empty($n['activity_name'])): ?>
                                    <span style="font-size: 0.85rem; font-weight: <?= $n['is_read'] ? '600' : '700' ?>; color: #1e293b;">
                                        Kegiatan: <?= htmlspecialchars($n['activity_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div style="font-size: 0.92rem; color: <?= $n['is_read'] ? '#475569' : '#0f172a' ?>; font-weight: <?= $n['is_read'] ? '400' : '600' ?>; margin-top: 8px; line-height: 1.5;">
                                <?= htmlspecialchars($n['message']) ?>
                            </div>

                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($n['sent_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div style="flex-shrink: 0; margin-left: 10px; display: flex; align-items: center;">
                        <span style="font-size: 0.85rem; color: var(--text-muted); padding: 6px 10px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; gap: 6px;">
                            <?php if (!$n['is_read']): ?>
                                <i class="fa-solid fa-chevron-right" style="color: var(--primary-dark); font-size: 0.8rem;"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-check-double" style="color: #10b981; font-size: 0.8rem;"></i>
                            <?php endif; ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

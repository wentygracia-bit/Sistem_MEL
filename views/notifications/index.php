<?php
require_once __DIR__ . '/../layouts/header.php';
?>

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
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; padding: 16px 20px; border-radius: var(--radius-md); background: <?= $n['is_read'] ? '#ffffff' : '#f0f7e8' ?>; border: 1px solid <?= $n['is_read'] ? 'var(--border-color)' : '#a4be75' ?>; position: relative; box-shadow: <?= $n['is_read'] ? 'none' : '0 2px 8px rgba(181, 206, 136, 0.25)' ?>; transition: all 0.2s ease;">
                    <div style="display: flex; gap: 14px; align-items: flex-start;">
                        <div style="position: relative;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; color: #2e4a16; border: 1px solid <?= $n['is_read'] ? '#e2e8f0' : '#b5ce88' ?>; box-shadow: var(--shadow-sm); flex-shrink: 0;">
                                <?php if ($n['notification_type'] === 'Reminder'): ?>
                                    <i class="fa-solid fa-clock" style="color: #d97706;"></i>
                                <?php elseif ($n['notification_type'] === 'Permintaan Perbaikan'): ?>
                                    <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-bell" style="color: #15803d;"></i>
                                <?php endif; ?>
                            </div>
                            <?php if (!$n['is_read']): ?>
                                <span style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; border-radius: 50%; background: #ef4444; border: 2px solid #ffffff;" title="Belum dibaca"></span>
                            <?php endif; ?>
                        </div>
                        <div>
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

                                <span class="badge <?= $n['notification_type'] === 'Reminder' ? 'badge-warning' : ($n['notification_type'] === 'Permintaan Perbaikan' ? 'badge-danger' : 'badge-info') ?>">
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

                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">
                                <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($n['sent_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div style="flex-shrink: 0; margin-left: 10px;">
                        <?php if (!$n['is_read']): ?>
                            <a href="<?= BASE_URL ?>/notifications/read?id=<?= $n['notification_id'] ?>" class="btn btn-primary btn-sm" title="Tandai Sudah Dibaca">
                                <i class="fa-solid fa-check"></i> Tandai Sudah Dibaca
                            </a>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Terbaca
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>

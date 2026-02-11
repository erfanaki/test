<?php
/**
 * ویوی اعلان‌ها
 */
?>

<div class="grid-2">
    <!-- ستون چپ: فرم یادداشت -->
    <div>
        <div class="table-container animate-fadeIn">
            <div class="table-header">
                <span class="table-title"><!-- آیکون --> ثبت یادداشت / یادآوری جدید</span>
            </div>
            <div style="padding:20px;">
                <form method="POST" action="<?= BASE_URL ?>notifications">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label>عنوان *</label>
                        <input type="text" name="title" class="form-control" required placeholder="عنوان یادداشت">
                    </div>
                    <div class="form-group">
                        <label>متن پیام</label>
                        <textarea name="message" class="form-control" rows="4" placeholder="متن یادداشت یا یادآوری خود را بنویسید..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>نوع</label>
                            <select name="type" class="form-control">
                                <option value="note">یادداشت</option>
                                <option value="reminder">یادآوری</option>
                                <option value="debt">بدهی</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>تاریخ یادآوری</label>
                            <div class="date-picker-wrapper">
                                <input type="text" name="remind_date" class="form-control datepicker-input" readonly
                                       placeholder="<?= toFarsiNumber(todayJalali()) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>مرتبط با کارمند</label>
                        <select name="related_employee_id" class="form-control">
                            <option value="">-- بدون ارتباط --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>"><?= $emp['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">ثبت یادداشت</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ستون راست: یادآوری‌های امروز -->
    <div>
        <?php if (!empty($todayReminders)): ?>
            <div class="table-container animate-slideUp mb-3" style="border:1px solid var(--accent-warning);">
                <div class="table-header" style="background:rgba(243,156,18,0.1);">
                    <span class="table-title" style="color:var(--accent-warning);"><!-- آیکون زنگ --> یادآوری‌های امروز (<?= toFarsiNumber(count($todayReminders)) ?>)</span>
                </div>
                <div style="padding:12px;">
                    <?php foreach ($todayReminders as $rem): ?>
                        <div style="padding:10px;margin-bottom:8px;background:var(--bg-input);border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <strong style="color:var(--accent-warning);"><?= $rem['title'] ?></strong>
                                <?php if ($rem['message']): ?>
                                    <p style="margin:4px 0 0;font-size:12px;color:var(--text-secondary);"><?= $rem['message'] ?></p>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-sm btn-success" onclick="markDone(<?= $rem['id'] ?>)">&#10003; انجام شد</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- لیست اعلان‌ها -->
        <div class="table-container animate-fadeIn">
            <div class="table-header">
                <span class="table-title">همه اعلان‌ها (<?= toFarsiNumber(count($notifications)) ?>)</span>
                <span class="badge badge-danger"><?= toFarsiNumber($unreadCount) ?> خوانده نشده</span>
            </div>
            <div style="max-height:500px;overflow-y:auto;">
                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">&#9888;</div>
                        <p class="empty-state-text">اعلانی وجود ندارد</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <?php
                        $typeColors = ['note' => 'var(--accent-info)', 'reminder' => 'var(--accent-warning)', 'debt' => 'var(--accent-danger)', 'system' => 'var(--accent-purple)'];
                        $typeLabels = ['note' => 'یادداشت', 'reminder' => 'یادآوری', 'debt' => 'بدهی', 'system' => 'سیستم'];
                        $bgStyle = $notif['is_read'] ? '' : 'background:rgba(233,69,96,0.05);';
                        ?>
                        <div style="padding:14px 16px;border-bottom:1px solid var(--border-light);<?= $bgStyle ?>display:flex;justify-content:space-between;align-items:flex-start;">
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:<?= $typeColors[$notif['type']] ?? 'var(--accent-info)' ?>;"></span>
                                    <strong style="font-size:13px;"><?= $notif['title'] ?></strong>
                                    <span class="badge" style="font-size:10px;background:rgba(0,0,0,0.1);color:var(--text-secondary);"><?= $typeLabels[$notif['type']] ?? '' ?></span>
                                    <?php if (!$notif['is_read']): ?>
                                        <span class="badge badge-danger" style="font-size:9px;">جدید</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($notif['message']): ?>
                                    <p style="font-size:12px;color:var(--text-secondary);margin:0;"><?= $notif['message'] ?></p>
                                <?php endif; ?>
                                <?php if ($notif['employee_name']): ?>
                                    <span style="font-size:11px;color:var(--accent-primary);">مرتبط: <?= $notif['employee_name'] ?></span>
                                <?php endif; ?>
                                <?php if ($notif['remind_date']): ?>
                                    <span style="font-size:11px;color:var(--text-muted);margin-right:8px;">تاریخ: <?= toFarsiNumber($notif['remind_date']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-1 no-print">
                                <?php if (!$notif['is_done']): ?>
                                    <button class="btn btn-sm btn-success" onclick="markDone(<?= $notif['id'] ?>)" title="انجام شد">&#10003;</button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteNotif(<?= $notif['id'] ?>)" title="حذف">&times;</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.datepicker-input').forEach(function(input) {
        input.addEventListener('click', function() { JalaliDatePicker.init(this); });
    });
});

function markDone(id) {
    ajaxPost('<?= BASE_URL ?>notifications', {action:'mark_done', id:id}, function(err, res) {
        if (res && res.success) {
            showNotification('انجام شد', 'success');
            setTimeout(function() { location.reload(); }, 800);
        }
    });
}

function deleteNotif(id) {
    showConfirm('حذف شود؟', function() {
        ajaxPost('<?= BASE_URL ?>notifications', {action:'delete', id:id}, function(err, res) {
            if (res && res.success) {
                showNotification('حذف شد', 'success');
                setTimeout(function() { location.reload(); }, 800);
            }
        });
    });
}
</script>
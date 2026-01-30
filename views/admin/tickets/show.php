<?php
/**
 * صفحة عرض تفاصيل التذكرة
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'tickets';
ob_start();

$statusLabels = [
    'new' => 'جديدة',
    'received' => 'مستلمة',
    'in_progress' => 'قيد العمل',
    'fixed' => 'تم الإصلاح',
    'closed' => 'مغلقة',
];

$statusClasses = [
    'new' => 'badge-new',
    'received' => 'badge-working',
    'in_progress' => 'badge-working',
    'fixed' => 'glass-badge',
    'closed' => 'badge-closed',
];

$actionLabels = [
    'created' => 'تم إنشاء التذكرة',
    'status_changed' => 'تغيير الحالة',
    'assigned' => 'تم التعيين',
    'note_added' => 'ملاحظة جديدة',
    'report_uploaded' => 'رفع تقرير',
];

// منطق تتبع الحالة (Stepper Logic)
$steps = ['new', 'received', 'in_progress', 'fixed', 'closed'];
$currentStepIndex = array_search($ticket['status'], $steps);
?>

<div style="margin-bottom: 2rem;">
    <a href="<?= BASE_URL ?>/admin/tickets" class="link-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="fas fa-arrow-right"></i> العودة لقائمة التذاكر
    </a>
</div>

<!-- شريط الحالة (Stepper) -->
<div class="glass-card" style="margin-bottom: 2rem; padding: 2rem;">
    <div class="status-stepper">
        <?php foreach ($steps as $index => $step): ?>
            <?php 
                $isCompleted = $index < $currentStepIndex;
                $isActive = $index === $currentStepIndex;
                $stepClass = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
            ?>
            <div class="step-item <?= $stepClass ?>">
                <div class="step-circle">
                    <?php if ($isCompleted): ?>
                        <i class="fas fa-check"></i>
                    <?php else: ?>
                        <?= $index + 1 ?>
                    <?php endif; ?>
                </div>
                <div class="step-label"><?= $statusLabels[$step] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- العمود الأيمن: التفاصيل والسجل -->
    <div>
        <!-- تفاصيل التذكرة -->
        <div class="glass-card table-responsive" style="margin-bottom: 2rem;">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-info-circle section-icon" style="color: var(--neon-blue);"></i>
                    <span>تفاصيل التذكرة #<?= htmlspecialchars($ticket['ticket_number']) ?></span>
                </div>
                <span class="glass-badge <?= $statusClasses[$ticket['status']] ?? 'glass-badge' ?>">
                    <?= $statusLabels[$ticket['status']] ?>
                </span>
            </div>
            
            <table class="modern-table">
                <tr>
                    <td style="width: 150px; color: #94a3b8;">العميل</td>
                    <td style="font-weight: 600; color: #fff;"><?= htmlspecialchars($ticket['customer_name']) ?></td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">رقم الهاتف</td>
                    <td>
                        <a href="tel:<?= $ticket['customer_phone'] ?>" class="link-primary" style="font-family: monospace;">
                            <?= htmlspecialchars($ticket['customer_phone']) ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">العنوان</td>
                    <td><?= htmlspecialchars($ticket['customer_address']) ?></td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">الجهاز</td>
                    <td>
                        <?= $ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة' ?>
                        <?php if($ticket['machine_model']): ?>
                            - <span style="color: var(--neon-blue);"><?= htmlspecialchars($ticket['machine_model']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($ticket['error_code']): ?>
                <tr>
                    <td style="color: #94a3b8;">كود الخطأ</td>
                    <td><span class="badge-danger-soft glass-badge"><?= htmlspecialchars($ticket['error_code']) ?></span></td>
                </tr>
                <?php endif; ?>
            </table>
            
            <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid var(--glass-border);">
                <h4 style="margin: 0 0 1rem 0; color: #cbd5e1;">وصف العطل</h4>
                <div style="line-height: 1.6; color: #e2e8f0;">
                    <?= nl2br(htmlspecialchars($ticket['fault_description'])) ?>
                </div>
                <?php if ($ticket['fault_type'] === 'repeated'): ?>
                <div style="margin-top: 1rem; color: #ef4444; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    هذا عطل متكرر (<?= $ticket['repeat_count'] ?> مرات)
                </div>
                <?php endif; ?>
            </div>

            <!-- الصور المرفقة -->
            <?php if ($ticket['machine_model_image'] || $ticket['screen_image']): ?>
            <div style="margin-top: 2rem;">
                <h4 style="margin: 0 0 1rem 0; color: #cbd5e1; font-size: 1rem;">الصور المرفقة</h4>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <?php if ($ticket['machine_model_image']): ?>
                    <div style="cursor: pointer;" onclick="openLightbox('<?= BASE_URL ?>/storage/uploads/<?= $ticket['machine_model_image'] ?>')">
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= $ticket['machine_model_image'] ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 1px solid var(--glass-border);" alt="صورة الموديل">
                    </div>
                    <?php endif; ?>
                    <?php if ($ticket['screen_image']): ?>
                    <div style="cursor: pointer;" onclick="openLightbox('<?= BASE_URL ?>/storage/uploads/<?= $ticket['screen_image'] ?>')">
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= $ticket['screen_image'] ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 1px solid var(--glass-border);" alt="صورة الشاشة">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- السجل الزمني (Timeline) -->
        <div class="glass-card">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-history section-icon" style="color: #a78bfa;"></i>
                    <span>سجل النشاطات</span>
                </div>
            </div>
            
            <div class="timeline-container">
                <?php if (empty($timeline)): ?>
                <p style="text-align: center; color: #64748b; padding: 2rem;">لا توجد نشاطات مسجلة بعد</p>
                <?php else: ?>
                <?php foreach ($timeline as $event): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--neon-blue);"><?= $actionLabels[$event['action']] ?? $event['action'] ?></strong>
                            <span style="color: #64748b; font-size: 0.8rem;"><?= date('Y-m-d H:i', strtotime($event['created_at'])) ?></span>
                        </div>
                        
                        <?php if ($event['old_value'] && $event['new_value']): ?>
                        <div style="font-size: 0.9rem; color: #cbd5e1; display: flex; align-items: center; gap: 8px;">
                            <span class="glass-badge" style="font-size: 0.75rem;"><?= $statusLabels[$event['old_value']] ?? $event['old_value'] ?></span>
                            <i class="fas fa-arrow-left" style="color: #64748b; font-size: 0.8rem;"></i>
                            <span class="glass-badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.75rem;"><?= $statusLabels[$event['new_value']] ?? $event['new_value'] ?></span>
                        </div>
                        <?php elseif ($event['new_value']): ?>
                        <div style="font-size: 0.9rem; color: #cbd5e1;">
                            <?= htmlspecialchars($event['new_value']) ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($event['notes']): ?>
                        <div style="margin-top: 0.8rem; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 6px; font-size: 0.9rem; color: #94a3b8; border-right: 2px solid var(--neon-blue);">
                            <?= htmlspecialchars($event['notes']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event['user_name']): ?>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b; text-align: left;">
                            بواسطة: <?= htmlspecialchars($event['user_name']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- العمود الأيسر: الإجراءات -->
    <div>
        <div class="glass-card" style="position: sticky; top: 100px;">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-cogs section-icon"></i>
                    <span>الإجراءات</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- زر تغيير الحالة -->
                <button onclick="openModal('statusModal')" class="btn-glass-primary" style="justify-content: center;">
                    <i class="fas fa-sync-alt"></i> تغيير الحالة
                </button>

                <!-- زر تعيين فني -->
                <button onclick="openModal('assignModal')" class="btn-glass-primary" style="justify-content: center; background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <i class="fas fa-user-cog"></i> تعيين فني
                </button>

                <!-- زر إضافة ملاحظة -->
                <button onclick="openModal('noteModal')" class="btn-glass-primary" style="justify-content: center; background: linear-gradient(135deg, #059669, #10b981);">
                    <i class="fas fa-sticky-note"></i> إضافة ملاحظة
                </button>
            </div>

            <div class="dropdown-divider" style="margin: 1.5rem 0;"></div>

            <div class="section-header" style="margin-bottom: 1rem;">
                <div class="section-title" style="font-size: 1rem;">
                    <i class="fas fa-file-pdf section-icon" style="color: #f43f5e;"></i>
                    <span>التقارير</span>
                </div>
            </div>

            <?php if ($ticket['repair_report']): ?>
            <a href="<?= BASE_URL ?>/storage/uploads/<?= $ticket['repair_report'] ?>" target="_blank" class="btn-glass-primary" style="display: flex; justify-content: center; margin-bottom: 1rem; background: rgba(30, 41, 59, 0.6); border: 1px solid var(--glass-border);">
                <i class="fas fa-download"></i> تحميل التقرير الحالي
            </a>
            <?php endif; ?>

            <input type="file" id="reportFile" accept=".pdf" style="display: none;" onchange="uploadReport()">
            <button onclick="document.getElementById('reportFile').click()" class="btn-glass-primary" style="width: 100%; justify-content: center; background: transparent; border: 1px dashed var(--glass-border); color: #94a3b8;">
                <i class="fas fa-upload"></i> رفع تقرير جديد
            </button>
        </div>
        
        <!-- معلومات المسؤول -->
        <?php if($ticket['assigned_name']): ?>
        <div class="glass-card" style="margin-top: 1.5rem; text-align: center;">
            <div class="user-avatar" style="width: 60px; height: 60px; font-size: 1.5rem; margin: 0 auto 1rem auto; background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                <?= mb_substr($ticket['assigned_name'], 0, 1) ?>
            </div>
            <h4 style="margin: 0; color: #fff;"><?= htmlspecialchars($ticket['assigned_name']) ?></h4>
            <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 0.9rem;">الفني المسؤول</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals -->

<!-- تغيير الحالة -->
<div id="statusModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">تحديث حالة التذكرة</div>
            <button class="modal-close" onclick="closeModal('statusModal')"><i class="fas fa-times"></i></button>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.5rem;">
            <?php foreach ($statusLabels as $status => $label): ?>
            <button onclick="setStatus('<?= $status ?>')" class="btn-glass-primary" style="justify-content: center; <?= $ticket['status'] === $status ? 'opacity: 0.5; cursor: not-allowed;' : 'background: rgba(30, 41, 59, 0.6); border: 1px solid var(--glass-border);' ?>">
                <?= $label ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- تعيين فني -->
<div id="assignModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">تعيين فني للعمل</div>
            <button class="modal-close" onclick="closeModal('assignModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <select id="technicianSelect" class="search-input" style="width: 100%;">
                <option value="" style="color: #000;">اختر فني...</option>
                <?php foreach ($technicians as $tech): ?>
                <option value="<?= $tech['id'] ?>" style="color: #000;" <?= $ticket['assigned_to'] == $tech['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tech['full_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button onclick="submitAssign()" class="btn-glass-primary" style="width: 100%; justify-content: center;">حفظ</button>
    </div>
</div>

<!-- إضافة ملاحظة -->
<div id="noteModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">إضافة ملاحظة جديدة</div>
            <button class="modal-close" onclick="closeModal('noteModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <textarea id="noteText" class="search-input" rows="4" style="width: 100%;" placeholder="اكتب ملاحظاتك هنا..."></textarea>
        </div>
        <button onclick="submitNote()" class="btn-glass-primary" style="width: 100%; justify-content: center;">إضافة الملاحظة</button>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox-overlay" onclick="closeLightbox()">
    <button class="lightbox-close">&times;</button>
    <div class="lightbox-content">
        <img id="lightboxImage" src="" alt="Full view">
    </div>
</div>

<script>
const ticketId = <?= $ticket['id'] ?>;
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// Modal Functions
function openModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'flex';
    modal.offsetHeight; // force reflow
    modal.classList.add('show');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

// Lightbox
function openLightbox(src) {
    document.getElementById('lightboxImage').src = src;
    document.getElementById('lightbox').style.display = 'flex';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

// Actions
function setStatus(status) {
    const notes = prompt('هل تريد إضافة ملاحظة عند تغيير الحالة؟ (اختياري)');
    
    fetch('<?= BASE_URL ?>/admin/tickets/update-status/' + ticketId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `status=${status}&notes=${notes || ''}&<?= CSRF_TOKEN_NAME ?>=${csrfToken}`
    }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error);
    });
}

function submitAssign() {
    const techId = document.getElementById('technicianSelect').value;
    if(!techId) return alert('الرجاء اختيار فني');
    
    fetch('<?= BASE_URL ?>/admin/tickets/assign/' + ticketId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `technician_id=${techId}&<?= CSRF_TOKEN_NAME ?>=${csrfToken}`
    }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error);
    });
}

function submitNote() {
    const notes = document.getElementById('noteText').value;
    if(!notes) return alert('الرجاء كتابة ملاحظة');
    
    fetch('<?= BASE_URL ?>/admin/tickets/add-note/' + ticketId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `notes=${encodeURIComponent(notes)}&<?= CSRF_TOKEN_NAME ?>=${csrfToken}`
    }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error);
    });
}

function uploadReport() {
    const file = document.getElementById('reportFile').files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('report', file);
    
    fetch('<?= BASE_URL ?>/admin/tickets/upload-report/' + ticketId, {
        method: 'POST',
        body: formData
    }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.error);
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

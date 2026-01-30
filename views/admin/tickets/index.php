<?php
/**
 * صفحة إدارة التذاكر
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
    'fixed' => 'glass-badge', /* Default green from style */
    'closed' => 'badge-closed',
];

use App\Services\Auth;
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-ticket-alt" style="color: #60a5fa;"></i>
        إدارة التذاكر
        <span class="glass-badge" style="margin-right: 15px; font-size: 0.85rem; padding: 5px 12px; border: 1px solid rgba(59, 130, 246, 0.3);"><?= $total ?> تذكرة</span>
    </h1>
    <div class="search-container" style="width: 300px;">
        <form action="" method="GET" style="width: 100%; position: relative;">
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="بحث عن تذكرة، عميل..." class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); color: #fff; padding-right: 40px; width: 100%;">
            <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<!-- Bulk Actions Toolbar -->
<div id="bulk-actions" class="glass-card" style="margin-bottom: 2rem; padding: 0.75rem 1.5rem; display: none; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="color: #fca5a5; font-weight: 600;">تم تحديد <span id="selected-count">0</span> تذكرة</span>
        <button onclick="bulkDelete()" class="btn-glass-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-trash-alt"></i> حذف المحدد
        </button>
    </div>
    <button onclick="deselectAll()" style="background: none; border: none; color: #cbd5e1; cursor: pointer; opacity: 0.7; hover: opacity: 1;">
        إلغاء التحديد
    </button>
</div>

<!-- فلاتر الحالة -->
<div class="glass-card" style="margin-bottom: 2rem; padding: 0.5rem; display: inline-flex; background: rgba(30, 41, 59, 0.5); border-radius: 12px; gap: 4px;">
    <a href="<?= BASE_URL ?>/admin/tickets" 
       style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; transition: all 0.3s; <?= !$currentStatus ? 'background: var(--neon-blue); color: white; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); font-weight: 600;' : 'color: #94a3b8; hover: background: rgba(255,255,255,0.05);' ?>">
        الكل
    </a>
    <?php foreach ($statusLabels as $status => $label): ?>
    <a href="<?= BASE_URL ?>/admin/tickets?status=<?= $status ?>" 
       style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; transition: all 0.3s; <?= $currentStatus === $status ? 'background: var(--neon-blue); color: white; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); font-weight: 600;' : 'color: #94a3b8;' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="glass-card table-responsive">
    <table class="modern-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">
                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--neon-blue);">
                </th>
                <th style="width: 12%;">رقم التذكرة</th>
                <th style="width: 20%;">العميل</th>
                <th style="width: 15%;">الهاتف</th>
                <th style="width: 13%;">الجهاز</th>
                <th style="width: 10%; text-align: center;">الحالة</th>
                <th style="width: 15%; text-align: center;">الفني</th>
                <th style="width: 10%; text-align: center;">التاريخ</th>
                <th style="width: 5%; text-align: center;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <br>
                    لا توجد تذاكر مطابقة
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td style="text-align: center;">
                    <input type="checkbox" class="ticket-checkbox" value="<?= $ticket['id'] ?>" onchange="updateBulkActions()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--neon-blue);">
                </td>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <a href="<?= BASE_URL ?>/admin/tickets/show/<?= $ticket['id'] ?>" class="link-primary" style="font-weight: 700; font-family: monospace; font-size: 0.95rem;">
                            #<?= htmlspecialchars($ticket['ticket_number']) ?>
                        </a>
                        <?php if ($ticket['fault_type'] === 'repeated'): ?>
                        <div style="display: flex; justify-content: center; width: 100%;">
                            <span class="glass-badge badge-danger-soft" style="font-size: 0.75rem; margin-top: 6px; width: fit-content; padding: 4px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                متكرر 
                                <?php if(isset($ticket['repetition_count']) && $ticket['repetition_count'] > 1): ?>
                                <span style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.7em;">
                                    <?= $ticket['repetition_count'] ?>
                                </span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <span style="font-weight: 600; color: #f1f5f9; display: block; margin-bottom: 4px;"><?= htmlspecialchars($ticket['customer_name']) ?></span>
                </td>
                <td>
                    <span style="font-family: monospace; color: #cbd5e1; background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px;">
                        <?= htmlspecialchars($ticket['customer_phone']) ?>
                    </span>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <?php if ($ticket['machine_type'] === 'copier'): ?>
                            <div style="background: rgba(96, 165, 250, 0.1); padding: 6px; border-radius: 6px; color: #60a5fa;">
                                <i class="fas fa-copy"></i>
                            </div>
                            <span style="font-size: 0.9rem;">آلة تصوير</span>
                        <?php else: ?>
                            <div style="background: rgba(167, 139, 250, 0.1); padding: 6px; border-radius: 6px; color: #a78bfa;">
                                <i class="fas fa-print"></i>
                            </div>
                            <span style="font-size: 0.9rem;">طابعة</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="text-align: center;">
                    <span class="glass-badge <?= $statusClasses[$ticket['status']] ?? 'glass-badge' ?>" style="justify-content: center; width: 100%;">
                        <?= $statusLabels[$ticket['status']] ?? $ticket['status'] ?>
                    </span>
                </td>
                <td style="text-align: center;">
                    <?php if ($ticket['assigned_name']): ?>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px; background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 20px;">
                            <div class="user-avatar" style="width: 20px; height: 20px; font-size: 0.6rem;">
                                <?= mb_substr($ticket['assigned_name'], 0, 1) ?>
                            </div>
                            <span style="font-size: 0.8rem;"><?= htmlspecialchars($ticket['assigned_name']) ?></span>
                        </div>
                    <?php else: ?>
                        <span style="color: #64748b; font-size: 0.8rem; font-style: italic;">-- غير معين --</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: center; color: #94a3b8; font-size: 0.85rem; white-space: nowrap;">
                    <div><?= date('Y-m-d', strtotime($ticket['created_at'])) ?></div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;"><?= date('h:i A', strtotime($ticket['created_at'])) ?></div>
                </td>
                <td style="text-align: center; white-space: nowrap;">
                    <div style="display: inline-flex; gap: 4px; position: relative;">
                        <!-- Action buttons directly inline for better access or kept in dropdown -->
                        <button class="btn-icon-glass" onclick="toggleActions(<?= $ticket['id'] ?>, event)" style="width: 32px; height: 32px;">
                            <i class="fas fa-ellipsis-v" style="font-size: 0.9rem;"></i>
                        </button>
                        <!-- القائمة المنسدلة (Template) -->
                        <div id="actions-template-<?= $ticket['id'] ?>" style="display: none;">
                        <a href="<?= BASE_URL ?>/admin/tickets/show/<?= $ticket['id'] ?>" class="action-item">
                            <i class="fas fa-eye" style="width: 20px; color: #60a5fa;"></i> عرض التفاصيل
                        </a>
                        <div class="action-item" onclick="openAssignModal(<?= $ticket['id'] ?>)">
                            <i class="fas fa-user-plus" style="width: 20px; color: #a78bfa;"></i> تعيين فني
                        </div>
                        <?php if ($ticket['status'] !== 'closed'): ?>
                        <div class="action-item" onclick="quickStatusUpdate(<?= $ticket['id'] ?>, 'closed')">
                            <i class="fas fa-check-circle" style="width: 20px; color: #34d399;"></i> إغلاق التذكرة
                        </div>
                        <?php endif; ?>
                        
                        <?php if (Auth::can('tickets.delete')): ?>
                        <div class="dropdown-divider" style="margin: 4px 0; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                        <div class="action-item text-danger" onclick="deleteTicket(<?= $ticket['id'] ?>)">
                            <i class="fas fa-trash-alt" style="width: 20px;"></i> حذف التذكرة
                        </div>
                        <?php endif; ?>
                    </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- التصفح Pagination -->
<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
    <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
    <a href="<?= BASE_URL ?>/admin/tickets?page=<?= $i ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
       class="btn-icon-glass" 
       style="<?= $i == $currentPage ? 'background: var(--neon-blue); color: white; border-color: var(--neon-blue);' : '' ?> width: 40px; height: 40px;">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Modal تعيين فني -->
<div id="assignModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">تعيين فني للتذكرة</div>
            <button class="modal-close" onclick="closeAssignModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="assignForm" onsubmit="submitAssign(event)">
            <input type="hidden" id="assignTicketId">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #cbd5e1; margin-bottom: 0.5rem;">اختر الفني المسؤول</label>
                <select id="technicianSelect" class="search-input" style="width: 100%; color: #fff;" required>
                    <option value="" style="color: #000;">اختر من القائمة...</option>
                    <?php foreach ($technicians as $tech): ?>
                    <option value="<?= $tech['id'] ?>" style="color: #000;"><?= htmlspecialchars($tech['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-glass-primary" style="flex: 1; justify-content: center;">
                    حفظ التعيين
                </button>
                <button type="button" onclick="closeAssignModal()" class="btn-glass-primary" style="flex: 1; justify-content: center; background: transparent; border: 1px solid var(--glass-border);">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>




<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// Toggle Dropdown with Smart Positioning
function toggleActions(id, event) {
    event.stopPropagation();
    const btn = event.currentTarget;
    const template = document.getElementById('actions-template-' + id);
    const globalMenu = document.getElementById('global-actions-menu');
    
    // Hide if already showing for this ID
    if (globalMenu.classList.contains('show') && globalMenu.dataset.activeId == id) {
        globalMenu.classList.remove('show');
        globalMenu.dataset.activeId = '';
        return;
    }

    // Populate Global Menu
    globalMenu.innerHTML = template.innerHTML;
    globalMenu.dataset.activeId = id;
    
    // Calculate position
    const rect = btn.getBoundingClientRect();
    const dropdownWidth = 160; 
    
    globalMenu.style.position = 'fixed';
    globalMenu.style.top = (rect.bottom + 5) + 'px';
    globalMenu.style.zIndex = '99999';
    globalMenu.style.minWidth = '160px'; // Ensure width
    
    // Clear previous positioning
    globalMenu.style.left = '';
    globalMenu.style.right = '';
        
        // Smart Horizontal Alignment (RTL support)
        // If button is close to left edge, align left. Else align right.
        if (rect.left < dropdownWidth) {
             globalMenu.style.left = rect.left + 'px';
             globalMenu.style.right = 'auto';
             globalMenu.style.transformOrigin = 'top left';
        } else {
             if (rect.left + dropdownWidth > window.innerWidth) {
                 globalMenu.style.left = 'auto';
                 globalMenu.style.right = (window.innerWidth - rect.right) + 'px';
                 globalMenu.style.transformOrigin = 'top right';
             } else {
                 globalMenu.style.left = rect.left + 'px';
                 globalMenu.style.right = 'auto';
                 globalMenu.style.transformOrigin = 'top left';
             }
        }

        globalMenu.classList.add('show');
}

// Close dropdowns when clicking outside or scrolling
const closeAll = () => {
    const globalMenu = document.getElementById('global-actions-menu');
    if(globalMenu) {
        globalMenu.classList.remove('show');
        globalMenu.dataset.activeId = '';
    }
};

document.addEventListener('click', closeAll);
window.addEventListener('scroll', closeAll, true);
window.addEventListener('resize', closeAll);

function openAssignModal(ticketId) {
    document.getElementById('assignTicketId').value = ticketId;
    const modal = document.getElementById('assignModal');
    modal.style.display = 'flex';
    // Trigger reflow
    modal.offsetHeight; 
    modal.classList.add('show');
}

function closeAssignModal() {
    const modal = document.getElementById('assignModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function submitAssign(e) {
    e.preventDefault();
    const ticketId = document.getElementById('assignTicketId').value;
    const technicianId = document.getElementById('technicianSelect').value;
    
    fetch('<?= BASE_URL ?>/admin/tickets/assign/' + ticketId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: 'technician_id=' + technicianId + '&<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showSystemAlert(data.error, 'error');
        }
    });
}

function quickStatusUpdate(ticketId, status) {
    showSystemConfirm('تغيير الحالة', 'هل أنت متأكد من تغيير حالة التذكرة؟', () => {
        fetch('<?= BASE_URL ?>/admin/tickets/update-status/' + ticketId, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `status=${status}&notes=تحديث سريع&<?= CSRF_TOKEN_NAME ?>=${csrfToken}`
        }).then(res => res.json()).then(data => {
            if (data.success) {
                 location.reload();
            } else {
                 showSystemAlert(data.error || 'حدث خطأ ما', 'error');
            }
        });
    });
}

// Bulk Actions Logic
function toggleSelectAll() {
    const parent = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.ticket-checkbox');
    checkboxes.forEach(cb => cb.checked = parent.checked);
    updateBulkActions();
}

function deselectAll() {
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.ticket-checkbox').forEach(cb => cb.checked = false);
    updateBulkActions();
}

function updateBulkActions() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    const toolbar = document.getElementById('bulk-actions');
    const countSpan = document.getElementById('selected-count');
    
    if (checked.length > 0) {
        toolbar.style.display = 'flex';
        countSpan.textContent = checked.length;
    } else {
        toolbar.style.display = 'none';
        document.getElementById('select-all').checked = false;
    }
}

function deleteTicket(id) {
    showSystemConfirm('حذف التذكرة', 'هل أنت متأكد من حذف هذه التذكرة نهائياً؟ هذا الإجراء لا يمكن التراجع عنه.', () => {
        fetch('<?= BASE_URL ?>/admin/tickets/delete/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                showSystemAlert(data.error, 'error');
            }
        });
    });
}

function bulkDelete() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    if (checked.length === 0) return;
    
    showSystemConfirm('حذف جماعي', 'هل أنت متأكد من حذف ' + checked.length + ' تذكرة؟ هذا الإجراء لا يمكن التراجع عنه.', () => {
        const ids = Array.from(checked).map(cb => cb.value);
        
        // Create form data properly for array
        const formData = new URLSearchParams();
        ids.forEach(id => formData.append('ids[]', id));
        formData.append('<?= CSRF_TOKEN_NAME ?>', csrfToken);
        
        fetch('<?= BASE_URL ?>/admin/tickets/bulk-delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                showSystemAlert(data.error, 'error');
            }
        });
    });
}
</script>

<!-- Global Action Menu Container -->
<div id="global-actions-menu" class="action-dropdown" style="position: fixed; display: none; z-index: 99999; background: #1e293b; border: 1px solid var(--glass-border); border-radius: 8px; padding: 4px; box-shadow: 0 4px 20px rgba(0,0,0,0.5);"></div>

<style>
/* Ensure global menu shows correctly */
#global-actions-menu.show {
    display: block !important;
    animation: fadeIn 0.1s ease-out;
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

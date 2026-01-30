<?php
$currentPage = 'messages';
ob_start();

$unreadCount = 0;
foreach (($messages ?? []) as $msg) {
    if (!$msg['is_read']) $unreadCount++;
}
?>

<!-- Page Header -->
<div class="page-header" style="justify-content: space-between; align-items: center; display: flex;">
    <!-- Right Side: Title & Badges -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 class="page-title">
            <i class="fas fa-envelope-open-text" style="color: #60a5fa;"></i>
            إدارة الرسائل
        </h1>
        <div style="display: flex; gap: 10px;">
            <span class="glass-badge" style="font-size: 0.9rem;">
                <?= $total ?? 0 ?> رسالة
            </span>
            <?php if ($unreadCount > 0): ?>
            <span class="glass-badge" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2);">
                <i class="fas fa-bell" style="margin-left: 5px;"></i>
                <?= $unreadCount ?> غير مقروءة
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Left Side: Search Form & Actions -->
    <div style="display: flex; gap: 1rem; align-items: center;">
        <!-- Bulk Actions Button -->
        <div id="bulkActionsContainer">
            <button id="bulkDeleteBtn" onclick="bulkDelete()" class="btn-glass-danger" style="display: none;">
                <i class="fas fa-trash-alt"></i> حذف المحدد (<span id="selectedCount">0</span>)
            </button>
        </div>
        
        <!-- Search Form -->
        <div style="width: 300px;">
            <form action="" method="GET" style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" class="glass-input" placeholder="بحث باسم، بريد، أو موضوع..." value="<?= htmlspecialchars($search ?? '') ?>" style="padding-right: 1rem; padding-left: 2.5rem; width: 100%;">
            </form>
        </div>
    </div>
</div>

<!-- Messages Table -->
<div class="glass-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <table class="glass-table">
        <thead>
            <tr>
                <th style="width: 40px; padding-right: 1.5rem;"><input type="checkbox" id="selectAll"></th>
                <th>الحالة</th>
                <th>المرسل</th>
                <th>الموضوع</th>
                <th>التاريخ</th>
                <th class="text-left" style="padding-left: 2rem;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    لا توجد رسائل
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($messages as $msg): ?>
            <tr style="<?= !$msg['is_read'] ? 'background: rgba(59, 130, 246, 0.05);' : '' ?>">
                <td style="padding-right: 1.5rem;">
                    <input type="checkbox" class="message-checkbox" value="<?= $msg['id'] ?>" onchange="updateBulkActions()">
                </td>
                <td>
                    <?php if (!$msg['is_read']): ?>
                    <span class="glass-badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);">جديدة</span>
                    <?php else: ?>
                    <span class="glass-badge" style="background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">مقروءة</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.8rem;">
                            <?= mb_substr($msg['name'], 0, 1) ?>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 600; color: #f1f5f9; font-size: 0.95rem;">
                                <?= htmlspecialchars($msg['name']) ?>
                            </span>
                            <span style="font-size: 0.85rem; color: #38bdf8; font-family: monospace; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($msg['email'] ?? '') ?>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="color: #e2e8f0; font-weight: 500;">
                        <?= htmlspecialchars($msg['subject'] ?? 'بدون موضوع') ?>
                    </span>
                </td>
                <td>
                    <span style="color: #94a3b8; font-size: 0.9rem;">
                        <i class="far fa-clock" style="margin-left: 5px;"></i>
                        <?= date('Y/m/d H:i', strtotime($msg['created_at'])) ?>
                    </span>
                </td>
                <td class="actions-cell">
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <a href="<?= BASE_URL ?>/admin/messages/show/<?= $msg['id'] ?>" class="btn-icon" title="عرض الرسالة">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="deleteMessage(<?= $msg['id'] ?>)" class="btn-icon delete" title="حذف الرسالة">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (($totalPages ?? 1) > 1): ?>
<div style="display: flex; justify-content: center; margin-top: 2rem; gap: 0.5rem;">
    <?php if (($currentPage ?? 1) > 1): ?>
    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>" class="btn-glass-secondary">
        <i class="fas fa-chevron-right"></i> السابق
    </a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" class="btn-glass-<?= $i == $currentPage ? 'primary' : 'secondary' ?>" style="min-width: 40px; text-align: center;">
        <?= $i ?>
    </a>
    <?php endfor; ?>
    
    <?php if (($currentPage ?? 1) < $totalPages): ?>
    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>" class="btn-glass-secondary">
        التالي <i class="fas fa-chevron-left"></i>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<style>
/* Custom minimal overrides */
.glass-input {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: #fff;
    transition: all 0.3s ease;
}
.glass-input:focus {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(96, 165, 250, 0.5);
    outline: none;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.2);
}
.btn-glass-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #fca5a5;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-glass-danger:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-2px);
}
.btn-glass-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-glass-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}
.btn-glass-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-glass-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}
</style>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// تحديد الكل
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.message-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkActions();
});

// تحديث أزرار الإجراءات الجماعية
function updateBulkActions() {
    const selected = document.querySelectorAll('.message-checkbox:checked').length;
    const btn = document.getElementById('bulkDeleteBtn');
    const countSpan = document.getElementById('selectedCount');
    
    if (selected > 0) {
        btn.style.display = 'inline-flex';
        countSpan.textContent = selected;
        // Animation
        btn.style.animation = 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    } else {
        btn.style.display = 'none';
    }
}

// تنفيذ الحذف المتعدد
function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.message-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) return;
    
    showSystemConfirm('حذف جماعي', 'هل أنت متأكد من حذف ' + selected.length + ' رسالة؟ لا يمكن التراجع عن هذا الإجراء.', function() {
        const formData = new FormData();
        formData.append('<?= CSRF_TOKEN_NAME ?>', csrfToken);
        selected.forEach(id => formData.append('ids[]', id));
        
        fetch('<?= BASE_URL ?>/admin/messages/bulk-delete', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                // Animate rows out
                selected.forEach(id => {
                    const row = document.querySelector(`.message-checkbox[value="${id}"]`).closest('tr');
                    if(row) {
                        row.style.transition = 'all 0.5s ease';
                        row.style.transform = 'translateX(100px)';
                        row.style.opacity = '0';
                    }
                });
                setTimeout(() => location.reload(), 500);
            } else {
                showSystemAlert(data.message, 'error');
            }
        });
    });
}

function deleteMessage(id) {
    showSystemConfirm('حذف الرسالة', 'هل أنت متأكد من حذف هذه الرسالة؟', function() {
        fetch('<?= BASE_URL ?>/admin/messages/delete/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
        }).then(res => res.json()).then(data => {
            if (data.success) {
                const row = document.querySelector(`button[onclick="deleteMessage(${id})"]`).closest('tr');
                row.style.transition = 'all 0.5s ease';
                row.style.transform = 'translateX(100px)';
                row.style.opacity = '0';
                setTimeout(() => location.reload(), 500);
            } else {
                showSystemAlert(data.message, 'error');
            }
        });
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

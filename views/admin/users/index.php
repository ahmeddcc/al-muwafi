<?php
$currentPage = 'users';
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="justify-content: space-between; align-items: center; display: flex;">
    <!-- Right Side: Title & Badges -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 class="page-title">
            <i class="fas fa-users" style="color: #60a5fa;"></i>
            إدارة المستخدمين
        </h1>
        <span class="glass-badge" style="font-size: 0.9rem;">
            <?= $total ?? 0 ?> مستخدم
        </span>
        <a href="<?= BASE_URL ?>/admin/users/create" class="btn-glass-primary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">
            <i class="fas fa-plus"></i> إضافة مستخدم
        </a>
    </div>

    <!-- Left Side: Search Form & Actions -->
    <div style="display: flex; gap: 1rem; align-items: center;">
        <!-- Bulk Actions Button -->
        <div id="bulkActionsContainer">
            <button id="bulkDeleteBtn" onclick="bulkDelete()" class="btn-glass-danger" style="display: none;">
                <i class="fas fa-trash-alt"></i> حذف المحدد (<span id="selectedCount">0</span>)
            </button>
        </div>

        <div style="width: 300px;">
            <form action="" method="GET" style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" name="search" class="glass-input" placeholder="بحث بالاسم، البريد، أو الهاتف..." value="<?= htmlspecialchars($search ?? '') ?>" style="padding-right: 1rem; padding-left: 2.5rem; width: 100%;">
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="glass-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <table class="glass-table">
        <thead>
            <tr>
                <th style="width: 40px; padding-right: 1.5rem;"><input type="checkbox" id="selectAll"></th>
                <th>المستخدم</th>
                <th>اسم المستخدم</th>
                <th>الدور والصلاحية</th>
                <th>الحالة</th>
                <th>آخر دخول</th>
                <th class="text-left" style="padding-left: 2rem;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-users-slash" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    لا يوجد مستخدمين
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($users as $u): ?>
            <tr>
                <td style="padding-right: 1.5rem;">
                    <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                    <input type="checkbox" class="user-checkbox" value="<?= $u['id'] ?>" onchange="updateBulkActions()">
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <?php if (!empty($u['avatar'])): ?>
                        <div style="width: 45px; height: 45px; border-radius: 50%; overflow: hidden; border: 2px solid rgba(255, 255, 255, 0.1);">
                            <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($u['avatar']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php else: ?>
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; border: 2px solid rgba(255, 255, 255, 0.1); font-size: 1.1rem;">
                            <?= mb_substr($u['full_name'], 0, 1) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 600; color: #f1f5f9; font-size: 1rem;">
                                <?= htmlspecialchars($u['full_name']) ?>
                            </span>
                            <span style="font-size: 0.85rem; color: #38bdf8; font-family: monospace; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($u['email']) ?>
                            </span>
                            <?php if ($u['phone']): ?>
                            <span style="font-size: 0.8rem; color: #94a3b8;">
                                <i class="fas fa-phone-alt"></i> <?= htmlspecialchars($u['phone']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="background: rgba(255, 255, 255, 0.05); padding: 4px 8px; border-radius: 6px; font-family: monospace; color: #cbd5e1;">
                        @<?= htmlspecialchars($u['username']) ?>
                    </span>
                </td>
                <td>
                    <span class="glass-badge" style="background: rgba(59, 130, 246, 0.1); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <i class="fas fa-user-shield" style="margin-left: 5px;"></i>
                        <?= htmlspecialchars($u['role_name_ar']) ?>
                    </span>
                </td>
                <td>
                    <button onclick="toggleStatus(<?= $u['id'] ?>, this)" 
                            class="status-btn <?= $u['is_active'] ? 'active' : 'inactive' ?>"
                            <?= $u['id'] == ($_SESSION['user_id'] ?? 0) ? 'disabled style="cursor:not-allowed; opacity:0.75;"' : '' ?>>
                        <span class="status-dot"></span>
                        <span class="status-text"><?= $u['is_active'] ? 'مفعل' : 'معطل' ?></span>
                    </button>
                </td>
                <td>
                    <?php if ($u['last_login']): ?>
                    <span style="color: #cbd5e1; font-size: 0.9rem;">
                        <?= date('Y-m-d H:i', strtotime($u['last_login'])) ?>
                    </span>
                    <?php else: ?>
                    <span style="color: #64748b; font-size: 0.9rem;">-</span>
                    <?php endif; ?>
                </td>
                <td class="actions-cell">
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <a href="<?= BASE_URL ?>/admin/users/edit/<?= $u['id'] ?>" class="btn-icon" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/users/activity/<?= $u['id'] ?>" class="btn-icon" title="سجل النشاط">
                            <i class="fas fa-history"></i>
                        </a>
                        <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                        <button onclick="deleteUser(<?= $u['id'] ?>)" class="btn-icon delete" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
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
/* Custom overrides */
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
    transform: translateY(-2px);
    color: #fff;
}
.status-btn {
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}
.status-btn.active {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #34d399;
}
.status-btn.active .status-dot {
    background: #34d399;
    box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
}
.status-btn.inactive {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}
.status-btn.inactive .status-dot {
    background: #f87171;
}
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
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
</style>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// تحديد الكل
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkActions();
});

// تحديث أزرار الإجراءات الجماعية
function updateBulkActions() {
    const selected = document.querySelectorAll('.user-checkbox:checked').length;
    const btn = document.getElementById('bulkDeleteBtn');
    const countSpan = document.getElementById('selectedCount');
    
    if (selected > 0) {
        btn.style.display = 'inline-flex';
        countSpan.textContent = selected;
        btn.style.animation = 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
    } else {
        btn.style.display = 'none';
    }
}

// تنفيذ الحذف المتعدد
function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) return;
    
    showSystemConfirm('حذف جماعي', 'هل أنت متأكد من حذف ' + selected.length + ' مستخدم؟ لا يمكن التراجع عن هذا الإجراء.', function() {
        const formData = new FormData();
        formData.append('<?= CSRF_TOKEN_NAME ?>', csrfToken);
        selected.forEach(id => formData.append('ids[]', id));
        
        fetch('<?= BASE_URL ?>/admin/users/bulk-delete', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                selected.forEach(id => {
                    const row = document.querySelector(`.user-checkbox[value="${id}"]`).closest('tr');
                    if(row) {
                        row.style.transition = 'all 0.5s ease';
                        row.style.transform = 'translateX(100px)';
                        row.style.opacity = '0';
                    }
                });
                setTimeout(() => location.reload(), 500);
            } else {
                showSystemAlert(data.error, 'error');
            }
        });
    });
}

function toggleStatus(id, btn) {
    // Disable button to prevent double clicks
    btn.disabled = true;
    
    fetch('<?= BASE_URL ?>/admin/users/toggle-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        btn.disabled = false;
        if (data.success) {
            if (data.is_active) {
                btn.className = 'status-btn active';
                btn.innerHTML = '<span class="status-dot" style="background: #34d399; box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);"></span> <span class="status-text">مفعل</span>';
            } else {
                btn.className = 'status-btn inactive';
                btn.innerHTML = '<span class="status-dot" style="background: #f87171;"></span> <span class="status-text">معطل</span>';
            }
            showSystemAlert('تم تحديث حالة المستخدم بنجاح');
        } else {
            showSystemAlert(data.error, 'error');
        }
    });
}

function deleteUser(id) {
    showSystemConfirm('حذف المستخدم', 'هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.', function() {
        fetch('<?= BASE_URL ?>/admin/users/delete/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
        }).then(res => res.json()).then(data => {
            if (data.success) {
                const row = document.querySelector(`button[onclick="deleteUser(${id})"]`).closest('tr');
                row.style.transition = 'all 0.5s ease';
                row.style.transform = 'translateX(100px)';
                row.style.opacity = '0';
                setTimeout(() => location.reload(), 500);
            } else {
                showSystemAlert(data.error, 'error');
            }
        });
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

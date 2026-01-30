<?php
/**
 * نموذج إضافة/تعديل دور (Redesigned Matrix Layout)
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Security;

$currentPage = 'roles';
$isEdit = !empty($role);
$title = $isEdit ? 'تعديل الدور: ' . htmlspecialchars($role['name_ar']) : 'إضافة دور جديد';

ob_start();

$moduleNames = [
    'users' => 'إدارة المستخدمين',
    'roles' => 'الأدوار والصلاحيات',
    'products' => 'إدارة المنتجات',
    'categories' => 'الأقسام والتصنيفات',
    'spare_parts' => 'قطع الغيار',
    'services' => 'الخدمات',
    'tickets' => 'نظام التذاكر',
    'pages' => 'إدارة الصفحات',
    'messages' => 'الرسائل',
    'settings' => 'الإعدادات العامة',
    'general' => 'صلاحيات عامة',
    'dashboard' => 'لوحة التحكم',
    'logs' => 'سجلات النظام'
];

$moduleIcons = [
    'users' => 'fa-users',
    'roles' => 'fa-user-shield',
    'products' => 'fa-box',
    'categories' => 'fa-layer-group',
    'spare_parts' => 'fa-tools',
    'services' => 'fa-concierge-bell',
    'tickets' => 'fa-ticket-alt',
    'pages' => 'fa-file-alt',
    'messages' => 'fa-envelope',
    'settings' => 'fa-cogs',
    'general' => 'fa-globe',
    'dashboard' => 'fa-home',
    'logs' => 'fa-history'
];

// Helper for permission styling
function getPermStyle($name) {
    if (strpos($name, '.delete') !== false) return ['class' => 'danger', 'icon' => 'fa-trash-alt'];
    if (strpos($name, '.view') !== false) return ['class' => 'view', 'icon' => 'fa-eye'];
    if (strpos($name, '.create') !== false) return ['class' => 'modify', 'icon' => 'fa-plus'];
    if (strpos($name, '.edit') !== false) return ['class' => 'modify', 'icon' => 'fa-pen'];
    if (strpos($name, '.assign') !== false) return ['class' => 'action', 'icon' => 'fa-user-check'];
    return ['class' => 'default', 'icon' => 'fa-check'];
}
?>

<!-- Page Header -->
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="<?= BASE_URL ?>/admin/roles" class="btn-icon back-btn" title="عودة">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="page-title">
                <i class="fas fa-shield-alt text-primary"></i>
                <?= $title ?>
            </h1>
            <p style="color: #94a3b8; margin-top: 5px; font-size: 0.9rem;">
                <?= $isEdit ? 'تعديل الصلاحيات والمسميات المخصصة لهذا الدور' : 'إنشاء دور جديد وتحديد مستوى الوصول' ?>
            </p>
        </div>
    </div>
</div>

<div class="form-container">
    <?php if (!empty($_SESSION['flash']['error'])): ?>
    <div class="alert-glass error">
        <i class="fas fa-exclamation-circle"></i>
        <?= $_SESSION['flash']['error'] ?>
        <?php unset($_SESSION['flash']['error']); ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/roles/<?= $isEdit ? 'update/' . $role['id'] : 'store' ?>" method="POST" id="roleForm">
        <?= Security::csrfField() ?>
        
        <!-- Role Info Section -->
        <div class="glass-card section-card">
            <div class="section-header">
                <div class="section-icon"><i class="fas fa-info-circle"></i></div>
                <h3>البيانات الأساسية</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="glass-label">الاسم العربي <span class="text-danger">*</span></label>
                    <input type="text" name="name_ar" class="glass-input" 
                           value="<?= htmlspecialchars($role['name_ar'] ?? '') ?>" required
                           placeholder="مثال: مدير المبيعات">
                </div>
                
                <div class="form-group">
                    <label class="glass-label">الاسم البرمجي (English) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="glass-input" 
                           value="<?= htmlspecialchars($role['name'] ?? '') ?>" required
                           placeholder="example: sales_manager" <?= ($role['name'] ?? '') === 'admin' ? 'readonly' : '' ?>>
                    <small style="color: #64748b; margin-top: 5px; display: block;">يستخدم في الكود، لا يمكن تغييره للكتابة العربية.</small>
                </div>
                
                <div class="form-group full-width">
                    <label class="glass-label">الوصف</label>
                    <input type="text" name="description" class="glass-input" 
                           value="<?= htmlspecialchars($role['description'] ?? '') ?>"
                           placeholder="وصف مختصر لمهام هذا الدور وصلاحياته">
                </div>
            </div>
        </div>

        <!-- Permissions Toolbar -->
        <div class="permissions-toolbar glass-card">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="permissionSearch" placeholder="ابحث في الصلاحيات...">
            </div>
            
            <div class="batch-actions">
                <button type="button" onclick="selectAll(true)" class="btn-action check-all">
                    <i class="fas fa-check-double"></i> تحديد الكل
                </button>
                <button type="button" onclick="selectAll(false)" class="btn-action uncheck-all">
                    <i class="fas fa-times"></i> إلغاء الكل
                </button>
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div class="permissions-matrix">
            <?php foreach (($groupedPermissions ?? []) as $module => $perms): ?>
            <div class="module-card glass-card" data-module="<?= $moduleNames[$module] ?? $module ?>">
                <div class="module-header">
                    <div class="module-title">
                        <i class="fas <?= $moduleIcons[$module] ?? 'fa-cube' ?>"></i>
                        <h3><?= $moduleNames[$module] ?? $module ?></h3>
                        <span class="count-badge"><?= count($perms) ?></span>
                    </div>
                    <label class="switch-sm" title="تفعيل الكل">
                        <input type="checkbox" onchange="toggleModule('<?= $module ?>', this.checked)">
                        <span class="slider round"></span>
                    </label>
                </div>
                
                <div class="perms-grid">
                    <?php foreach ($perms as $perm): 
                        $style = getPermStyle($perm['name']);
                    ?>
                    <label class="perm-item <?= $style['class'] ?>" title="<?= $perm['description'] ?? '' ?>">
                        <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" 
                               data-module="<?= $module ?>"
                               data-name="<?= $perm['name_ar'] ?? $perm['name'] ?>"
                               <?= in_array($perm['id'], $rolePermissions ?? []) ? 'checked' : '' ?>>
                        <div class="perm-content">
                            <span class="perm-icon"><i class="fas <?= $style['icon'] ?>"></i></span>
                            <span class="perm-text"><?= htmlspecialchars($perm['name_ar'] ?? $perm['name']) ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sticky Footer Actions -->
        <div class="form-actions-bar">
            <div class="container">
                <a href="<?= BASE_URL ?>/admin/roles" class="btn-glass-secondary">إلغاء</a>
                <button type="submit" class="btn-glass-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
            </div>
        </div>
        
    </form>
</div>

<style>
/* Layout */
.form-container {
    max-width: 1200px;
    margin: 0 auto;
    padding-bottom: 80px;
}

.section-card {
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.section-icon {
    width: 32px;
    height: 32px;
    background: rgba(96, 165, 250, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #60a5fa;
}

.section-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #e2e8f0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.full-width { grid-column: span 2; }

@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
}

/* Toolbar */
.permissions-toolbar {
    padding: 1rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.search-box i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}

.search-box input {
    width: 100%;
    padding: 10px 35px 10px 15px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    color: #fff;
    outline: none;
}

.search-box input:focus {
    background: rgba(255,255,255,0.08);
    border-color: rgba(96, 165, 250, 0.5);
}

.batch-actions {
    display: flex;
    gap: 10px;
}

.btn-action {
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.03);
    color: #cbd5e1;
    cursor: pointer;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-action:hover {
    background: rgba(255,255,255,0.08);
    color: #fff;
}

.check-all:hover { border-color: #10b981; color: #34d399; }
.uncheck-all:hover { border-color: #ef4444; color: #f87171; }

/* Matrix */
.permissions-matrix {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.module-card {
    padding: 0;
    overflow: hidden;
    height: 100%;
}

.module-header {
    background: rgba(0,0,0,0.2);
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.module-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.module-title h3 {
    margin: 0;
    font-size: 1rem;
    color: #e2e8f0;
}

.module-title i { color: #94a3b8; }

.count-badge {
    background: rgba(255,255,255,0.1);
    color: #94a3b8;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 4px;
}

.perms-grid {
    padding: 1.25rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.perm-item {
    position: relative;
    cursor: pointer;
    user-select: none;
}

.perm-item input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.perm-content {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.perm-text {
    font-size: 0.85rem;
    color: #94a3b8;
}

.perm-icon {
    font-size: 0.9rem;
    color: #64748b;
    width: 20px;
    text-align: center;
}

/* Hover States */
.perm-item:hover .perm-content {
    background: rgba(255,255,255,0.06);
}

/* Active States */
.perm-item input:checked ~ .perm-content {
    background: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.4);
}
.perm-item input:checked ~ .perm-content .perm-text { color: #e2e8f0; font-weight: 500; }
.perm-item input:checked ~ .perm-content .perm-icon { color: #34d399; }

/* View Style */
.perm-item.view input:checked ~ .perm-content {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.4);
}
.perm-item.view input:checked ~ .perm-content .perm-icon { color: #60a5fa; }

/* Danger Style */
.perm-item.danger input:checked ~ .perm-content {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(239, 68, 68, 0.4);
}
.perm-item.danger input:checked ~ .perm-content .perm-icon { color: #f87171; }

.perm-item.hidden { display: none; }
.module-card.hidden { display: none; }

/* Actions Bar */
.form-actions-bar {
    position: fixed;
    bottom: 0;
    left: 280px; /* Sidebar width */
    right: 0;
    background: rgba(15, 23, 42, 0.9);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 1rem 0;
    z-index: 100;
}

@media (max-width: 992px) {
    .form-actions-bar { left: 0; }
}

.form-actions-bar .container {
    padding: 0 2rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.switch-sm {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
}
.switch-sm input { opacity: 0; width: 0; height: 0; }
.switch-sm .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(255,255,255,0.2);
    transition: .4s;
}
.switch-sm .slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}
.switch-sm input:checked + .slider { background-color: #60a5fa; }
.switch-sm input:checked + .slider:before { transform: translateX(14px); }
.switch-sm .slider.round { border-radius: 20px; }
.switch-sm .slider.round:before { border-radius: 50%; }

.back-btn {
    background: rgba(255,255,255,0.1);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #e2e8f0;
    text-decoration: none;
    transition: all 0.2s;
}
.back-btn:hover { background: rgba(255,255,255,0.2); color: #fff; }
</style>

<script>
// Search
document.getElementById('permissionSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    
    document.querySelectorAll('.module-card').forEach(module => {
        let hasVisible = false;
        
        module.querySelectorAll('.perm-item').forEach(item => {
            const name = item.querySelector('input').dataset.name.toLowerCase();
            if (name.includes(term)) {
                item.classList.remove('hidden');
                hasVisible = true;
            } else {
                item.classList.add('hidden');
            }
        });
        
        if (hasVisible) {
            module.classList.remove('hidden');
        } else {
            module.classList.add('hidden');
        }
    });
});

// Select/Deselect All
function selectAll(checked) {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        if (!cb.closest('.perm-item').classList.contains('hidden')) {
            cb.checked = checked;
        }
    });
    // Update modules switches
    document.querySelectorAll('.switch-sm input').forEach(sw => sw.checked = checked);
}

// Module Toggle
function toggleModule(module, checked) {
    document.querySelectorAll(`input[data-module="${module}"]`).forEach(cb => {
        if (!cb.closest('.perm-item').classList.contains('hidden')) {
            cb.checked = checked;
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

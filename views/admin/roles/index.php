<?php
/**
 * صفحة إدارة الأدوار والصلاحيات (Modern Grid Layout)
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'roles';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-shield text-primary"></i>
                إدارة الأدوار والصلاحيات
            </h1>
            <p style="color: #94a3b8; margin-top: 5px; font-size: 0.9rem;">
                تحكم في مستويات الوصول وصلاحيات المستخدمين في النظام
            </p>
        </div>
        <a href="<?= BASE_URL ?>/admin/roles/create" class="btn-glass-primary">
            <i class="fas fa-plus"></i> إضافة دور جديد
        </a>
    </div>
</div>

<?php if (empty($roles)): ?>
<div class="glass-card empty-state">
    <div class="empty-icon">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h3>لا توجد أدوار معرفة</h3>
    <p>قم بإضافة أدوار جديدة لتحديد صلاحيات المستخدمين.</p>
    <a href="<?= BASE_URL ?>/admin/roles/create" class="btn-glass-primary mt-3">
        <i class="fas fa-plus"></i> إضافة دور جديد
    </a>
</div>
<?php else: ?>

<div class="roles-grid">
    <?php foreach ($roles as $role): 
        // حساب نسبة الصلاحيات
        $maxPerms = 50; // تقديري
        $percent = min(100, round(($role['permissions_count'] / $maxPerms) * 100));
        
        // تحديد اللون بناءً على النسبة
        $progressColor = '#10b981'; // Green
        if ($percent > 40) $progressColor = '#f59e0b'; // Orange
        if ($percent > 80) $progressColor = '#ef4444'; // Red (Super Admin level)
        
        // الأيقونة حسب الدور
        $roleIcon = 'fa-user-tag';
        if ($role['id'] == 1) $roleIcon = 'fa-crown';
        if ($role['id'] == 2) $roleIcon = 'fa-user-tie';
        if ($role['id'] == 3) $roleIcon = 'fa-tools';
    ?>
    <div class="role-card glass-card">
        <div class="role-header">
            <div class="role-icon-wrapper">
                <i class="fas <?= $roleIcon ?>"></i>
            </div>
            <div class="role-actions">
                <a href="<?= BASE_URL ?>/admin/roles/edit/<?= $role['id'] ?>" class="btn-icon" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <?php if (!in_array($role['name'], ['admin', 'super_admin', 'superadmin'])): ?>
                <button onclick="deleteRole(<?= $role['id'] ?>)" class="btn-icon delete" title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <h3 class="role-name"><?= htmlspecialchars($role['name_ar'] ?? $role['name']) ?></h3>
        <span class="role-slug"><?= htmlspecialchars($role['name']) ?></span>
        
        <p class="role-desc"><?= htmlspecialchars($role['description'] ?? 'لا يوجد وصف') ?></p>
        
        <div class="role-stats">
            <div class="stat-item">
                <i class="fas fa-users"></i>
                <span><?= $role['users_count'] ?> مستخدم</span>
            </div>
            <div class="stat-item">
                <i class="fas fa-key"></i>
                <span><?= $role['permissions_count'] ?> صلاحية</span>
            </div>
        </div>
        
        <div class="role-permissions-bar">
            <div class="progress-label">
                <span>مستوى الصلاحيات</span>
                <span><?= $percent ?>%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: <?= $percent ?>%; background: <?= $progressColor ?>;"></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<style>
.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    padding-bottom: 2rem;
}

.role-card {
    padding: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.role-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.role-icon-wrapper {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #60a5fa;
    border: 1px solid rgba(255,255,255,0.1);
}

.role-name {
    font-size: 1.25rem;
    color: #e2e8f0;
    margin: 0;
    font-weight: 700;
}

.role-slug {
    font-family: monospace;
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    display: block;
}

.role-desc {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
    flex-grow: 1;
}

.role-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #cbd5e1;
    background: rgba(255,255,255,0.03);
    padding: 4px 10px;
    border-radius: 20px;
}

.stat-item i { color: #60a5fa; }

.role-permissions-bar {
    margin-top: auto;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #94a3b8;
    margin-bottom: 5px;
}

.progress-track {
    height: 6px;
    background: rgba(255,255,255,0.05);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.empty-icon {
    font-size: 4rem;
    color: rgba(255,255,255,0.1);
    margin-bottom: 1rem;
}
</style>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

function deleteRole(id) {
    if (!confirm('هل أنت متأكد من حذف هذا الدور؟ سيتم حذف جميع الصلاحيات المرتبطة به.')) return;
    
    fetch('<?= BASE_URL ?>/admin/roles/delete/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

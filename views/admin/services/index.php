<?php
/**
 * صفحة إدارة الخدمات
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'services';
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="justify-content: space-between; align-items: center; display: flex;">
    <!-- Right Side: Title -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 class="page-title">
            <i class="fa-solid fa-layer-group" style="color: #60a5fa;"></i>
            إدارة الخدمات
        </h1>
        <span class="glass-badge" style="font-size: 0.9rem;">
            <?= $total ?? 0 ?> خدمة
        </span>
        <a href="<?= BASE_URL ?>/admin/services/create" class="btn-glass-primary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">
            <i class="fa-solid fa-plus"></i> إضافة خدمة
        </a>
    </div>

    <!-- Left Side: Search Form -->
    <div style="width: 300px;">
        <form action="" method="GET" style="position: relative;">
            <i class="fa-solid fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" name="search" class="glass-input" placeholder="بحث عن خدمة..." value="<?= htmlspecialchars($search ?? '') ?>" style="padding-right: 1rem; padding-left: 2.5rem; width: 100%;">
        </form>
    </div>
</div>

<!-- جدول الفئات -->
<div class="glass-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <table class="glass-table">
        <thead>
            <tr>
                <th width="80">الأيقونة</th>
                    <th>اسم الخدمة</th>
                    <th>الوصف</th>
                    <th>الترتيب</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="fa-solid fa-layer-group" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                        <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: #e2e8f0;">لا توجد خدمات مضافة</h3>
                        <p style="margin: 0;">ابدأ بإضافة خدماتك لعرضها في الموقع</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td>
                        <div class="avatar-wrapper">
                            <?php if (!empty($service['image'])): ?>
                            <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($service['image']) ?>" 
                                 alt="<?= htmlspecialchars($service['name']) ?>" class="table-img">
                            <?php else: ?>
                            <div class="avatar-placeholder bg-blue-subtle text-blue">
                                <?= $service['icon'] ?? '🔧' ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold"><?= htmlspecialchars($service['name']) ?></div>
                        <div class="text-xs text-muted"><?= htmlspecialchars($service['slug']) ?></div>
                    </td>
                    <td class="text-wrap" style="max-width: 300px;">
                        <?= mb_substr(htmlspecialchars($service['description'] ?? ''), 0, 80) ?>
                        <?php if (mb_strlen($service['description'] ?? '') > 80) echo '...'; ?>
                    </td>
                    <td>
                        <span class="badge badge-secondary"><?= $service['sort_order'] ?></span>
                    </td>
                    <td>
                        <button onclick="toggleStatus(<?= $service['id'] ?>, this)" 
                                class="status-badge <?= $service['is_active'] ? 'status-active' : 'status-inactive' ?>">
                            <?= $service['is_active'] ? 'مفعل' : 'معطل' ?>
                        </button>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="<?= BASE_URL ?>/admin/services/edit/<?= $service['id'] ?>" class="btn-icon-glass text-blue" title="تعديل">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button onclick="showSystemConfirm('حذف الخدمة', 'هل أنت متأكد من حذف هذه الخدمة؟', () => deleteService(<?= $service['id'] ?>))" 
                                    class="btn-icon-glass text-red" title="حذف">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
</div>

<!-- التصفح -->
<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination-container">
    <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
    <a href="<?= BASE_URL ?>/admin/services?page=<?= $i ?><?= ($search ?? '') ? '&search=' . urlencode($search) : '' ?>" 
       class="pagination-link <?= $i == ($currentPage ?? 1) ? 'active' : '' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

function toggleStatus(id, btn) {
    fetch('<?= BASE_URL ?>/admin/services/toggle-status/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            btn.textContent = data.is_active ? 'مفعل' : 'معطل';
            btn.className = 'status-badge ' + (data.is_active ? 'status-active' : 'status-inactive');
            showSystemAlert(data.message, 'success');
        } else {
            showSystemAlert(data.message, 'error');
        }
    }).catch(err => showSystemAlert('حدث خطأ في الاتصال', 'error'));
}

function deleteService(id) {
    fetch('<?= BASE_URL ?>/admin/services/delete/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            location.reload();
        } else {
            showSystemAlert(data.message, 'error');
        }
    }).catch(err => showSystemAlert('حدث خطأ في الاتصال', 'error'));
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

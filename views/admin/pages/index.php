<?php
$currentPage = 'pages';
ob_start();
?>

<div class="section-header">
    <div class="section-title">
        <i class="fas fa-file-alt section-icon" style="color: var(--neon-blue);"></i>
        <span>إدارة الصفحات</span>
        <span class="glass-badge" style="margin-right: 10px; font-size: 0.75rem;"><?= count($pages ?? []) ?> صفحة</span>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/admin/pages/create" class="btn-glass-primary">
            <i class="fas fa-plus"></i> إضافة صفحة
        </a>
    </div>
</div>

<div class="glass-card table-responsive">
    <table class="modern-table">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>الرابط (Slug)</th>
                <th>القائمة</th>
                <th>الفوتر</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pages)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <br>
                    لا توجد صفحات مضافة
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($pages as $page): ?>
            <tr>
                <td>
                    <strong style="color: #fff; font-size: 1rem;"><?= htmlspecialchars($page['title']) ?></strong>
                </td>
                <td>
                    <div style="font-family: monospace; background: rgba(0, 0, 0, 0.2); padding: 4px 8px; border-radius: 6px; display: inline-block; color: #cbd5e1; direction: ltr;">
                        /<?= htmlspecialchars($page['slug']) ?>
                    </div>
                </td>
                <td>
                    <?php if ($page['show_in_menu']): ?>
                        <span class="glass-badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; border-color: rgba(16, 185, 129, 0.2);">
                            <i class="fas fa-check"></i> نعم
                        </span>
                    <?php else: ?>
                        <span class="glass-badge" style="background: rgba(148, 163, 184, 0.1); color: #94a3b8; border-color: rgba(148, 163, 184, 0.2);">
                            لا
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($page['show_in_footer']): ?>
                        <span class="glass-badge" style="background: rgba(16, 185, 129, 0.1); color: #34d399; border-color: rgba(16, 185, 129, 0.2);">
                            <i class="fas fa-check"></i> نعم
                        </span>
                    <?php else: ?>
                        <span class="glass-badge" style="background: rgba(148, 163, 184, 0.1); color: #94a3b8; border-color: rgba(148, 163, 184, 0.2);">
                            لا
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <button onclick="toggleStatus(<?= $page['id'] ?>, this)" 
                            class="glass-badge" 
                            style="border: 1px solid var(--glass-border); cursor: pointer; <?= $page['is_active'] ? 'background: rgba(16, 185, 129, 0.1); color: #34d399;' : 'background: rgba(239, 68, 68, 0.1); color: #ef4444;' ?>">
                        <?= $page['is_active'] ? 'مفعل' : 'معطل' ?>
                    </button>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="<?= BASE_URL ?>/page/<?= $page['slug'] ?>" target="_blank" class="btn-icon-glass" title="عرض الصفحة">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/pages/edit/<?= $page['id'] ?>" class="btn-icon-glass" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deletePage(<?= $page['id'] ?>)" class="btn-icon-glass text-danger-soft" title="حذف" style="border-color: rgba(239, 68, 68, 0.3);">
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

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

function toggleStatus(id, btn) {
    fetch('<?= BASE_URL ?>/admin/pages/toggle-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            btn.textContent = data.is_active ? 'مفعل' : 'معطل';
            if (data.is_active) {
                btn.style.background = 'rgba(16, 185, 129, 0.1)';
                btn.style.color = '#34d399';
            } else {
                btn.style.background = 'rgba(239, 68, 68, 0.1)';
                btn.style.color = '#ef4444';
            }
        }
    });
}

function deletePage(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الصفحة؟ لا يمكن التراجع عن هذا الإجراء.')) return;
    
    fetch('<?= BASE_URL ?>/admin/pages/delete/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء الحذف');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

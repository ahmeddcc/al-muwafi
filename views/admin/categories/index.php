<?php
/**
 * صفحة إدارة الأقسام
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'categories';
ob_start();

$typeLabels = [
    'copier' => 'آلات تصوير',
    'printer' => 'طابعات',
    'spare_part' => 'قطع غيار',
    'service' => 'خدمات',
];

$typeIcons = [
    'copier' => 'fa-copy',
    'printer' => 'fa-print',
    'spare_part' => 'fa-cogs',
    'service' => 'fa-tools',
];
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cubes" style="color: #60a5fa;"></i>
        إدارة الأقسام
        <span class="glass-badge" style="margin-right: 15px; font-size: 0.85rem; padding: 5px 12px; border: 1px solid rgba(59, 130, 246, 0.3);"><?= count($categories) ?> قسم</span>
    </h1>
    <div style="display: flex; gap: 10px; align-items: center;">
        <div class="search-container" style="width: 250px;">
            <form action="" method="GET" style="width: 100%; position: relative;">
                <?php if ($currentType): ?>
                <input type="hidden" name="type" value="<?= $currentType ?>">
                <?php endif; ?>
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="بحث..." class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); color: #fff; padding-right: 40px; width: 100%;">
                <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer;">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <a href="<?= BASE_URL ?>/admin/categories/create" class="btn-glass-primary">
            <i class="fas fa-plus"></i> إضافة قسم
        </a>
    </div>
</div>

<!-- Bulk Actions Toolbar -->
<div id="bulk-actions" class="glass-card" style="margin-bottom: 2rem; padding: 0.75rem 1.5rem; display: none; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="color: #fca5a5; font-weight: 600;">تم تحديد <span id="selected-count">0</span> قسم</span>
        <button onclick="bulkDelete()" class="btn-glass-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-trash-alt"></i> حذف المحدد
        </button>
    </div>
    <button onclick="deselectAll()" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 0.9rem;">إلغاء التحديد</button>
</div>

<!-- Filters Tabs -->
<div class="glass-nav-tabs" style="margin-bottom: 2rem; display: flex; gap: 10px; flex-wrap: wrap;">
    <a href="<?= BASE_URL ?>/admin/categories" class="nav-tab-item <?= !$currentType ? 'active' : '' ?>">
        <i class="fas fa-th-large"></i> الكل
    </a>
    <?php foreach ($typeLabels as $type => $label): ?>
    <a href="<?= BASE_URL ?>/admin/categories?type=<?= $type ?>" class="nav-tab-item <?= $currentType === $type ? 'active' : '' ?>">
        <i class="fas <?= $typeIcons[$type] ?>"></i> <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Categories Table -->
<div class="glass-card">
    <table class="modern-table">
        <thead>
            <tr>
                <th style="width: 40px;">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="select-all" onclick="toggleSelectAll()">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th style="width: 50px;">#</th>
                <th style="width: 80px;">الصورة</th>
                <th>اسم القسم</th>
                <th>النوع</th>
                <th>المنتجات</th>
                <th>الحالة</th>
                <th style="text-align: left;">الإجراءات</th>
            </tr>
        </thead>
        <tbody id="sortable">
            <?php if (empty($categories)): ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>لا توجد أقسام مضافة حالياً</p>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($categories as $cat): ?>
            <tr data-id="<?= $cat['id'] ?>">
                <td>
                    <label class="custom-checkbox">
                        <input type="checkbox" class="row-checkbox" value="<?= $cat['id'] ?>" onchange="updateBulkActions()">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td style="cursor: grab;" title="سحب لإعادة الترتيب"><i class="fas fa-grip-vertical" style="color: #475569;"></i></td>
                <td>
                    <?php if ($cat['image']): ?>
                    <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($cat['image']) ?>" class="table-img-preview" onclick="openLightbox(this.src)">
                    <?php else: ?>
                    <div class="table-img-placeholder"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight: 700; color: #f1f5f9;"><?= htmlspecialchars($cat['name']) ?></div>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;"><?= htmlspecialchars($cat['slug']) ?></div>
                </td>
                <td>
                    <span class="badge-category-type">
                        <i class="fas <?= $typeIcons[$cat['type']] ?? 'fa-tag' ?>"></i>
                        <?= $typeLabels[$cat['type']] ?? $cat['type'] ?>
                    </span>
                </td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <span class="glass-badge" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); color: #cbd5e1;">
                             <i class="fas fa-box" style="color: #64748b; font-size: 0.8rem;"></i>
                             <span style="margin-right: 6px; font-weight: 700;">
                                <?php if ($cat['type'] === 'spare_part'): ?>
                                <?= $cat['parts_count'] ?>
                                <?php else: ?>
                                <?= $cat['products_count'] ?>
                                <?php endif; ?>
                             </span>
                        </span>
                    </div>
                </td>
                <td>
                    <label class="switch-toggle" title="تغيير الحالة">
                        <input type="checkbox" onchange="toggleStatus(<?= $cat['id'] ?>, this)" <?= $cat['is_active'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <div class="actions-cell">
                        <a href="<?= BASE_URL ?>/admin/categories/edit/<?= $cat['id'] ?>" class="btn-icon-glass" title="تعديل">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button onclick="deleteCategory(<?= $cat['id'] ?>)" class="btn-icon-glass text-danger" title="حذف">
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

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="close-lightbox">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
</div>



<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// Bulk Selection Logic
function toggleSelectAll() {
    const parent = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = parent.checked;
    });
    updateBulkActions();
}

function deselectAll() {
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    updateBulkActions();
}

function updateBulkActions() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    const bulkToolbar = document.getElementById('bulk-actions');
    const countSpan = document.getElementById('selected-count');
    
    if (selected.length > 0) {
        bulkToolbar.style.display = 'flex';
        countSpan.textContent = selected.length;
    } else {
        bulkToolbar.style.display = 'none';
        document.getElementById('select-all').checked = false;
    }
}

function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) return;
    

    
    showSystemConfirm('حذف جماعي', `هل أنت متأكد من حذف ${selected.length} قسم؟`, function() {
        fetch('<?= BASE_URL ?>/admin/categories/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ ids: selected, <?= CSRF_TOKEN_NAME ?>: csrfToken })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                showSystemAlert('فشل الحذف', data.error, 'error');
            }
        });
    });
}


function toggleStatus(id, checkbox) {
    const originalState = !checkbox.checked;
    fetch('<?= BASE_URL ?>/admin/categories/toggle-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (!data.success) {
            checkbox.checked = originalState; // Revert on failure
            showSystemAlert('خطأ', data.error || 'حدث خطأ أثناء تحديث الحالة', 'error');
        } 
    }).catch(err => {
        checkbox.checked = originalState;
    });
}

function deleteCategory(id) {
    showSystemConfirm('حذف القسم', 'هل أنت متأكد من رغبتك في حذف هذا القسم؟ لا يمكن التراجع عن هذا الإجراء.', function() {
        fetch('<?= BASE_URL ?>/admin/categories/delete/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                showSystemAlert('خطأ', data.error, 'error');
            }
        });
    });
}

// Lightbox
function openLightbox(src) {
    const lightbox = document.getElementById('lightbox');
    const img = document.getElementById('lightbox-img');
    img.src = src;
    lightbox.style.display = 'flex';
    setTimeout(() => lightbox.classList.add('active'), 10);
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    setTimeout(() => lightbox.style.display = 'none', 300);
}

// Sortable (Simple placeholder for now)
const sortable = document.getElementById('sortable');
// TODO: Implement SortableJS if needed
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

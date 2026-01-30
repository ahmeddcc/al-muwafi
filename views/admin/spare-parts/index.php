<?php
/**
 * صفحة عرض قطع الغيار (Technical Catalog)
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'spare-parts';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tools" style="color: #60a5fa;"></i>
        الكتالوج الفني لقطع الغيار
        <span class="glass-badge" style="margin-right: 15px; font-size: 0.9rem;">
            <?= $total ?? 0 ?> قطعة
        </span>
    </h1>
    <a href="<?= BASE_URL ?>/admin/spare-parts/create" class="btn-glass-primary">
        <i class="fas fa-plus"></i> إضافة قطعة جديدة
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="glass-card" style="padding: 1.2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem; flex: 1; align-items: center;">
        
        <div style="position: relative; flex: 1; max-width: 400px;">
            <i class="fas fa-search" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="ابحث (اسم، رقم، موديل ماكينة)..." class="form-control" style="padding-right: 45px;">
        </div>

        <div style="position: relative; width: 200px;">
            <i class="fas fa-filter" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
            <select name="category" class="form-control" style="padding-right: 45px; appearance: none;" onchange="this.form.submit()">
                <option value="">كل الأقسام</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <i class="fas fa-chevron-down" style="position: absolute; left: 15px; top: 14px; color: #64748b; pointer-events: none;"></i>
        </div>

        <button type="submit" class="btn-glass-secondary" style="height: 42px;">
            بحث
        </button>
    </form>
</div>

<!-- Glass Table -->
<div class="glass-card" style="padding: 0; overflow: hidden;">
    <table class="glass-table">
        <thead>
            <tr>
                <th width="80">الصورة</th>
                <th>اسم القطعة</th>
                <th>بيانات فنية</th>
                <th>التوافقية</th>
                <th>الحالة</th>
                <th class="text-left" style="padding-left: 2rem;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($spareParts)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-tools" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    لا توجد بيانات
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($spareParts as $part): ?>
            <tr>
                <td>
                    <?php if (!empty($part['image'])): ?>
                    <a href="javascript:void(0)" onclick="openLightbox('<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($part['image']) ?>', '<?= htmlspecialchars($part['name']) ?>')">
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($part['image']) ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 2px solid rgba(255,255,255,0.1);">
                    </a>
                    <?php else: ?>
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #64748b;">
                        ⚙️
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <span style="font-weight: 600; font-size: 1rem; color: #e2e8f0;"><?= htmlspecialchars($part['name']) ?></span>
                        <span style="font-family: monospace; color: #60a5fa; font-size: 0.85rem;">PN: <?= htmlspecialchars($part['part_number']) ?></span>
                        <?php if(!empty($part['tags_str'])): ?>
                            <div style="display: flex; gap: 3px; flex-wrap: wrap;">
                            <?php foreach(explode(',', $part['tags_str']) as $tag): ?>
                                <span class="glass-badge" style="font-size: 0.7rem; padding: 2px 6px; background: rgba(94, 234, 212, 0.1); color: #5eead4; border-color: rgba(94, 234, 212, 0.2);">
                                    <?= htmlspecialchars($tag) ?>
                                </span>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <span class="glass-badge" style="background: rgba(255, 255, 255, 0.05); color: #cbd5e1;">
                        <?= htmlspecialchars($part['category_name'] ?? 'عام') ?>
                    </span>
                </td>
                <td>
                    <?php if($part['compat_count'] > 0): ?>
                    <span class="glass-badge" style="background: rgba(14, 165, 233, 0.1); color: #38bdf8;">
                        <i class="fas fa-check-circle"></i> يناسب <?= $part['compat_count'] ?> ماكينة
                    </span>
                    <?php else: ?>
                    <span style="color: #64748b; font-size: 0.85rem;">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button onclick="toggleStatus(<?= $part['id'] ?>, this)" 
                            class="status-badge <?= $part['is_active'] ? 'status-active' : 'status-inactive' ?>"
                            style="border: none; cursor: pointer;">
                        <?= $part['is_active'] ? 'مفعل' : 'معطل' ?>
                    </button>
                </td>
                <td class="actions-cell">
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <button class="btn-icon" title="QR Code" onclick="alert('QR Code: <?= $part["part_number"] ?>')">
                            <i class="fas fa-qrcode"></i>
                        </button>
                        <a href="<?= BASE_URL ?>/admin/spare-parts/edit/<?= $part['id'] ?>" class="btn-icon" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deletePart(<?= $part['id'] ?>)" class="btn-icon delete" title="حذف">
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
<div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
    <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
    <a href="<?= BASE_URL ?>/admin/spare-parts?page=<?= $i ?><?= ($categoryId ?? '') ? '&category=' . $categoryId : '' ?><?= ($search ?? '') ? '&search=' . urlencode($search) : '' ?>" 
       class="btn-glass-secondary <?= $i == ($currentPage ?? 1) ? 'active' : '' ?>"
       style="<?= $i == ($currentPage ?? 1) ? 'background: var(--glass-highlight); border-color: var(--neon-blue); color: white;' : '' ?>">
       <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Lightbox Container -->
<div id="lightbox" class="lightbox">
    <span class="close-lightbox" onclick="closeLightbox()">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <div id="lightbox-caption" style="position: absolute; bottom: 30px; color: white; background: rgba(0,0,0,0.7); padding: 10px 20px; border-radius: 20px;"></div>
</div>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// Lightbox
function openLightbox(src, caption) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    
    lightbox.style.display = "flex";
    setTimeout(() => { lightbox.classList.add('active'); }, 10);
    
    lightboxImg.src = src;
    lightboxCaption.innerHTML = caption;
}
function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    setTimeout(() => { lightbox.style.display = "none"; }, 300);
}
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

// Toggle Status
function toggleStatus(id, btn) {
    // ... existing logic ...
    const originalText = btn.textContent;
    btn.textContent = '...';
    btn.style.opacity = '0.7';

    fetch('<?= BASE_URL ?>/admin/spare-parts/toggle-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            btn.textContent = data.newStatus ? 'مفعل' : 'معطل';
            btn.className = 'status-badge ' + (data.newStatus ? 'status-active' : 'status-inactive');
        } else {
            btn.textContent = originalText;
            alert('خطأ في التحديث');
        }
    }).catch(() => {
        btn.textContent = originalText;
    });
}

// Delete Part
function deletePart(id) {
    if (!confirm('هل أنت متأكد من حذف هذه القطعة نهائياً؟')) return;
    
    fetch('<?= BASE_URL ?>/admin/spare-parts/delete/' + id, {
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

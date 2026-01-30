<?php
/**
 * صفحة إدارة المنتجات (تصميم زجاجي جديد)
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'products';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-box-open" style="color: #60a5fa;"></i>
        إدارة المنتجات
        <span class="glass-badge" style="margin-right: 15px; font-size: 0.9rem;">
            <?= $total ?> منتج
        </span>
    </h1>
    <a href="<?= BASE_URL ?>/admin/products/create" class="btn-glass-primary">
        <i class="fas fa-plus"></i> إضافة منتج جديد
    </a>
</div>

<!-- Search & Filter Bar -->
<div class="glass-card" style="padding: 1.2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem; flex: 1; align-items: center;">
        
        <div style="position: relative; flex: 1; max-width: 400px;">
            <i class="fas fa-search" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="ابحث عن منتج..." class="form-control" style="padding-right: 45px;">
        </div>

        <div style="position: relative; width: 200px;">
            <i class="fas fa-filter" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
            <select name="category" class="form-control" style="padding-right: 45px; appearance: none;" onchange="this.form.submit()">
                <option value="">كل الأقسام</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $currentCategory == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <i class="fas fa-chevron-down" style="position: absolute; left: 15px; top: 14px; color: #64748b; pointer-events: none;"></i>
        </div>

        <button type="submit" class="btn-glass-secondary">
            بحث
        </button>
    </form>
</div>

<!-- Products Table -->
<div class="glass-card" style="overflow: hidden; padding: 0;">
    <table class="glass-table">
        <thead>
            <tr>
                <th width="80">الصورة</th>
                <th>اسم المنتج</th>
                <th>القسم</th>
                <th>الموديل</th>
                <th>المشاهدات</th>
                <th>الحالة</th>
                <th class="text-left" style="padding-left: 2rem;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    لا توجد منتجات مطابقة للبحث
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php if ($product['thumbnail']): ?>
                    <a href="javascript:void(0)" onclick="openLightbox('<?= BASE_URL ?>/storage_proxy.php?path=<?= urlencode($product['thumbnail']) ?>&v=3', '<?= htmlspecialchars($product['name']) ?>')">
                        <img src="<?= BASE_URL ?>/storage_proxy.php?path=<?= urlencode($product['thumbnail']) ?>&v=3" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px; border: 2px solid rgba(255,255,255,0.1); transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.1)'" 
                             onmouseout="this.style.transform='scale(1)'">
                    </a>
                    <?php else: ?>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        🖨️
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: 600; font-size: 0.95rem; color: #e2e8f0;"><?= htmlspecialchars($product['name']) ?></span>
                        <span style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($product['slug']) ?></span>
                    </div>
                </td>
                <td>
                    <span class="glass-badge" style="background: rgba(59, 130, 246, 0.1); color: #93c5fd;">
                        <?= htmlspecialchars($product['category_name'] ?? 'عام') ?>
                    </span>
                </td>
                <td style="color: #cbd5e1;"><?= htmlspecialchars($product['model'] ?? '-') ?></td>
                <td>
                    <span style="display: inline-flex; align-items: center; gap: 5px; color: #94a3b8;">
                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i> <?= $product['views_count'] ?>
                    </span>
                </td>
                <td>
                    <button onclick="toggleStatus(<?= $product['id'] ?>, this)" 
                            class="status-badge <?= $product['is_active'] ? 'status-active' : 'status-inactive' ?>"
                            style="border: none; cursor: pointer; transition: all 0.3s ease;">
                        <?= $product['is_active'] ? 'نشط' : 'معطل' ?>
                    </button>
                </td>
                <td class="actions-cell">
                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                        <a href="<?= BASE_URL ?>/products/show/<?= $product['slug'] ?>" target="_blank" class="btn-icon" title="عرض في المتجر">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product['id'] ?>" class="btn-icon" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn-icon delete" title="حذف">
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
<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
    <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
    <a href="<?= BASE_URL ?>/admin/products?page=<?= $i ?><?= $currentCategory ? '&category=' . $currentCategory : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
       class="btn-glass-secondary <?= $i == $currentPage ? 'active' : '' ?>" 
       style="<?= $i == $currentPage ? 'background: var(--glass-highlight); border-color: var(--neon-blue); color: white;' : '' ?>">
       <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Lightbox Container -->
<div id="lightbox" class="lightbox">
    <span class="close-lightbox" onclick="closeLightbox()">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <div id="lightbox-caption"></div>
</div>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

// Lightbox Functions
function openLightbox(src, caption) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    
    lightbox.style.display = "flex";
    setTimeout(() => { lightbox.classList.add('show'); }, 10);
    
    lightboxImg.src = src;
    lightboxCaption.innerHTML = caption;
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('show');
    setTimeout(() => { lightbox.style.display = "none"; }, 300);
}

// Close lightbox on click outside or escape key
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});
document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") closeLightbox();
});

// Toggle Status
function toggleStatus(id, btn) {
    const originalText = btn.textContent;
    btn.textContent = '...';
    btn.style.opacity = '0.7';

    fetch('<?= BASE_URL ?>/admin/products/toggle-status/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            btn.textContent = data.is_active ? 'نشط' : 'معطل';
            btn.className = 'status-badge ' + (data.is_active ? 'status-active' : 'status-inactive');
        } else {
            btn.textContent = originalText;
            alert('حدث خطأ أثناء تحديث الحالة');
        }
    }).catch(() => {
        btn.textContent = originalText;
    }).finally(() => {
        btn.style.opacity = '1';
    });
}

// Delete Product
function deleteProduct(id) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج نهائياً؟')) return;
    
    fetch('<?= BASE_URL ?>/admin/products/delete/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
    }).then(res => res.json()).then(data => {
        if (data.success) {
            // Animate row removal
            const row = document.querySelector(`button[onclick="deleteProduct(${id})"]`).closest('tr');
            row.style.transition = 'all 0.5s ease';
            row.style.transform = 'translateX(100px)';
            row.style.opacity = '0';
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert(data.error);
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

<?php
/**
 * صفحة إضافة/تعديل صفحة
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Security;

$currentPage = 'pages';
$isEdit = !empty($page);
$title = $isEdit ? 'تعديل الصفحة: ' . $page['title'] : 'إضافة صفحة جديدة';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-file-alt" style="color: #60a5fa;"></i>
        <?= htmlspecialchars($title) ?>
        <?php if ($isEdit): ?>
        <span class="glass-badge" style="margin-right: 15px;"><?= htmlspecialchars($page['title']) ?></span>
        <?php endif; ?>
    </h1>
    <a href="<?= BASE_URL ?>/admin/pages" class="btn-glass-primary">
        <i class="fas fa-arrow-right"></i> عودة للقائمة
    </a>
</div>

<div class="glass-card" style="width: 95%; margin: 0 auto; animation: fadeInUp 0.5s ease;">
    <div style="padding: 2rem;">
        
        <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
            <?= $_SESSION['flash']['error'] ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>/admin/pages/<?= $isEdit ? 'update/' . $page['id'] : 'store' ?>" method="POST">
            <?= Security::csrfField() ?>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                
                <!-- Main Info -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <div class="form-group">
                        <label class="form-label">عنوان الصفحة *</label>
                        <div style="position: relative;">
                            <i class="fas fa-heading" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title'] ?? '') ?>" placeholder="مثال: من نحن" required style="padding-right: 45px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">المحتوى</label>
                        <textarea name="content" class="form-control" rows="12" placeholder="محتوى الصفحة HTML..."><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                        <div style="margin-top: 8px; font-size: 0.85rem; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-info-circle"></i> يمكنك استخدام أكواد HTML لتنسيق المحتوى.
                        </div>
                    </div>

                </div>

                <!-- Sidebar Info -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Settings -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); border: 1px dashed rgba(255,255,255,0.1);">
                        <h4 style="color: #cbd5e1; font-size: 1rem; margin-bottom: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-cogs"></i> خصائص الصفحة
                        </h4>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">تفعيل الصفحة</label>
                                <label class="switch-toggle" title="تفعيل / تعطيل">
                                    <input type="checkbox" name="is_active" value="1" <?= ($page['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">القائمة الرئيسية</label>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="show_in_menu" value="1" <?= ($page['show_in_menu'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">الفوتر (تذييل)</label>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="show_in_footer" value="1" <?= ($page['show_in_footer'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div class="form-group">
                        <label class="form-label">ترتيب العرض</label>
                        <div style="position: relative;">
                            <i class="fas fa-sort-numeric-down" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                            <input type="number" name="sort_order" class="form-control" value="<?= $page['sort_order'] ?? 0 ?>" min="0" style="padding-right: 45px;">
                        </div>
                        <small style="color: #64748b; margin-top: 5px; display: block;">الرقم الأقل يظهر أولاً.</small>
                    </div>

                </div>
            </div>

            <!-- SEO Section -->
            <div style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.5rem;">
                <h4 style="color: #94a3b8; font-size: 1rem; margin-bottom: 1rem; font-weight: 600;">
                    <i class="fas fa-search"></i> تحسين محركات البحث (SEO)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">عنوان SEO</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" placeholder="اتركه فارغاً لاستخدام العنوان الرئيسي">
                    </div>
                    <div class="form-group">
                        <label class="form-label">وصف SEO</label>
                        <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($page['meta_description'] ?? '') ?>" placeholder="وصف مختصر يظهر في نتائج البحث">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="<?= BASE_URL ?>/admin/pages" class="btn-glass-danger" style="text-decoration: none;">إلغاء</a>
                <button type="submit" class="btn-glass-primary" style="padding: 10px 30px; font-size: 1rem;">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'حفظ التعديلات' : 'إضافة الصفحة' ?>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add custom animation styles dynamically if needed
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

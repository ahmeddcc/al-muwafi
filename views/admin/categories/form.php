<?php
/**
 * صفحة إضافة/تعديل قسم
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'categories';
$isEdit = !empty($category);
$title = $isEdit ? 'تعديل القسم' : 'إضافة قسم جديد';
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
        <i class="fas fa-edit" style="color: #60a5fa;"></i>
        <?= $title ?>
        <?php if ($isEdit): ?>
        <span class="glass-badge" style="margin-right: 15px;"><?= htmlspecialchars($category['name']) ?></span>
        <?php endif; ?>
    </h1>
    <a href="<?= BASE_URL ?>/admin/categories" class="btn-glass-primary">
        <i class="fas fa-arrow-right"></i> عودة للأقسام
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
        
        <form action="<?= BASE_URL ?>/admin/categories/<?= $isEdit ? 'update/' . $category['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
            <?= $csrf_field ?>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                
                <!-- Main Info -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    

                    <!-- Row: Name, Type, Parent -->
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">اسم القسم *</label>
                            <div style="position: relative;">
                                <i class="fas fa-heading" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name'] ?? '') ?>" placeholder="مثال: ماكينات تصوير ألوان" required style="padding-right: 45px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">نوع القسم *</label>
                            <div style="position: relative;">
                                <i class="fas fa-tags" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                                <select name="type" class="form-control" required style="padding-right: 45px; appearance: none;">
                                    <?php foreach ($typeLabels as $type => $label): ?>
                                    <option value="<?= $type ?>" <?= ($category['type'] ?? '') === $type ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down" style="position: absolute; left: 15px; top: 14px; color: #64748b; pointer-events: none;"></i>
                            </div>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">القسم الأب</label>
                            <div style="position: relative;">
                                <i class="fas fa-level-up-alt" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                                <select name="parent_id" class="form-control" style="padding-right: 45px; appearance: none;">
                                    <option value="">بدون (رئيسي)</option>
                                    <?php foreach ($parentCategories as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down" style="position: absolute; left: 15px; top: 14px; color: #64748b; pointer-events: none;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="وصف قصير للقسم..."><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    </div>

                    <!-- SEO Fields (Moved Here) -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); border: 1px dashed rgba(255,255,255,0.05);">
                        <h5 style="color: #94a3b8; margin-bottom: 1rem; font-size: 0.9rem;"><i class="fas fa-search"></i> تحسين محركات البحث (SEO)</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label">عنوان SEO</label>
                                <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($category['meta_title'] ?? '') ?>" placeholder="اتركه فارغاً للافتراضي">
                            </div>
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label">وصف SEO</label>
                                <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($category['meta_description'] ?? '') ?>" placeholder="وصف مختصر للبحث">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar Info (Image & Status) -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Image Upload -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); border: 1px dashed rgba(255,255,255,0.1); text-align: center;">
                        <label class="form-label" style="margin-bottom: 1rem; display: block;">صورة القسم</label>
                        
                        <div style="position: relative; width: 100%; aspect-ratio: 16/9; background: rgba(0,0,0,0.2); border-radius: 12px; overflow: hidden; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                            <img id="image-preview" src="<?= ($isEdit && $category['image']) ? BASE_URL . '/storage/uploads/' . htmlspecialchars($category['image']) : '' ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: <?= ($isEdit && $category['image']) ? 'block' : 'none' ?>;">
                            
                            <div id="image-placeholder" style="display: <?= ($isEdit && $category['image']) ? 'none' : 'flex' ?>; flex-direction: column; align-items: center; color: #64748b;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; margin-bottom: 8px;"></i>
                                <span style="font-size: 0.8rem;">اختر صورة</span>
                            </div>
                        </div>

                        <input type="file" name="image" id="image-input" class="form-control" accept="image/*" style="opacity: 0; position: absolute; z-index: -1;">
                        <button type="button" onclick="document.getElementById('image-input').click()" class="btn-glass-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-image"></i> تغيير الصورة
                        </button>
                    </div>

                    <!-- Status -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); display: flex; align-items: center; justify-content: space-between;">
                        <label class="form-label" style="margin: 0;">حالة التفعيل</label>
                        <label class="switch-toggle" title="تفعيل / تعطيل">
                            <input type="checkbox" name="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </div>

                </div>
            </div>



            <!-- Actions -->
            <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="<?= BASE_URL ?>/admin/categories" class="btn-glass-danger" style="text-decoration: none;">إلغاء</a>
                <button type="submit" class="btn-glass-primary" style="padding: 10px 30px; font-size: 1rem;">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'حفظ التعديلات' : 'إضافة القسم' ?>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// Image Preview Logic
document.getElementById('image-input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
});

// Animation
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

<?php
/**
 * نموذج إضافة/تعديل خدمة
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Security;

$currentPage = 'services';
$isEdit = !empty($service);
ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= $isEdit ? 'تعديل الخدمة' : 'إضافة خدمة جديدة' ?></h1>
        <p class="text-muted">أدخل تفاصيل الخدمة بعناية ليتم عرضها في الموقع</p>
    </div>
    <div class="header-actions">
        <a href="<?= BASE_URL ?>/admin/services" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-right"></i> إلغاء ورجوع
        </a>
    </div>
</div>

<form action="<?= BASE_URL ?>/admin/services/<?= $isEdit ? 'update/' . $service['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
    <?= Security::csrfField() ?>
    
    <div class="grid-form-layout">
        <!-- 1. البطاقة الأولى: المعلومات الأساسية -->
        <div class="glass-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-info-circle"></i> المعلومات الأساسية</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error mb-4">
                    <?= $_SESSION['flash']['error'] ?>
                    <?php unset($_SESSION['flash']['error']); ?>
                </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">اسم الخدمة (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="glass-input" 
                               value="<?= htmlspecialchars($service['name'] ?? '') ?>" required
                               placeholder="مثال: صيانة الطابعات">
                    </div>
                    
                    <div class="form-group col-6">
                        <label class="form-label">اسم الخدمة (إنجليزي)</label>
                        <input type="text" name="name_ar" class="glass-input text-left" 
                               value="<?= htmlspecialchars($service['name_ar'] ?? '') ?>"
                               placeholder="Example: Printer Maintenance">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class="form-label">الوصف (عربي)</label>
                        <textarea name="description" class="glass-input" rows="8" 
                                  placeholder="اكتب وصفاً تفصيلياً للخدمة..."><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group col-6">
                        <label class="form-label">الوصف (إنجليزي)</label>
                        <textarea name="description_ar" class="glass-input text-left" rows="8" 
                                  placeholder="Description in English..."><?= htmlspecialchars($service['description_ar'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 2. البطاقة الثانية: الوسائط -->
        <div class="glass-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-image"></i> الوسائط</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">الأيقونة (Emoji)</label>
                    <input type="text" name="icon" class="glass-input text-center text-2xl" 
                           value="<?= htmlspecialchars($service['icon'] ?? '🔧') ?>"
                           placeholder="🔧">
                </div>
                
                <div class="form-group">
                    <label class="form-label">صورة الخدمة</label>
                    <?php if ($isEdit && !empty($service['image'])): ?>
                    <div class="current-image mb-3">
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($service['image']) ?>" 
                             class="rounded-lg shadow-sm w-100">
                    </div>
                    <?php endif; ?>
                    
                    <div class="file-upload-wrapper">
                        <input type="file" name="image" class="file-upload-input" accept="image/*" id="serviceImage">
                        <label for="serviceImage" class="file-upload-label">
                            <i class="fa-solid fa-cloud-upload-alt"></i>
                            <span>اختر صورة جديدة</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. البطاقة الثالثة: الحالة والنشر -->
        <div class="glass-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-toggle-on"></i> حالة النشر</h3>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="glass-input" min="0" value="<?= $service['sort_order'] ?? 0 ?>">
                    <small class="text-muted">الأرقام الأقل تظهر أولاً</small>
                </div>
                
                <div class="toggle-switch-wrapper">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" <?= ($service['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span class="slider round"></span>
                    </label>
                    <span>تفعيل الخدمة</span>
                </div>
                
                <hr class="glass-separator">
                
                <button type="submit" class="btn btn-primary w-100 mb-2">
                    <i class="fa-solid fa-save"></i> <?= $isEdit ? 'حفظ التعديلات' : 'نشر الخدمة' ?>
                </button>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

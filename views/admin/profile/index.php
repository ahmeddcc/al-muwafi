<?php
/**
 * صفحة الملف الشخصي
 * نظام المُوَفِّي لخدمات ريكو
 */

use App\Services\Security;

$currentPage = 'profile';
ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">الملف الشخصي</h1>
        <p class="text-muted">تحديث بياناتك الشخصية وكلمة المرور</p>
    </div>
</div>

<form action="<?= BASE_URL ?>/admin/profile/update" method="POST" enctype="multipart/form-data">
    <?= Security::csrfField() ?>
    
    <div class="row" style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <!-- 1. الشريط الجانبي: الصورة والحالة -->
        <div class="col-md-4" style="flex: 1; min-width: 300px; max-width: 400px;">
            <div class="glass-card text-center" style="position: sticky; top: 100px;">
                <div class="card-body" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem;">
                    <div class="profile-image-wrapper mb-4" style="position: relative; display: inline-block; margin: 0 auto;">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($user['avatar']) ?>" 
                             class="rounded-circle shadow-lg profile-preview-img" 
                             style="width: 160px; height: 160px; object-fit: cover; border: 4px solid rgba(255,255,255,0.1); border-radius: 50%;">
                        <?php else: ?>
                        <div class="avatar-placeholder rounded-circle shadow-lg profile-preview-img" 
                             style="width: 160px; height: 160px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: 4px solid rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 4rem; color: white; display: block; line-height: 1;">
                                <?= mb_substr($user['full_name'] ?? 'U', 0, 1) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <label for="avatarInput" class="btn-icon-glass" 
                               style="position: absolute; bottom: 5px; right: 5px; width: 45px; height: 45px; background: var(--neon-blue); color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s;">
                            <i class="fa-solid fa-camera"></i>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </label>
                    </div>
                    
                    <h3 class="mb-1" style="color: #fff; font-weight: 700; font-size: 1.5rem; margin-top: 0.5rem;"><?= htmlspecialchars($user['full_name']) ?></h3>
                    <p class="text-muted mb-3" dir="ltr" style="text-align: center; width: 100%; font-size: 0.95rem; opacity: 0.8;">@<?= htmlspecialchars($user['username']) ?></p>
                    
                    <div class="badge badge-primary px-3 py-2" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        <?= htmlspecialchars($user['role_name_ar'] ?? 'مستخدم') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. المحتوى الرئيسي: نموذج البيانات -->
        <div class="col-md-8" style="flex: 2; min-width: 300px;">
            <div class="glass-card">
                <div class="card-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; color: #f1f5f9;"><i class="fa-solid fa-user-edit text-neon ms-2"></i> البيانات الشخصية</h3>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($_SESSION['flash']['error'])): ?>
                    <div class="alert alert-error mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2);">
                        <i class="fa-solid fa-triangle-exclamation ms-2"></i>
                        <?= $_SESSION['flash']['error'] ?>
                        <?php unset($_SESSION['flash']['error']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['flash']['success'])): ?>
                    <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2);">
                        <i class="fa-solid fa-check-circle ms-2"></i>
                        <?= $_SESSION['flash']['success'] ?>
                        <?php unset($_SESSION['flash']['success']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                        <div class="col-md-6" style="flex: 1; min-width: 250px;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">الاسم الكامل <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper" style="position: relative;">
                                <i class="fa-regular fa-user" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #64748b;"></i>
                                <input type="text" name="full_name" class="glass-input" style="padding-right: 40px;" 
                                       value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6" style="flex: 1; min-width: 250px;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">اسم المستخدم <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper" style="position: relative;">
                                <i class="fa-solid fa-at" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #64748b;"></i>
                                <input type="text" name="username" class="glass-input" style="padding-right: 40px;"
                                       value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                        <div class="col-md-6" style="flex: 1; min-width: 250px;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper" style="position: relative;">
                                <i class="fa-regular fa-envelope" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #64748b;"></i>
                                <input type="email" name="email" class="glass-input" style="padding-right: 40px; text-align: left; direction: ltr;" 
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6" style="flex: 1; min-width: 250px;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">رقم الهاتف</label>
                            <div class="input-icon-wrapper" style="position: relative;">
                                <i class="fa-solid fa-phone" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #64748b;"></i>
                                <input type="text" name="phone" class="glass-input" style="padding-right: 40px; text-align: left; direction: ltr;"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="glass-separator" style="margin: 2rem 0; border-color: rgba(255,255,255,0.05);">
                    
                    <h4 class="mb-3 text-neon" style="color: #f1f5f9; display: flex; align-items: center; gap: 10px;">
                        <span style="background: rgba(14, 165, 233, 0.1); padding: 8px; border-radius: 8px;"><i class="fa-solid fa-lock text-neon"></i></span>
                        تغيير كلمة المرور
                    </h4>
                    
                    <div class="form-group mb-4">
                        <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: #94a3b8;">كلمة المرور الجديدة</label>
                        <div class="input-icon-wrapper" style="position: relative;">
                            <i class="fa-solid fa-key" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #64748b;"></i>
                            <input type="password" name="password" class="glass-input" style="padding-right: 40px;"
                                   placeholder="اتركها فارغة إذا كنت لا تريد تغييرها">
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 5px; color: #64748b;">يجب أن تكون 6 أحرف على الأقل</small>
                    </div>
                    
                    <div class="mt-4 text-end" style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary px-5" style="min-width: 200px; padding: 12px 24px; font-weight: 600; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <i class="fa-solid fa-save"></i> حفظ التغييرات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const img = input.parentElement.previousElementSibling;
            if (img.tagName === 'IMG') {
                img.src = e.target.result;
            } else {
                // If placeholder exists, replace it with img
                const wrapper = input.parentElement.parentElement;
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.className = 'rounded-circle shadow-lg';
                newImg.style = 'width: 150px; height: 150px; object-fit: cover; border: 4px solid rgba(255,255,255,0.1);';
                wrapper.replaceChild(newImg, img);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

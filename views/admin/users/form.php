<?php
/**
 * صفحة إضافة/تعديل مستخدم (Glassmorphism Redesign)
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'users';
$isEdit = !empty($user);
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="<?= BASE_URL ?>/admin/users" class="btn-icon" style="background: rgba(255,255,255,0.1); width: 40px; height: 40px;" title="عودة">
            <i class="fas fa-arrow-right"></i>
        </a>
        <h1 class="page-title">
            <i class="fas fa-user-<?= $isEdit ? 'edit' : 'plus' ?>" style="color: #60a5fa;"></i>
            <?= $isEdit ? 'تعديل بيانات المستخدم' : 'إضافة مستخدم جديد' ?>
        </h1>
    </div>
</div>

<div class="glass-card" style="padding: 2rem;">
    <?php if (!empty($_SESSION['flash']['error'])): ?>
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
        <div><?= $_SESSION['flash']['error'] ?></div>
        <?php unset($_SESSION['flash']['error']); ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/users/<?= $isEdit ? 'update/' . $user['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
        <?= $csrf_field ?>
        
        <div class="user-form-grid">
            <!-- Right Column: Basic Info -->
            <div class="form-section">
                <h3 style="color: #f1f5f9; margin-bottom: 1.5rem; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                    <i class="fas fa-info-circle text-primary" style="margin-left: 8px;"></i> البيانات الأساسية
                </h3>
                
                <div class="form-group">
                    <label class="glass-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="full_name" class="glass-input with-icon" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required placeholder="مثال: أحمد محمد">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="glass-label">اسم المستخدم <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-at input-icon"></i>
                            <input type="text" name="username" class="glass-input with-icon" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required placeholder="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="glass-label">رقم الهاتف</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" name="phone" class="glass-input with-icon" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="05xxxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="glass-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="glass-input with-icon" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="email@example.com">
                    </div>
                </div>

                <div class="form-group">
                    <label class="glass-label">الدور والصلاحية <span class="text-danger">*</span></label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <select name="role_id" class="glass-input with-icon" required>
                            <option value="" style="background: #1e293b;">اختر الدور...</option>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" style="background: #1e293b;" <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['name_ar']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="is_active" value="1" <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span class="checkmark"></span>
                        <span class="label-text">تفعيل الحساب (يمكن للمستخدم تسجيل الدخول)</span>
                    </label>
                </div>
                
                <?php 
                // خيار إخفاء المستخدم - يظهر فقط لمدير النظام
                $currentUser = \App\Services\Auth::user();
                $isSuperAdmin = ($currentUser['role_id'] ?? 0) == 1;
                if ($isSuperAdmin): 
                ?>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="custom-checkbox" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); padding: 12px; border-radius: 8px;">
                        <input type="checkbox" name="is_hidden" value="1" <?= ($user['is_hidden'] ?? 0) ? 'checked' : '' ?>>
                        <span class="checkmark" style="border-color: #f59e0b;"></span>
                        <span class="label-text" style="color: #fbbf24;">
                            <i class="fas fa-eye-slash" style="margin-left: 5px;"></i>
                            إخفاء المستخدم من المديرين والفريق
                        </span>
                    </label>
                    <small style="color: #94a3b8; display: block; margin-top: 5px; padding-right: 10px;">
                        المستخدمون المخفيون لن يظهروا لأي شخص غير مدير النظام (Super Admin)
                    </small>
                </div>
                <?php endif; ?>
            </div>

            <!-- Left Column: Avatar & Password -->
            <div class="form-section">
                <!-- Avatar Upload -->
                <div class="avatar-upload-container">
                    <div class="avatar-preview" id="avatarPreview">
                        <?php if ($isEdit && $user['avatar']): ?>
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($user['avatar']) ?>" id="previewImage">
                        <?php else: ?>
                        <div class="avatar-placeholder" id="avatarPlaceholder">
                            <?= $isEdit ? mb_substr($user['full_name'], 0, 1) : '<i class="fas fa-camera"></i>' ?>
                        </div>
                        <img src="" id="previewImage" style="display: none;">
                        <?php endif; ?>
                        
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                            <span>تغيير</span>
                        </div>
                    </div>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display: none;" onchange="previewFile(this)">
                    <button type="button" class="btn-glass-secondary btn-sm" onclick="document.getElementById('avatarInput').click()" style="margin-top: 1rem;">
                        <i class="fas fa-upload"></i> رفع صورة شخصية
                    </button>
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.5rem;">يفضل صورة مربعة، الحد الأقصى 2MB</p>
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0;">

                <h3 style="color: #f1f5f9; margin-bottom: 1.5rem; font-size: 1.1rem;">
                    <i class="fas fa-lock text-primary" style="margin-left: 8px;"></i> الأمان وكلمة المرور
                </h3>

                <div class="form-group">
                    <label class="glass-label"><?= $isEdit ? 'كلمة المرور الجديدة' : 'كلمة المرور *' ?></label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" class="glass-input with-icon" <?= $isEdit ? '' : 'required' ?> minlength="8" placeholder="********">
                    </div>
                    <?php if ($isEdit): ?>
                    <small style="color: #64748b; font-size: 0.8rem; margin-top: 5px; display: block;">اتركها فارغة إذا لم ترد تغييرها</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="glass-label">تأكيد كلمة المرور</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-check-double input-icon"></i>
                        <input type="password" name="password_confirm" class="glass-input with-icon" placeholder="********">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="<?= BASE_URL ?>/admin/users" class="btn-glass-secondary">
                إلغاء
            </a>
            <button type="submit" class="btn-glass-primary" style="min-width: 150px; justify-content: center;">
                <i class="fas fa-save"></i> <?= $isEdit ? 'حفظ التغييرات' : 'إنشاء المستخدم' ?>
            </button>
        </div>
    </form>
</div>

<style>
.user-form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}
@media (max-width: 768px) {
    .user-form-grid {
        grid-template-columns: 1fr;
    }
    .form-section:last-child {
        order: -1; /* Avatar first on mobile */
    }
}
.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.glass-label {
    display: block;
    color: #cbd5e1;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    font-weight: 500;
}
.input-icon-wrapper {
    position: relative;
}
.input-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    z-index: 1;
}
.glass-input.with-icon {
    padding-right: 2.5rem;
}
.glass-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 0.8rem 1rem;
    color: #fff;
    transition: all 0.3s ease;
    font-family: inherit;
}
.glass-input:focus {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(96, 165, 250, 0.5);
    outline: none;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
}
.text-danger { color: #f87171; }
.text-primary { color: #60a5fa; }

/* Custom Checkbox */
.custom-checkbox {
    display: flex;
    align-items: center;
    position: relative;
    padding-right: 30px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 0.95rem;
    color: #e2e8f0;
    user-select: none;
}
.custom-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.checkmark {
    position: absolute;
    right: 0;
    top: 0;
    height: 22px;
    width: 22px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.2s ease;
}
.custom-checkbox:hover input ~ .checkmark {
    background-color: rgba(255, 255, 255, 0.2);
}
.custom-checkbox input:checked ~ .checkmark {
    background-color: #10b981;
    border-color: #10b981;
}
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}
.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}
.custom-checkbox .checkmark:after {
    left: 7px;
    top: 3px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}

/* Avatar Upload Styling */
.avatar-upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background: rgba(255,255,255,0.02);
    border-radius: 12px;
    border: 1px dashed rgba(255,255,255,0.1);
}
.avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    position: relative;
    overflow: hidden;
    background: rgba(255,255,255,0.05);
    border: 4px solid rgba(255,255,255,0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-preview:hover {
    border-color: #60a5fa;
}
.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.avatar-placeholder {
    font-size: 3rem;
    color: rgba(255,255,255,0.3);
    font-weight: bold;
}
.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
}
.avatar-preview:hover .avatar-overlay {
    opacity: 1;
}

/* Buttons */
.btn-glass-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-glass-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
}
.btn-glass-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    cursor: pointer;
}
.btn-glass-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    transform: translateY(-2px);
}
</style>

<script>
function previewFile(input) {
    const preview = document.getElementById('previewImage');
    const placeholder = document.getElementById('avatarPlaceholder');
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

// Click on avatar to trigger file input
document.getElementById('avatarPreview').addEventListener('click', function() {
    document.getElementById('avatarInput').click();
});
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

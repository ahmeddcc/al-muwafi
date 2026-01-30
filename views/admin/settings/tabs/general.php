<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-cogs text-primary"></i> إعدادات النظام العامة</h3>
        <p class="glass-subtitle">التحكم في حالة الموقع وخيارات العرض.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-general" method="POST">
        <?= $csrf_field ?>
        
        <div class="security-toggles-grid">
            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">وضع الصيانة (Maintenance Mode)</span>
                    <span class="toggle-desc">إغلاق الموقع مؤقتاً للزوار وعرض رسالة صيانة.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="maintenance_mode" value="1" <?= ($general['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>

            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">وضع "قريباً" (Coming Soon)</span>
                    <span class="toggle-desc">عرض صفحة انتظار للزوار قبل الاطلاق الرسمي.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="coming_soon" value="1" <?= ($general['coming_soon'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>
            
            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">تفعيل نظام التذاكر</span>
                    <span class="toggle-desc">السماح للعملاء بفتح تذاكر صيانة جديدة.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="tickets_enabled" value="1" <?= ($general['tickets_enabled'] ?? 1) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>
        </div>

        <div class="form-group" style="margin-top: 2rem; max-width: 300px;">
            <label class="glass-label">عدد المنتجات في الصفحة</label>
            <input type="number" name="products_per_page" class="glass-input" value="<?= htmlspecialchars($general['products_per_page'] ?? 12) ?>">
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ الإعدادات</button>
        </div>
    </form>
</div>

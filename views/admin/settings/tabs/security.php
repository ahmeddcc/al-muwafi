<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-shield-alt text-primary"></i> إعدادات الأمان المتقدمة</h3>
        <p class="glass-subtitle">تحكم في خيارات الحماية ومنع النسخ وتامين النظام.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-security" method="POST">
        <?= $csrf_field ?>
        
        <div class="security-toggles-grid">
            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">تعطيل الزر الأيمن (Right Click)</span>
                    <span class="toggle-desc">منع الزوار من استخدام القائمة المختصرة في الموقع.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="disable_right_click" value="1" <?= ($security['disable_right_click'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>

            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">تعطيل أدوات المطور (Inspect)</span>
                    <span class="toggle-desc">منع فتح نافذة فحص العناصر في المتصفح.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="disable_inspect" value="1" <?= ($security['disable_inspect'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>
            
            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">تعطيل النسخ (Copy/Paste)</span>
                    <span class="toggle-desc">منع تحديد النصوص ونسخها من الموقع.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="disable_copy" value="1" <?= ($security['disable_copy'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
            </label>

            <label class="toggle-card">
                <div class="toggle-info">
                    <span class="toggle-title">الحماية من الإغراق (Rate Limiting)</span>
                    <span class="toggle-desc">تحديد عدد الطلبات المسموحة في الدقيقة لمنع الهجمات.</span>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="rate_limiting" value="1" <?= ($security['rate_limiting'] ?? 1) ? 'checked' : '' ?> onchange="toggleRateInputs(this.checked)">
                    <span class="slider round"></span>
                </div>
            </label>
        </div>

        <div id="rate_limit_options" style="margin-top: 1.5rem; display: <?= ($security['rate_limiting'] ?? 1) ? 'grid' : 'none' ?>; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1rem; background: rgba(59, 130, 246, 0.05); border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.1);">
            <div class="form-group">
                <label class="glass-label">عدد الطلبات المسموح</label>
                <input type="number" name="rate_limit_requests" class="glass-input" value="<?= htmlspecialchars($security['rate_limit_requests'] ?? 100) ?>">
            </div>
            <div class="form-group">
                <label class="glass-label">خلال فترة (ثانية)</label>
                <input type="number" name="rate_limit_window" class="glass-input" value="<?= htmlspecialchars($security['rate_limit_window'] ?? 60) ?>">
            </div>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ الإعدادات</button>
        </div>
    </form>
</div>

<style>
.security-toggles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
}

.toggle-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.toggle-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.1);
}

.toggle-info { display: flex; flex-direction: column; gap: 4px; }
.toggle-title { color: #f1f5f9; font-weight: 500; font-size: 0.95rem; }
.toggle-desc { color: #94a3b8; font-size: 0.8rem; }

/* Switch Styles */
.toggle-switch-wrapper {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}
.toggle-switch-wrapper input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: #334155; transition: .4s;
}
.slider:before {
    position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
    background-color: white; transition: .4s;
}
.slider.round { border-radius: 24px; }
.slider.round:before { border-radius: 50%; }
input:checked + .slider { background-color: #3b82f6; }
input:checked + .slider:before { transform: translateX(22px); }
</style>

<script>
function toggleRateInputs(checked) {
    document.getElementById('rate_limit_options').style.display = checked ? 'grid' : 'none';
}
</script>

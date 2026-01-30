<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-images text-primary"></i> معالجة الصور</h3>
        <p class="glass-subtitle">تحكم في العلامة المائية وإزالة الخلفيات وتنسيقات الصور.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-images" method="POST" enctype="multipart/form-data">
        <?= $csrf_field ?>
        
        <!-- Background Removal -->
        <label class="toggle-card" style="margin-bottom: 2rem;">
            <div class="toggle-info">
                <span class="toggle-title">إزالة الخلفية تلقائياً (Remove Background)</span>
                <span class="toggle-desc">محاولة إزالة الخلفية البيضاء من صور المنتجات الجديدة.</span>
            </div>
            <div class="toggle-switch-wrapper">
                <input type="checkbox" name="remove_background_enabled" value="1" <?= ($images['remove_background_enabled'] ?? 0) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </div>
        </label>

        <h4 style="color: #e2e8f0; margin-bottom: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 1rem;">
            <i class="fas fa-stamp" style="margin-left: 8px; color: #60a5fa;"></i>
            إعدادات العلامة المائية (Watermark)
        </h4>

        <!-- تفعيل العلامة المائية -->
        <label class="toggle-card" style="margin-bottom: 1.5rem;">
            <div class="toggle-info">
                <span class="toggle-title">تفعيل العلامة المائية</span>
                <span class="toggle-desc">إضافة العلامة المائية على صور المنتجات الجديدة تلقائياً.</span>
            </div>
            <div class="toggle-switch-wrapper">
                <input type="checkbox" name="watermark_enabled" value="1" <?= ($images['watermark_enabled'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </div>
        </label>

        <div class="settings-grid-2">
            <!-- العمود الأيمن -->
            <div class="settings-col">
                <div class="form-group">
                    <label class="glass-label">صورة العلامة المائية (PNG)</label>
                    <input type="file" name="watermark_logo" accept="image/png,image/jpeg,image/gif" class="glass-input" style="padding: 10px;">
                    
                    <?php if (file_exists(APP_PATH . '/storage/uploads/watermark/logo.png')): ?>
                    <div style="margin-top: 12px; padding: 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 10px; display: flex; align-items: center; gap: 12px;">
                        <img src="<?= BASE_URL ?>/storage/uploads/watermark/logo.png?t=<?= time() ?>" height="45" alt="Current Watermark" style="border-radius: 6px; background: rgba(255,255,255,0.1); padding: 5px;">
                        <div>
                            <span style="font-size: 0.85rem; color: #34d399; font-weight: 600;">
                                <i class="fas fa-check-circle" style="margin-left: 5px;"></i> مفعلة حالياً
                            </span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div style="margin-top: 12px; padding: 12px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 10px;">
                        <span style="font-size: 0.85rem; color: #fbbf24;">
                            <i class="fas fa-info-circle" style="margin-left: 5px;"></i> لم يتم رفع صورة للعلامة المائية بعد
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="glass-label">موضع العلامة</label>
                    <select name="watermark_position" class="glass-input">
                        <?php 
                        $positions = [
                            'bottom-right' => 'أسفل اليمين',
                            'bottom-left' => 'أسفل اليسار', 
                            'top-right' => 'أعلى اليمين',
                            'top-left' => 'أعلى اليسار',
                            'center' => 'المنتصف'
                        ];
                        foreach($positions as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($images['watermark_position'] ?? 'bottom-right') == $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- العمود الأيسر -->
            <div class="settings-col">
                <div class="form-group">
                    <label class="glass-label">شفافية العلامة المائية</label>
                    
                    <div class="opacity-mode-wrapper">
                        <label class="radio-pill <?= ($images['watermark_opacity_mode'] ?? 'auto') == 'auto' ? 'active' : '' ?>">
                            <input type="radio" name="watermark_opacity_mode" value="auto" <?= ($images['watermark_opacity_mode'] ?? 'auto') == 'auto' ? 'checked' : '' ?> onclick="toggleOpacitySlider(false); updateRadioPills(this);">
                            <span><i class="fas fa-magic"></i> تلقائي (50%)</span>
                        </label>
                        <label class="radio-pill <?= ($images['watermark_opacity_mode'] ?? '') == 'manual' ? 'active' : '' ?>">
                            <input type="radio" name="watermark_opacity_mode" value="manual" <?= ($images['watermark_opacity_mode'] ?? '') == 'manual' ? 'checked' : '' ?> onclick="toggleOpacitySlider(true); updateRadioPills(this);">
                            <span><i class="fas fa-sliders-h"></i> يدوي</span>
                        </label>
                    </div>
                    
                    <div id="opacity-slider-wrap" style="display: <?= ($images['watermark_opacity_mode'] ?? 'auto') == 'manual' ? 'block' : 'none' ?>; margin-top: 1rem;">
                        <div class="slider-container">
                            <input type="range" name="watermark_opacity" id="opacitySlider" min="10" max="100" step="5" class="glass-slider" value="<?= $images['watermark_opacity'] ?? 50 ?>" oninput="updateOpacityValue(this.value)">
                            <div class="slider-value" id="opacityValue"><?= $images['watermark_opacity'] ?? 50 ?>%</div>
                        </div>
                        <div class="slider-hints">
                            <span>خفيفة</span>
                            <span>قوية</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions-footer" style="margin-top: 2rem;">
            <button type="submit" class="btn-glass-primary">
                <i class="fas fa-save" style="margin-left: 8px;"></i> حفظ الإعدادات
            </button>
        </div>
    </form>
</div>

<style>
/* Grid layout fixed */
.settings-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

@media (max-width: 768px) {
    .settings-grid-2 {
        grid-template-columns: 1fr;
    }
}

.settings-col {
    display: flex;
    flex-direction: column;
}

/* Toggle Card Styles */
.toggle-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 1.25rem;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.toggle-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(96, 165, 250, 0.3);
}
.toggle-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.toggle-title {
    color: #e2e8f0;
    font-weight: 600;
    font-size: 1rem;
}
.toggle-desc {
    color: #94a3b8;
    font-size: 0.85rem;
}

/* Switch Styles */
.toggle-switch-wrapper {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
    flex-shrink: 0;
}
.toggle-switch-wrapper input { 
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(255, 255, 255, 0.1);
    transition: .4s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
input:checked + .slider {
    background-color: #60a5fa;
}
input:checked + .slider:before {
    transform: translateX(24px);
}
.slider.round {
    border-radius: 34px;
}
.slider.round:before {
    border-radius: 50%;
}

/* Opacity mode wrapper */
.opacity-mode-wrapper {
    display: flex;
    gap: 10px;
    margin-top: 0.5rem;
}

.radio-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #94a3b8;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.radio-pill:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(96, 165, 250, 0.3);
}

.radio-pill.active {
    background: rgba(96, 165, 250, 0.15);
    border-color: rgba(96, 165, 250, 0.4);
    color: #60a5fa;
}

.radio-pill input {
    display: none;
}

/* Slider container */
.slider-container {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.05);
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-slider {
    flex: 1;
    -webkit-appearance: none;
    appearance: none;
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    outline: none;
}

.glass-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.4);
    transition: transform 0.2s;
}

.glass-slider::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

.slider-value {
    min-width: 50px;
    text-align: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: #60a5fa;
}

.slider-hints {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    padding: 0 5px;
    font-size: 0.75rem;
    color: #64748b;
}
</style>

<script>
function toggleOpacitySlider(show) {
    document.getElementById('opacity-slider-wrap').style.display = show ? 'block' : 'none';
}

function updateOpacityValue(value) {
    document.getElementById('opacityValue').innerText = value + '%';
}

function updateRadioPills(clickedInput) {
    document.querySelectorAll('.radio-pill').forEach(pill => pill.classList.remove('active'));
    clickedInput.closest('.radio-pill').classList.add('active');
}
</script>

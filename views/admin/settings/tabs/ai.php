<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-brain text-primary"></i> الذكاء الاصطناعي (AI)</h3>
        <p class="glass-subtitle">إعدادات الاتصال بنماذج الذكاء الاصطناعي لتوليد المحتوى تلقائياً.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-ai" method="POST">
        <?= $csrf_field ?>
        
        <div class="form-group">
            <label class="toggle-card" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);">
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="enabled" value="1" <?= ($ai['enabled'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
                <div class="toggle-info">
                    <span class="toggle-title" style="color: #34d399;">تفعيل المساعد الذكي</span>
                    <span class="toggle-desc">سيظهر زر "توليد بالذكاء الاصطناعي" في النماذج (المنتجات، المقالات...).</span>
                </div>
            </label>
        </div>
        
        <div class="form-group" style="margin-top: 1.5rem;">
            <label class="glass-label">API Key</label>
            <div class="input-icon-wrapper">
                <i class="fas fa-key input-icon"></i>
                <input type="password" name="api_key" class="glass-input with-icon code-font" 
                       value="<?= htmlspecialchars($ai['api_key'] ?? '') ?>" placeholder="sk-...">
            </div>
        </div>
        
        <div class="form-group">
            <label class="glass-label">API Endpoint URL</label>
            <input type="url" name="api_url" class="glass-input code-font" 
                   value="<?= htmlspecialchars($ai['api_url'] ?? 'https://api.openai.com/v1/chat/completions') ?>">
            <small class="helper-text">الافتراضي: OpenAI. يمكن تغييره لاستخدام Local LLM أو خدمات أخرى.</small>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ مفاتيح الربط</button>
        </div>
    </form>
</div>

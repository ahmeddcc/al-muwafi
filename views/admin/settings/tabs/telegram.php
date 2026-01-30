<div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-robot text-primary"></i> بوت المالك (Owner Bot)</h3>
        <p class="glass-subtitle">يستقبل إشعارات فورية بالتذاكر، التقارير المالية، وتنبيهات الأخطاء.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-telegram-owner" method="POST">
        <?= $csrf_field ?>
        
        <div class="grid-2">
            <div class="form-group">
                <label class="glass-label">Bot Token</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="text" name="owner_bot_token" class="glass-input with-icon code-font" 
                           value="<?= htmlspecialchars($telegram['owner_bot_token'] ?? $telegram['bot_token'] ?? '') ?>">
                </div>
                <small class="helper-text">احصل عليه من <a href="#" style="color: #60a5fa;">@BotFather</a></small>
            </div>
            
            <div class="form-group">
                <label class="glass-label">Chat ID (للمالك)</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-id-badge input-icon"></i>
                    <input type="text" name="owner_chat_id" class="glass-input with-icon code-font" 
                           value="<?= htmlspecialchars($telegram['owner_chat_id'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label class="toggle-card" style="width: fit-content;">
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="owner_enabled" value="1" <?= ($telegram['owner_enabled'] ?? $telegram['notifications_enabled'] ?? 1) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
                <div class="toggle-info">
                    <span class="toggle-title">تفعيل الإشعارات</span>
                </div>
            </label>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ الإعدادات</button>
            <button type="button" onclick="testOwnerBot()" class="btn-glass-secondary">اختبار الاتصال 🔄</button>
        </div>
    </form>
</div>

<!-- Support Bot Section -->
<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-headset text-primary"></i> بوت الدعم الفني (Support Bot)</h3>
        <p class="glass-subtitle">مخصص للفنيين لاستقبال التذاكر وتحديث حالتها. يمكن ربطه بجروب.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-telegram-support" method="POST">
        <?= $csrf_field ?>
        
        <div class="grid-2">
            <div class="form-group">
                <label class="glass-label">Bot Token (للدعم)</label>
                <input type="text" name="support_bot_token" class="glass-input code-font" 
                       value="<?= htmlspecialchars($telegram['support_bot_token'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="glass-label">Chat ID (جروب الفنيين)</label>
                <input type="text" name="support_chat_id" class="glass-input code-font" 
                       value="<?= htmlspecialchars($telegram['support_chat_id'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label class="toggle-card" style="width: fit-content;">
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" name="support_enabled" value="1" <?= ($telegram['support_enabled'] ?? 0) ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </div>
                <div class="toggle-info">
                    <span class="toggle-title">تفعيل النظام للفنيين</span>
                </div>
            </label>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ الإعدادات</button>
            <button type="button" onclick="testSupportBot()" class="btn-glass-secondary">اختبار الاتصال 🔄</button>
        </div>
    </form>
</div>

<script>
function testOwnerBot() {
    fetch('<?= BASE_URL ?>/admin/settings/test-telegram-owner')
        .then(r => r.json())
        .then(data => showSystemAlert(data.message || data.error, data.success ? 'success' : 'error'));
}
function testSupportBot() {
    fetch('<?= BASE_URL ?>/admin/settings/test-telegram-support')
        .then(r => r.json())
        .then(data => showSystemAlert(data.message || data.error, data.success ? 'success' : 'error'));
}
</script>

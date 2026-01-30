<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-share-alt text-primary"></i> منصات التواصل الاجتماعي</h3>
        <p class="glass-subtitle">أدخل روابط صفحات الشركة، ستظهر الأيقونات تلقائياً في الفوتر وصفحة "اتصل بنا".</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-social" method="POST">
        <?= $csrf_field ?>
        
        <div class="social-grid">
            <?php 
            $platforms = [
                'facebook' => ['label' => 'Facebook', 'icon' => 'fa-facebook-f', 'color' => '#1877f2'],
                'twitter' => ['label' => 'Twitter (X)', 'icon' => 'fa-twitter', 'color' => '#1da1f2'],
                'instagram' => ['label' => 'Instagram', 'icon' => 'fa-instagram', 'color' => '#e1306c'],
                'linkedin' => ['label' => 'LinkedIn', 'icon' => 'fa-linkedin-in', 'color' => '#0077b5'],
                'youtube' => ['label' => 'YouTube', 'icon' => 'fa-youtube', 'color' => '#ff0000']
            ];
            
            foreach ($platforms as $key => $p): 
            ?>
            <div class="form-group social-input-group">
                <label class="glass-label"><?= $p['label'] ?></label>
                <div class="input-icon-wrapper">
                    <div class="icon-holder" style="background: <?= $p['color'] ?>20; color: <?= $p['color'] ?>;">
                        <i class="fab <?= $p['icon'] ?>"></i>
                    </div>
                    <input type="url" name="<?= $key ?>" class="glass-input with-icon" 
                           value="<?= htmlspecialchars($social[$key] ?? '') ?>" 
                           placeholder="https://..." dir="ltr">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">
                <i class="fas fa-save"></i> حفظ الروابط
            </button>
        </div>
    </form>
</div>

<style>
.social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.icon-holder {
    position: absolute;
    left: 10px;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    z-index: 2;
}

.glass-input.with-icon {
    padding-left: 3.5rem; /* Space for icon */
}
</style>

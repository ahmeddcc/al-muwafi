<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-link text-primary"></i> إدارة روابط الفوتر</h3>
        <p class="glass-subtitle">اختر الصفحات التي تظهر في قائمة الروابط السريعة أسفل الموقع ورتبها.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-footer-links" method="POST">
        <?= $csrf_field ?>
        
        <div class="glass-table-wrapper">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>اسم الصفحة</th>
                        <th class="text-center" width="120">إظهار</th>
                        <th class="text-center" width="100">ترتيب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $footerLinksData = $menuLinks ?? [
                        ['slug' => 'products', 'title' => 'المنتجات', 'show_in_footer' => 1, 'sort_order' => 1],
                        ['slug' => 'services', 'title' => 'الخدمات', 'show_in_footer' => 1, 'sort_order' => 2],
                        ['slug' => 'spare-parts', 'title' => 'قطع الغيار', 'show_in_footer' => 1, 'sort_order' => 3],
                        ['slug' => 'about', 'title' => 'من نحن', 'show_in_footer' => 0, 'sort_order' => 4],
                        ['slug' => 'contact', 'title' => 'اتصل بنا', 'show_in_footer' => 0, 'sort_order' => 5],
                        ['slug' => 'maintenance', 'title' => 'طلب صيانة', 'show_in_footer' => 1, 'sort_order' => 6],
                    ];
                    foreach ($footerLinksData as $link): 
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="links[<?= $link['slug'] ?>][slug]" value="<?= $link['slug'] ?>">
                            <div class="link-info" style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-file-alt" style="color: #64748b;"></i>
                                <span style="font-weight: 500; color: #e2e8f0;"><?= htmlspecialchars($link['title']) ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="switch-toggle sm">
                                <input type="checkbox" name="links[<?= $link['slug'] ?>][show_in_footer]" value="1" <?= ($link['show_in_footer'] ?? 0) ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <input type="number" name="links[<?= $link['slug'] ?>][sort_order]" 
                                   value="<?= $link['sort_order'] ?? 0 ?>" 
                                   class="glass-input sm text-center">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary">حفظ الروابط</button>
        </div>
    </form>
</div>

<style>
.switch-toggle.sm {
    position: relative; display: inline-block; width: 34px; height: 18px;
}
.switch-toggle.sm .slider:before {
    height: 14px; width: 14px; left: 2px; bottom: 2px;
}
.switch-toggle.sm input:checked + .slider:before {
    transform: translateX(16px);
}
.glass-input.sm { padding: 0.3rem; font-size: 0.9rem; }
.text-center { text-align: center; }
</style>

<?php
/**
 * صفحة البحث عن تذكرة
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'maintenance';
ob_start();
?>

<section class="hero" style="padding: 3rem 0;">
    <div class="container">
        <h1>البحث عن تذكرة</h1>
        <p>أدخل رقم التذكرة للاستعلام عن حالتها</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 500px;">
        <div class="card">
            <div class="card-body" style="padding: 2rem;">
                
                <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <form action="<?= BASE_URL ?>/maintenance/search" method="GET">
                    <div class="form-group">
                        <label class="form-label">رقم التذكرة</label>
                        <input type="text" name="ticket_number" class="form-control" placeholder="مثال: TK2601150001" required autofocus style="font-size: 1.25rem; text-align: center; letter-spacing: 2px;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">بحث</button>
                </form>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: var(--gray);">ليس لديك رقم تذكرة؟</p>
            <a href="<?= BASE_URL ?>/maintenance" class="btn btn-secondary">طلب صيانة جديد</a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public_layout.php';
?>

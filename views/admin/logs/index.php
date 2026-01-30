<?php
/**
 * سجلات النظام
 * نظام المُوَفِّي لخدمات ريكو
 */

use App\Services\Security;

$activeSection = 'logs';
ob_start();

// Helper function to get badge class and icon for actions
function getActionStyle($action) {
    if (str_contains($action, 'login_success')) return ['class' => 'success', 'icon' => 'fa-check-circle', 'text' => 'تسجيل دخول ناجح', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)'];
    if (str_contains($action, 'login_failed')) return ['class' => 'danger', 'icon' => 'fa-times-circle', 'text' => 'فشل تسجيل الدخول', 'color' => '#f43f5e', 'bg' => 'rgba(244, 63, 94, 0.1)'];
    if (str_contains($action, 'logout')) return ['class' => 'warning', 'icon' => 'fa-sign-out-alt', 'text' => 'تسجيل خروج', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)'];
    if (str_contains($action, 'delete')) return ['class' => 'danger', 'icon' => 'fa-trash', 'text' => 'حذف سجل', 'color' => '#f43f5e', 'bg' => 'rgba(244, 63, 94, 0.1)'];
    if (str_contains($action, 'update')) return ['class' => 'info', 'icon' => 'fa-edit', 'text' => 'تعديل بيانات', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)'];
    if (str_contains($action, 'create')) return ['class' => 'primary', 'icon' => 'fa-plus-circle', 'text' => 'إضافة جديد', 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.1)'];
    
    return ['class' => 'secondary', 'icon' => 'fa-history', 'text' => $action, 'color' => '#94a3b8', 'bg' => 'rgba(148, 163, 184, 0.1)'];
}
?>

<!-- Page Header -->
<div class="page-header" style="justify-content: space-between; align-items: center; display: flex;">
    <!-- Right Side: Title -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 class="page-title">
            <i class="fas fa-history" style="color: #60a5fa;"></i>
            سجلات النظام
        </h1>
        <span class="glass-badge" style="font-size: 0.9rem;">
            مراقبة العمليات
        </span>
    </div>

    <!-- Left Side: Actions -->
    <div style="display: flex; gap: 1rem; align-items: center;">
        <?php if (!empty($logs)): ?>
        <form id="clearLogsForm" action="<?= BASE_URL ?>/admin/logs/clear" method="POST">
            <?= Security::csrfField() ?>
            <button type="button" onclick="confirmClearLogs()" class="btn-glass-danger" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                <i class="fas fa-trash-alt"></i> تنظيف السجلات
            </button>
        </form>
        <script>
        function confirmClearLogs() {
            showSystemConfirm(
                'تأكيد مسح السجلات',
                'هل أنت متأكد من مسح جميع سجلات النظام؟ لا يمكن التراجع عن هذا الإجراء.',
                function() {
                    document.getElementById('clearLogsForm').submit();
                }
            );
        }
        </script>
        <?php endif; ?>
    </div>
</div>

<!-- Search Bar Area -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end;">
    <div style="width: 100%; max-width: 400px;">
        <form action="" method="GET" style="position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input type="text" name="search" class="glass-input" placeholder="بحث باسم المستخدم، الـ IP، أو نوع العملية..." value="<?= htmlspecialchars($search ?? '') ?>" style="width: 100%; padding-left: 2.5rem;">
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="glass-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <table class="glass-table">
        <thead>
            <tr>
                <th style="padding-right: 1.5rem;">المستخدم</th>
                <th>نوع العملية</th>
                <th>تفاصيل العملية</th>
                <th>العنوان IP</th>
                <th style="padding-left: 1.5rem;">التوقيت</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fas fa-clipboard-check" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    لا توجد سجلات مطابقة
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($logs as $log): 
                $style = getActionStyle($log['action']);
            ?>
            <tr>
                <td style="padding-right: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <?php if (!empty($log['avatar'])): ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid rgba(255, 255, 255, 0.1);">
                            <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($log['avatar']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #334155, #475569); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; border: 2px solid rgba(255, 255, 255, 0.1); font-size: 1rem;">
                            <?= mb_substr($log['full_name'] ?? 'U', 0, 1) ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 600; color: #f1f5f9; font-size: 0.95rem;">
                                <?= htmlspecialchars($log['full_name'] ?? 'مستخدم محذوف') ?>
                            </span>
                            <span style="font-size: 0.8rem; color: #94a3b8;">
                                @<?= htmlspecialchars($log['username'] ?? 'deleted') ?>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="glass-badge" style="background: <?= $style['bg'] ?>; color: <?= $style['color'] ?>; border: 1px solid <?= $style['color'] ?>33; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas <?= $style['icon'] ?>"></i>
                        <?= $style['text'] ?>
                    </span>
                </td>
                <td>
                    <span style="color: #cbd5e1; font-size: 0.9rem; max-width: 350px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($log['description']) ?>">
                        <?= htmlspecialchars($log['description']) ?>
                    </span>
                </td>
                <td>
                    <span style="font-family: monospace; letter-spacing: 0.5px; color: #64748b; background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px; font-size: 0.85rem;" dir="ltr">
                        <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                    </span>
                </td>
                <td style="padding-left: 1.5rem;">
                    <div style="display: flex; flex-direction: column; align-items: flex-end;" dir="ltr">
                        <span style="color: #e2e8f0; font-size: 0.9rem; font-weight: 500;">
                            <?= date('H:i', strtotime($log['created_at'])) ?>
                        </span>
                        <span style="color: #64748b; font-size: 0.75rem;">
                            <?= date('Y-m-d', strtotime($log['created_at'])) ?>
                        </span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (($totalPages ?? 1) > 1): ?>
<div class="glass-pagination-wrapper" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; flex-wrap: wrap;">
    <?php if (($currentPage ?? 1) > 1): ?>
    <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>" class="glass-page-btn">
        <i class="fas fa-chevron-right"></i>
    </a>
    <?php endif; ?>
    
    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
    <a href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" class="glass-page-btn <?= $i == $currentPage ? 'active' : '' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
    
    <?php if (($currentPage ?? 1) < $totalPages): ?>
    <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>" class="glass-page-btn">
        <i class="fas fa-chevron-left"></i>
    </a>
    <?php endif; ?>
</div>

<style>
.glass-page-btn {
    min-width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #94a3b8;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.glass-page-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(96, 165, 250, 0.3);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
.glass-page-btn.active {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
}
</style>
<?php endif; ?>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

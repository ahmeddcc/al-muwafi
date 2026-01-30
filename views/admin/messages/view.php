<?php
$currentPage = 'messages';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="<?= BASE_URL ?>/admin/messages" class="btn-icon" style="background: rgba(255,255,255,0.1); width: 40px; height: 40px;" title="عودة">
            <i class="fas fa-arrow-right"></i>
        </a>
        <h1 class="page-title">
            <i class="fas fa-envelope-open" style="color: #60a5fa;"></i>
            <?= htmlspecialchars($message['subject'] ?? 'بدون موضوع') ?>
        </h1>
    </div>
    
    <button onclick="deleteMessage(<?= $message['id'] ?>)" class="btn-glass-danger">
        <i class="fas fa-trash-alt"></i> حذف الرسالة
    </button>
</div>

<div class="glass-card" style="padding: 2rem;">
    <!-- Sender Info -->
    <div style="display: flex; align-items: center; gap: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; color: white;">
            <?= mb_substr($message['name'], 0, 1) ?>
        </div>
        
        <div style="flex: 1;">
            <h2 style="margin: 0 0 0.5rem 0; color: #f1f5f9; font-size: 1.25rem;">
                <?= htmlspecialchars($message['name']) ?>
            </h2>
            <div style="display: flex; gap: 1.5rem; color: #94a3b8; font-size: 0.9rem;">
                <span><i class="fas fa-envelope" style="margin-left: 5px;"></i> <?= htmlspecialchars($message['email'] ?? '-') ?></span>
                <span><i class="fas fa-phone" style="margin-left: 5px;"></i> <?= htmlspecialchars($message['phone'] ?? '-') ?></span>
                <span><i class="far fa-clock" style="margin-left: 5px;"></i> <?= date('Y/m/d H:i', strtotime($message['created_at'])) ?></span>
            </div>
        </div>
    </div>
    
    <!-- Message Body -->
    <div style="background: rgba(255, 255, 255, 0.03); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.05); color: #e2e8f0; line-height: 1.8; white-space: pre-wrap; margin-bottom: 2rem;">
        <?= htmlspecialchars($message['message'] ?? '') ?>
    </div>
    
    <!-- Actions -->
    <div style="display: flex; gap: 1rem;">
        <a href="mailto:<?= htmlspecialchars($message['email'] ?? '') ?>?subject=رد: <?= htmlspecialchars($message['subject'] ?? '') ?>" class="btn-glass-primary">
            <i class="fas fa-paper-plane"></i> الرد عبر البريد
        </a>
        
        <?php if ($message['phone']): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $message['phone']) ?>" target="_blank" class="btn-glass-secondary" style="color: #4ade80 !important; border-color: rgba(74, 222, 128, 0.3);">
            <i class="fab fa-whatsapp"></i> مراسلة واتساب
        </a>
        <?php endif; ?>
    </div>
</div>

<style>
.btn-glass-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #fca5a5;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}
.btn-glass-danger:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-2px);
}
.btn-glass-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-glass-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}
</style>

<script>
const csrfToken = '<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>';

function deleteMessage(id) {
    showSystemConfirm('حذف الرسالة', 'هل أنت متأكد من حذف هذه الرسالة؟', function() {
        fetch('<?= BASE_URL ?>/admin/messages/delete/' + id, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '<?= CSRF_TOKEN_NAME ?>=' + csrfToken
        }).then(res => res.json()).then(data => {
            if (data.success) window.location.href = '<?= BASE_URL ?>/admin/messages';
            else showSystemAlert(data.message, 'error');
        });
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

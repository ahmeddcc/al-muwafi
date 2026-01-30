<?php
/**
 * صفحة نجاح إنشاء التذكرة - التصميم السينمائي
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'maintenance';
$hideFooter = true;

$statusLabels = [
    'new' => 'جديدة',
    'received' => 'مستلمة',
    'in_progress' => 'قيد العمل',
    'fixed' => 'تم الإصلاح',
    'closed' => 'مغلقة',
];

ob_start();
?>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');

/* Global Reset */
* { box-sizing: border-box; margin: 0; padding: 0; }

/* Page Loader */
.page-loader {
    position: fixed; inset: 0; z-index: 9999;
    background: #020617;
    display: flex; align-items: center; justify-content: center;
    transition: opacity 0.5s, visibility 0.5s;
}
.page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.loader-spinner {
    width: 50px; height: 50px;
    border: 3px solid rgba(255,255,255,0.1);
    border-top-color: #D4AF37;
    border-radius: 50%;
    animation: loaderSpin 1s linear infinite;
}
@keyframes loaderSpin { to { transform: rotate(360deg); } }
@keyframes shine { to { background-position: 200% center; } }
@keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }
@keyframes successPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
@keyframes checkmark { 0% { stroke-dashoffset: 100; } 100% { stroke-dashoffset: 0; } }

/* Main Wrapper */
.success-wrapper {
    min-height: 100vh;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 120px 20px 40px;
    position: relative;
    background: #020617;
}

/* Tech Grid Background */
.success-wrapper::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image:
        linear-gradient(rgba(14, 165, 233, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(14, 165, 233, 0.05) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(circle at center, black 40%, transparent 90%);
    -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 90%);
    pointer-events: none;
    z-index: 1;
}

/* Floating Orb */
.success-wrapper::after {
    content: "";
    position: fixed;
    top: -100px; right: -100px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.2), transparent 70%);
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
    animation: orbFloat 20s ease-in-out infinite;
}

/* Glass Panel */
.success-panel {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 600px;
    background: rgba(2, 6, 23, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 25px 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 50px 100px -20px rgba(0,0,0,0.5),
        inset 0 0 0 1px rgba(255,255,255,0.05);
    text-align: center;
}

/* Golden Shine Border */
.success-panel::before {
    content: ""; position: absolute; inset: 0; border-radius: 24px; padding: 1px;
    background: linear-gradient(to right, #10b981, #34d399, #10b981); 
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    z-index: 2; pointer-events: none; background-size: 200% auto; animation: shine 5s linear infinite;
}

/* Success Icon - Smaller inline */
.success-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 8px;
}
.success-icon {
    width: 50px; height: 50px;
    background: linear-gradient(135deg, #10b981, #34d399);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
    flex-shrink: 0;
}
.success-icon i { font-size: 1.5rem; color: white; }

/* Text Styles */
.success-title {
    font-size: 1.6rem; font-weight: 800; color: white;
    font-family: 'Tajawal', sans-serif;
    margin: 0;
}
.success-subtitle {
    color: #94a3b8; font-size: 0.9rem; margin-bottom: 20px;
}

/* Ticket Number Box */
.ticket-number-box {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}
.ticket-info {
    text-align: right;
}
.ticket-label { color: #94a3b8; font-size: 0.8rem; margin-bottom: 2px; }
.ticket-number {
    font-size: 1.6rem; font-weight: 800; color: #34d399;
    letter-spacing: 1px; font-family: 'Tajawal', monospace;
}
.copy-btn {
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 8px;
    padding: 10px 15px;
    color: #34d399;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
}
.copy-btn:hover { background: rgba(16, 185, 129, 0.3); }
.copy-btn.copied { background: #10b981; color: white; }

/* Info Table */
.info-table {
    width: 100%;
    margin-bottom: 25px;
    border-collapse: separate;
    border-spacing: 0 8px;
}
.info-table tr {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 8px;
}
.info-table td {
    padding: 12px 16px;
}
.info-table td:first-child {
    color: #64748b;
    text-align: right;
    border-radius: 0 8px 8px 0;
}
.info-table td:last-child {
    color: #e2e8f0;
    font-weight: 500;
    text-align: left;
    border-radius: 8px 0 0 8px;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Note */
.save-note {
    background: rgba(251, 191, 36, 0.1);
    border: 1px solid rgba(251, 191, 36, 0.2);
    border-radius: 10px;
    padding: 12px;
    color: #fcd34d;
    font-size: 0.9rem;
    margin-bottom: 25px;
}
.save-note i { margin-left: 8px; }

/* Buttons */
.btn-group {
    display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;
}
.gold-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 30px;
    background: linear-gradient(135deg, #b48811 0%, #ecd76d 50%, #b48811 100%);
    border: none; border-radius: 12px;
    color: #000; font-weight: 700; font-size: 1rem;
    cursor: pointer; text-decoration: none;
    box-shadow: 0 10px 30px -10px rgba(234, 179, 8, 0.5);
    transition: 0.3s;
}
.gold-btn:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -10px rgba(234, 179, 8, 0.7); }

.glass-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 30px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    color: #94a3b8; font-weight: 600; font-size: 1rem;
    cursor: pointer; text-decoration: none;
    transition: 0.3s;
}
.glass-btn:hover { background: rgba(255, 255, 255, 0.1); color: white; transform: translateY(-3px); }

/* Responsive */
@media (max-width: 600px) {
    .success-panel { padding: 30px 20px; }
    .success-title { font-size: 1.5rem; }
    .ticket-number { font-size: 1.6rem; }
    .btn-group { flex-direction: column; }
    .gold-btn, .glass-btn { width: 100%; justify-content: center; }
}
</style>

<!-- Page Loader -->
<div class="page-loader" id="page-loader">
    <div class="loader-spinner"></div>
</div>

<div class="success-wrapper">
    <div class="success-panel">
        
        <!-- Success Header with inline icon -->
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">تم إنشاء التذكرة بنجاح!</h1>
        </div>
        <p class="success-subtitle">سنتواصل معك في أقرب وقت ممكن</p>
        
        <!-- Ticket Number with Copy Button -->
        <div class="ticket-number-box">
            <button class="copy-btn" onclick="copyTicketNumber()" id="copyBtn">
                <i class="fas fa-copy"></i>
                <span>نسخ</span>
            </button>
            <div class="ticket-info">
                <div class="ticket-label">رقم التذكرة</div>
                <div class="ticket-number" id="ticketNum"><?= htmlspecialchars($ticket['ticket_number']) ?></div>
            </div>
        </div>
        
        <!-- Info Table -->
        <table class="info-table">
            <tr>
                <td>الاسم:</td>
                <td><?= htmlspecialchars($ticket['customer_name']) ?></td>
            </tr>
            <tr>
                <td>الهاتف:</td>
                <td><?= htmlspecialchars($ticket['customer_phone']) ?></td>
            </tr>
            <tr>
                <td>نوع الجهاز:</td>
                <td><?= $ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة' ?></td>
            </tr>
            <tr>
                <td>الحالة:</td>
                <td><span class="status-badge"><?= $statusLabels[$ticket['status']] ?></span></td>
            </tr>
        </table>
        
        <!-- Save Note -->
        <div class="save-note">
            <i class="fas fa-bookmark"></i>
            احتفظ برقم التذكرة للاستعلام عن حالتها لاحقاً
        </div>
        
        <!-- Buttons -->
        <div class="btn-group">
            <a href="<?= BASE_URL ?>/maintenance/search?ticket_number=<?= urlencode($ticket['ticket_number']) ?>" class="gold-btn">
                <i class="fas fa-search-location"></i>
                تتبع التذكرة
            </a>
            <a href="<?= BASE_URL ?>" class="glass-btn">
                <i class="fas fa-home"></i>
                العودة للرئيسية
            </a>
        </div>
        
    </div>
</div>

<script>
    // Hide page loader when content is ready
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    });
    
    // Copy ticket number to clipboard
    function copyTicketNumber() {
        const ticketNum = document.getElementById('ticketNum').innerText;
        const copyBtn = document.getElementById('copyBtn');
        
        navigator.clipboard.writeText(ticketNum).then(function() {
            // Success feedback
            copyBtn.classList.add('copied');
            copyBtn.innerHTML = '<i class="fas fa-check"></i><span>تم النسخ!</span>';
            
            setTimeout(function() {
                copyBtn.classList.remove('copied');
                copyBtn.innerHTML = '<i class="fas fa-copy"></i><span>نسخ</span>';
            }, 2000);
        }).catch(function() {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = ticketNum;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            copyBtn.classList.add('copied');
            copyBtn.innerHTML = '<i class="fas fa-check"></i><span>تم النسخ!</span>';
            
            setTimeout(function() {
                copyBtn.classList.remove('copied');
                copyBtn.innerHTML = '<i class="fas fa-copy"></i><span>نسخ</span>';
            }, 2000);
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>

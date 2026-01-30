<?php
/**
 * صفحة حالة التذكرة - التصميم السينمائي
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

$statusColors = [
    'new' => '#00d4ff',
    'received' => '#f59e0b',
    'in_progress' => '#8b5cf6',
    'fixed' => '#10b981',
    'closed' => '#64748b',
];

$statusIcons = [
    'new' => 'fa-plus-circle',
    'received' => 'fa-inbox',
    'in_progress' => 'fa-wrench',
    'fixed' => 'fa-check-circle',
    'closed' => 'fa-lock',
];

ob_start();
?>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');

/* Global Reset */
* { box-sizing: border-box; margin: 0; padding: 0; }

/* Animations */
@keyframes shine { to { background-position: 200% center; } }
@keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }
@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }

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

/* Main Wrapper */
.status-wrapper {
    min-height: 100vh;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 120px 20px 40px;
    position: relative;
    background: #020617;
}

/* Tech Grid Background */
.status-wrapper::before {
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
.status-wrapper::after {
    content: "";
    position: fixed;
    top: -100px; right: -100px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%);
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
    animation: orbFloat 20s ease-in-out infinite;
}

/* Header Section */
.status-header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    z-index: 10;
}
.status-header h1 {
    font-size: 1.8rem; font-weight: 800; color: white;
    font-family: 'Tajawal', sans-serif;
    margin-bottom: 10px;
}
.status-header p {
    color: #94a3b8; font-size: 1rem;
}

/* Glass Panel */
.status-panel {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 900px;
    background: rgba(2, 6, 23, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 15px 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 50px 100px -20px rgba(0,0,0,0.5),
        inset 0 0 0 1px rgba(255,255,255,0.05);
}

/* Golden Shine Border */
.status-panel::before {
    content: ""; position: absolute; inset: 0; border-radius: 24px; padding: 1px;
    background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    z-index: 2; pointer-events: none; background-size: 200% auto; animation: shine 5s linear infinite;
}

/* Ticket Header */
.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}
.ticket-number-box {
    text-align: right;
}
.ticket-label { color: #64748b; font-size: 0.9rem; }
.ticket-number { 
    font-size: 1.8rem; font-weight: 800; color: white;
    font-family: 'Tajawal', monospace; letter-spacing: 1px;
}
.status-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 25px;
    border-radius: 50px;
    font-weight: 700; font-size: 1rem;
    color: white;
}

/* Progress Bar */
.progress-section {
    margin-bottom: 35px;
}
.progress-track {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 0 10px;
}
.progress-line {
    position: absolute;
    top: 20px; right: 40px; left: 40px;
    height: 4px;
    background: rgba(255,255,255,0.1);
    border-radius: 2px;
    z-index: 0;
}
.progress-line-fill {
    position: absolute;
    top: 20px; right: 40px;
    height: 4px;
    background: linear-gradient(90deg, #00d4ff, #10b981);
    border-radius: 2px;
    z-index: 1;
    transition: width 0.5s ease;
}
.progress-step {
    text-align: center;
    z-index: 2;
    flex: 1;
    max-width: 100px;
}
.step-circle {
    width: 40px; height: 40px;
    border-radius: 50%;
    margin: 0 auto 10px;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold;
    transition: 0.3s;
}
.step-circle.active {
    background: linear-gradient(135deg, #00d4ff, #10b981);
    color: white;
    box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
}
.step-circle.inactive {
    background: rgba(255,255,255,0.05);
    border: 2px solid rgba(255,255,255,0.1);
    color: #64748b;
}
.step-circle.current {
    animation: pulse 2s ease-in-out infinite;
}
.step-label {
    font-size: 0.75rem;
    color: #64748b;
    transition: 0.3s;
}
.step-label.active { color: #e2e8f0; }

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}
.info-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 20px;
}
.info-card-header {
    display: flex; align-items: center; gap: 10px;
    color: #cfaa5d; font-weight: 700; font-size: 1rem;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.info-row:last-child { border-bottom: none; }
.info-label { color: #64748b; }
.info-value { color: #e2e8f0; font-weight: 500; }

/* Timeline */
.timeline-section {
    margin-bottom: 25px;
}
.timeline-header {
    display: flex; align-items: center; gap: 10px;
    color: #cfaa5d; font-weight: 700; font-size: 1rem;
    margin-bottom: 20px;
}
.timeline {
    position: relative;
    padding-right: 25px;
}
.timeline::before {
    content: "";
    position: absolute;
    right: 5px; top: 0; bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #cfaa5d, rgba(207, 170, 93, 0.1));
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
    padding-right: 25px;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot {
    position: absolute;
    right: -3px; top: 5px;
    width: 12px; height: 12px;
    background: #cfaa5d;
    border-radius: 50%;
    border: 2px solid #020617;
}
.timeline-content {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
    padding: 15px;
}
.timeline-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.timeline-action { color: #e2e8f0; font-weight: 600; }
.timeline-date { color: #64748b; font-size: 0.8rem; }
.timeline-value { color: #94a3b8; font-size: 0.9rem; }

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
@media (max-width: 768px) {
    .status-wrapper { padding: 100px 15px 30px; }
    .status-header h1 { font-size: 1.8rem; }
    .status-panel { padding: 25px 20px; }
    .ticket-header { flex-direction: column; text-align: center; }
    .ticket-number-box { text-align: center; }
    .ticket-number { font-size: 1.4rem; }
    
    /* Progress - Vertical on mobile */
    .progress-track {
        flex-direction: column;
        align-items: flex-start;
        padding-right: 50px;
    }
    .progress-line, .progress-line-fill {
        width: 3px !important;
        height: auto;
        top: 20px; bottom: 20px;
        right: 18px; left: auto;
    }
    .progress-step {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        max-width: none;
    }
    .step-circle { margin: 0; flex-shrink: 0; }
    .step-label { text-align: right; }
    
    .info-grid { grid-template-columns: 1fr; }
    .btn-group { flex-direction: column; }
    .gold-btn, .glass-btn { width: 100%; justify-content: center; }
}

@media (max-width: 400px) {
    .status-header h1 { font-size: 1.5rem; }
    .status-panel { padding: 20px 15px; }
    .info-card { padding: 15px; }
}
</style>

<!-- Page Loader -->
<div class="page-loader" id="page-loader">
    <div class="loader-spinner"></div>
</div>

<div class="status-wrapper">
    
    <!-- Header -->
    <div class="status-header">
        <h1>حالة التذكرة</h1>
        <p>تتبع حالة طلب الصيانة الخاص بك</p>
    </div>
    
    <div class="status-panel">
        
        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="ticket-number-box">
                <div class="ticket-label">رقم التذكرة</div>
                <div class="ticket-number"><?= htmlspecialchars($ticket['ticket_number']) ?></div>
            </div>
            <div class="status-badge" style="background: <?= $statusColors[$ticket['status']] ?>;">
                <i class="fas <?= $statusIcons[$ticket['status']] ?>"></i>
                <?= $statusLabels[$ticket['status']] ?>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-section">
            <?php 
            $statuses = ['new', 'received', 'in_progress', 'fixed', 'closed'];
            $currentIndex = array_search($ticket['status'], $statuses);
            $progressWidth = ($currentIndex / 4) * 100;
            ?>
            <div class="progress-track">
                <div class="progress-line"></div>
                <div class="progress-line-fill" style="width: <?= $progressWidth ?>%;"></div>
                
                <?php foreach ($statuses as $i => $status): ?>
                <div class="progress-step">
                    <div class="step-circle <?= $i <= $currentIndex ? 'active' : 'inactive' ?> <?= $i == $currentIndex ? 'current' : '' ?>">
                        <?= $i <= $currentIndex ? '<i class="fas fa-check"></i>' : ($i + 1) ?>
                    </div>
                    <div class="step-label <?= $i <= $currentIndex ? 'active' : '' ?>"><?= $statusLabels[$status] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-user"></i>
                    معلومات العميل
                </div>
                <div class="info-row">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value"><?= htmlspecialchars($ticket['customer_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">الهاتف:</span>
                    <span class="info-value"><?= htmlspecialchars($ticket['customer_phone']) ?></span>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-cog"></i>
                    معلومات الجهاز
                </div>
                <div class="info-row">
                    <span class="info-label">النوع:</span>
                    <span class="info-value"><?= $ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">الموديل:</span>
                    <span class="info-value"><?= htmlspecialchars($ticket['machine_model'] ?? '-') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Timeline -->
        <?php if (!empty($timeline)): ?>
        <div class="timeline-section">
            <div class="timeline-header">
                <i class="fas fa-history"></i>
                سجل التحديثات
            </div>
            <div class="timeline">
                <?php 
                $actionLabels = [
                    'created' => 'تم إنشاء التذكرة',
                    'status_changed' => 'تغيير الحالة',
                    'assigned' => 'تم تعيين فني',
                    'note_added' => 'ملاحظة جديدة',
                ];
                foreach ($timeline as $event): 
                ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">
                            <span class="timeline-action"><?= $actionLabels[$event['action']] ?? $event['action'] ?></span>
                            <span class="timeline-date"><?= date('Y-m-d H:i', strtotime($event['created_at'])) ?></span>
                        </div>
                        <?php if ($event['new_value']): ?>
                        <div class="timeline-value"><?= htmlspecialchars($statusLabels[$event['new_value']] ?? $event['new_value']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Buttons -->
        <div class="btn-group">
            <a href="<?= BASE_URL ?>/maintenance" class="gold-btn">
                <i class="fas fa-plus"></i>
                طلب صيانة جديد
            </a>
            <a href="<?= BASE_URL ?>" class="glass-btn">
                <i class="fas fa-home"></i>
                العودة للرئيسية
            </a>
        </div>
        
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) loader.classList.add('hidden');
    });
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>

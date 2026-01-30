<?php
/**
 * مركز إعدادات النظام (Modern Settings Hub)
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Auth;

$currentPage = 'settings';
$currentTab = $_GET['tab'] ?? 'company'; // Support deep linking via ?tab=x

// قائمة التبويبات مع الصلاحيات المطلوبة
$tabs = [
    'company' => ['icon' => 'fa-building', 'label' => 'بيانات الشركة', 'permission' => 'settings.company'],
    'social' => ['icon' => 'fa-share-alt', 'label' => 'التواصل الاجتماعي', 'permission' => 'settings.social'],
    'security' => ['icon' => 'fa-shield-alt', 'label' => 'الأمان والحماية', 'permission' => 'settings.security'],
    'telegram' => ['icon' => 'fa-paper-plane', 'label' => 'إعدادات Telegram', 'permission' => 'settings.telegram'],
    'ai' => ['icon' => 'fa-robot', 'label' => 'الذكاء الاصطناعي', 'permission' => 'settings.ai'],
    'images' => ['icon' => 'fa-images', 'label' => 'الصور والعلامة المائية', 'permission' => 'settings.images'],
    'general' => ['icon' => 'fa-cogs', 'label' => 'إعدادات عامة', 'permission' => 'settings.general'],
    'menu' => ['icon' => 'fa-link', 'label' => 'روابط الفوتر', 'permission' => 'settings.menu'],
];

// تصفية التبويبات بناءً على الصلاحيات
$allowedTabs = array_filter($tabs, function($tab) {
    return Auth::can($tab['permission']);
});

// تحديد أول تبويب متاح
$firstAllowedTab = !empty($allowedTabs) ? array_key_first($allowedTabs) : null;

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-sliders-h" style="color: #60a5fa;"></i>
        إعدادات النظام
    </h1>
</div>

<?php if (empty($allowedTabs)): ?>
<div class="glass-card" style="text-align: center; padding: 3rem;">
    <i class="fas fa-lock" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i>
    <h3 style="color: #e2e8f0;">لا توجد صلاحيات</h3>
    <p style="color: #94a3b8;">ليس لديك صلاحية الوصول إلى أي من إعدادات النظام.</p>
</div>
<?php else: ?>

<div class="settings-layout">
    <!-- Sidebar Navigation -->
    <aside class="settings-sidebar glass-card">
        <nav class="settings-nav">
            <?php $isFirst = true; foreach ($allowedTabs as $id => $tab): ?>
            <button class="nav-item <?= $isFirst ? 'active' : '' ?>" onclick="switchTab('<?= htmlspecialchars($id) ?>', this)" id="nav-<?= htmlspecialchars($id) ?>">
                <div class="nav-icon"><i class="fas <?= htmlspecialchars($tab['icon']) ?>"></i></div>
                <span><?= htmlspecialchars($tab['label']) ?></span>
                <i class="fas fa-chevron-left arrow"></i>
            </button>
            <?php $isFirst = false; endforeach; ?>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="settings-content">
        <?php if (!empty($_SESSION['flash']['success'])): ?>
        <div class="alert-glass success">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['flash']['success'] ?>
            <?php unset($_SESSION['flash']['success']); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div class="alert-glass error">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash']['error'] ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
        <?php endif; ?>

        <!-- Tab Containers - Only show allowed tabs -->
        <?php $isFirst = true; ?>
        <?php if (Auth::can('settings.company')): ?>
        <div id="tab-company" class="tab-pane <?= $isFirst && $firstAllowedTab === 'company' ? 'active' : '' ?>"><?php include 'tabs/company.php'; ?></div>
        <?php if ($firstAllowedTab === 'company') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.social')): ?>
        <div id="tab-social" class="tab-pane <?= $isFirst && $firstAllowedTab === 'social' ? 'active' : '' ?>"><?php include 'tabs/social.php'; ?></div>
        <?php if ($firstAllowedTab === 'social') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.security')): ?>
        <div id="tab-security" class="tab-pane <?= $isFirst && $firstAllowedTab === 'security' ? 'active' : '' ?>"><?php include 'tabs/security.php'; ?></div>
        <?php if ($firstAllowedTab === 'security') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.telegram')): ?>
        <div id="tab-telegram" class="tab-pane <?= $isFirst && $firstAllowedTab === 'telegram' ? 'active' : '' ?>"><?php include 'tabs/telegram.php'; ?></div>
        <?php if ($firstAllowedTab === 'telegram') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.ai')): ?>
        <div id="tab-ai" class="tab-pane <?= $isFirst && $firstAllowedTab === 'ai' ? 'active' : '' ?>"><?php include 'tabs/ai.php'; ?></div>
        <?php if ($firstAllowedTab === 'ai') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.images')): ?>
        <div id="tab-images" class="tab-pane <?= $isFirst && $firstAllowedTab === 'images' ? 'active' : '' ?>"><?php include 'tabs/images.php'; ?></div>
        <?php if ($firstAllowedTab === 'images') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.general')): ?>
        <div id="tab-general" class="tab-pane <?= $isFirst && $firstAllowedTab === 'general' ? 'active' : '' ?>"><?php include 'tabs/general.php'; ?></div>
        <?php if ($firstAllowedTab === 'general') $isFirst = false; endif; ?>
        
        <?php if (Auth::can('settings.menu')): ?>
        <div id="tab-menu" class="tab-pane <?= $isFirst && $firstAllowedTab === 'menu' ? 'active' : '' ?>"><?php include 'tabs/footer_links.php'; ?></div>
        <?php endif; ?>
    </main>
</div>
<?php endif; ?>

<style>
/* Layout Architecture */
.settings-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    align-items: start;
}

@media (max-width: 992px) {
    .settings-layout {
        grid-template-columns: 1fr;
    }
    .settings-sidebar {
        position: sticky;
        top: 0;
        z-index: 50;
    }
    .settings-nav {
        display: flex;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .nav-item {
        flex: 0 0 auto;
        border-radius: 8px !important;
        margin-right: 10px;
    }
    .nav-item .arrow { display: none; }
}

/* Sidebar Styles */
.settings-sidebar {
    padding: 1rem;
    background: rgba(30, 41, 59, 0.4);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.05);
}

.nav-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem;
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
    text-align: right;
    font-family: inherit;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #e2e8f0;
}

.nav-item.active {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), transparent);
    color: #60a5fa;
    font-weight: 600;
    border-right: 3px solid #60a5fa;
}

.nav-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-item.active .nav-icon {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
}

.arrow {
    margin-right: auto;
    font-size: 0.8rem;
    opacity: 0;
    transition: all 0.3s ease;
}

.nav-item.active .arrow {
    opacity: 1;
    transform: translateX(-5px);
}

/* Tab Content */
.tab-pane {
    display: none;
    animation: fadeIn 0.4s ease;
}
.tab-pane.active {
    display: block;
}

/* Alerts */
.alert-glass {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}
.alert-glass.success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #34d399;
}
.alert-glass.error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Tab Switching Logic
function switchTab(tabId, btn) {
    // Hide all tabs
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
    // Deselect all nav items
    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
    
    // Show selected
    document.getElementById('tab-' + tabId).classList.add('active');
    
    // If button passed (click event), activate it. If not (initial load), find it.
    if(btn) {
        btn.classList.add('active');
    } else {
        const navBtn = document.getElementById('nav-' + tabId);
        if(navBtn) navBtn.classList.add('active');
    }
    
    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabId);
    window.history.pushState({}, '', url);
}

// Handle browser back/forward and initial load
window.addEventListener('popstate', () => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'company';
    switchTab(tab);
});

// Initial check
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if(params.has('tab')) {
        switchTab(params.get('tab'));
    }
});
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

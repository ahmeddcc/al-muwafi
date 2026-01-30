<?php
/**
 * القالب الأساسي للوحة التحكم - تصميم داكن فاخر
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Auth;
use App\Services\Settings;
use App\Services\Security;

$user = Auth::user();
$companyInfo = Settings::getCompanyInfo();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'لوحة التحكم') ?> - <?= htmlspecialchars($companyInfo['name'] ?? 'المُوَفِّي') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <!-- Font Awesome Pro Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <?php if (!empty($companyInfo['favicon'])): ?>
    <link rel="icon" href="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['favicon'] ?>">
    <?php endif; ?>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/admin-dashboard.css?v=<?= time() ?>">
    
    <!-- Critical CSS to prevent image flash (FOUC) -->
    <style>
        /* Hide all images initially to prevent flash */
        .image-upload-box img, .preview-img { visibility: hidden; }
        .images-loaded .image-upload-box img, .images-loaded .preview-img { visibility: visible; }
        
        /* Prevent images from appearing full-screen before CSS loads */
        img { max-width: 100%; height: auto; }
        .preview-img { width: 100%; height: 100%; object-fit: contain; }
        .image-upload-box { position: relative; overflow: hidden; }
        .image-upload-box img { max-width: 100%; max-height: 100%; }
    </style>
    <script>
        // Show images after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('images-loaded');
        });
    </script>
</head>
<body>
    <!-- الشريط العلوي (محسن) -->
    <nav class="top-navbar">
        <div class="navbar-container">
            <!-- 1. الشعار (الهوية) -->
            <a href="<?= BASE_URL ?>/admin" class="navbar-brand">
                <?php if (!empty($companyInfo['logo'])): ?>
                    <img src="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['logo'] ?>" alt="Logo" style="height: 40px; width: auto;">
                <?php else: ?>
                    <img src="<?= BASE_URL ?>/public/assets/images/logo.png" alt="Logo" style="height: 40px; width: auto;">
                <?php endif; ?>
                <div class="navbar-brand-text" style="margin-right: 10px;">
                    <span class="brand-name" style="font-family: 'Cairo', sans-serif; font-size: 1.3rem; font-weight: 800; background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">المُوَافِي لمهمات المكاتب</span>
                </div>
            </a>

            <!-- 2. شريط البحث (تم نقله للهيدر) -->
            <!-- مساحة فارغة للحفاظ على التنسيق إذا لزم الأمر -->
            <div></div>

            <!-- 3. عناصر التنقل (Tabs) -->
            <div class="navbar-nav" id="navbarNav">
                <a href="<?= BASE_URL ?>/admin" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/home.png" alt="الرئيسية" class="nav-icon-3d">
                    <span class="nav-text">الرئيسية</span>
                </a>
                
                <?php if (Auth::can('tickets.view')): ?>
                <a href="<?= BASE_URL ?>/admin/tickets" class="nav-item <?= ($currentPage ?? '') === 'tickets' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/two-tickets.png" alt="التذاكر" class="nav-icon-3d">
                    <span class="nav-text">التذاكر</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('categories.view')): ?>
                <a href="<?= BASE_URL ?>/admin/categories" class="nav-item <?= ($currentPage ?? '') === 'categories' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/sorting-answers.png" alt="الأقسام" class="nav-icon-3d">
                    <span class="nav-text">الأقسام</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('products.view')): ?>
                <a href="<?= BASE_URL ?>/admin/products" class="nav-item <?= ($currentPage ?? '') === 'products' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/box.png" alt="المنتجات" class="nav-icon-3d">
                    <span class="nav-text">المنتجات</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('spare_parts.view')): ?>
                <a href="<?= BASE_URL ?>/admin/spare-parts" class="nav-item <?= ($currentPage ?? '') === 'spare-parts' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/maintenance.png" alt="قطع الغيار" class="nav-icon-3d">
                    <span class="nav-text">قطع الغيار</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('services.view')): ?>
                <a href="<?= BASE_URL ?>/admin/services" class="nav-item <?= ($currentPage ?? '') === 'services' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/service.png" alt="الخدمات" class="nav-icon-3d">
                    <span class="nav-text">الخدمات</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('pages.view')): ?>
                <a href="<?= BASE_URL ?>/admin/pages" class="nav-item <?= ($currentPage ?? '') === 'pages' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/web-design.png" alt="الصفحات" class="nav-icon-3d">
                    <span class="nav-text">الصفحات</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('messages.view')): ?>
                <a href="<?= BASE_URL ?>/admin/messages" class="nav-item <?= ($currentPage ?? '') === 'messages' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/chat.png" alt="الرسائل" class="nav-icon-3d">
                    <span class="nav-text">الرسائل</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('users.view')): ?>
                <a href="<?= BASE_URL ?>/admin/users" class="nav-item <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/group.png" alt="المستخدمين" class="nav-icon-3d">
                    <span class="nav-text">المستخدمين</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('roles.view')): ?>
                <a href="<?= BASE_URL ?>/admin/roles" class="nav-item <?= ($currentPage ?? '') === 'roles' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/security-checked.png" alt="الأدوار" class="nav-icon-3d">
                    <span class="nav-text">الأدوار</span>
                </a>
                <?php endif; ?>
                
                <?php if (Auth::can('settings.view')): ?>
                <a href="<?= BASE_URL ?>/admin/settings" class="nav-item <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                    <img src="https://img.icons8.com/fluency/48/settings.png" alt="الإعدادات" class="nav-icon-3d">
                    <span class="nav-text">الإعدادات</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- 4. الجزء الأيسر (الجرس + المستخدم) -->
            <div class="navbar-end">
                <!-- زر الإشعارات -->
                <!-- زر الإشعارات -->
                <div class="nav-item-wrapper">
                    <a href="javascript:void(0);" class="btn-icon-glass <?= ($notifications['total'] ?? 0) > 0 ? 'has-notifications' : '' ?>" 
                       onclick="toggleNotifications(event)" id="notificationBtn">
                        <i class="fa-regular fa-bell"></i>
                    </a>
                    
                    <!-- قائمة الإشعارات -->
                    <div class="notification-dropdown" id="notificationMenu">
                        <div class="dropdown-header">
                            <span class="title">الإشعارات</span>
                            <?php if (($notifications['total'] ?? 0) > 0): ?>
                            <span class="badge badge-primary"><?= $notifications['total'] ?> جديد</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="dropdown-body">
                            <?php if (($notifications['tickets'] ?? 0) > 0): ?>
                            <a href="<?= BASE_URL ?>/admin/tickets?status=new" class="notification-item">
                                <div class="icon-wrapper blue">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <div class="content">
                                    <p class="message">لديك <?= $notifications['tickets'] ?> تذاكر جديدة</p>
                                    <span class="time">تذاكر الدعم الفني</span>
                                </div>
                            </a>
                            <?php endif; ?>

                            <?php if (($notifications['messages'] ?? 0) > 0): ?>
                            <a href="<?= BASE_URL ?>/admin/messages?status=unread" class="notification-item">
                                <div class="icon-wrapper purple">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="content">
                                    <p class="message">لديك <?= $notifications['messages'] ?> رسائل جديدة</p>
                                    <span class="time">رسائل التواصل</span>
                                </div>
                            </a>
                            <?php endif; ?>

                            <?php if (empty($notifications['total'])): ?>
                            <div class="empty-state">
                                <i class="fa-regular fa-bell-slash"></i>
                                <p>لا توجد إشعارات جديدة</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- زر السجلات -->
                <a href="<?= BASE_URL ?>/admin/logs" class="btn-icon-glass" title="سجلات النظام" style="margin-left: 10px;">
                    <i class="fa-solid fa-file-alt"></i>
                </a>

                <!-- زر الموقع -->
                <a href="<?= BASE_URL ?>" target="_blank" class="btn-icon-glass" title="زيارة الموقع">
                    <i class="fa-solid fa-earth-americas"></i>
                </a>
                
                <!-- زر الملف الشخصي (يفتح نافذة منبثقة) -->
                <div class="user-profile-btn" onclick="openProfileModal()">
                    <div class="user-avatar"><?= mb_substr($user['full_name'] ?? 'م', 0, 1) ?></div>
                    <div class="user-info-text">
                        <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? 'Admin') ?></span>
                        <span class="user-role"><?= htmlspecialchars($user['role_name_ar'] ?? 'مدير') ?></span>
                    </div>
                </div>




                <!-- زر الخروج المستقل -->
                <a href="<?= BASE_URL ?>/admin/auth/logout" class="btn-icon-glass logout-btn" title="تسجيل الخروج">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
            
            <!-- زر موبايل -->
            <button class="mobile-menu-btn" onclick="toggleNav()">☰</button>
        </div>
    </nav>

    <style>
    .logout-btn {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.2) !important;
        margin-right: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .logout-btn i {
        transition: transform 0.3s ease;
    }
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.2) !important;
        margin-right: 15px;
    }
    .logout-btn:hover {
        background: rgba(239, 68, 68, 0.2) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .logout-btn:hover i {
        transform: rotate(90deg);
        transition: transform 0.3s ease;
    }
    /* تنسيق شريط البحث في الهيدر */
    .header-search-container {
        position: relative;
        width: 300px;
    }
    .header-search-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.6rem 2.5rem 0.6rem 1rem;
        border-radius: 8px;
        color: #fff;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    .header-search-input:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(94, 234, 212, 0.5);
        outline: none;
        box-shadow: 0 0 10px rgba(94, 234, 212, 0.2);
    }
    .header-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    </style>
    
    <!-- المحتوى الرئيسي -->
    <main class="main-content">
        <!-- رأس الصفحة -->
        <!-- رأس الصفحة -->
        <?php $currentSection = $activeSection ?? $currentPage ?? ''; ?>
        <?php if (!in_array($currentSection, ['tickets', 'categories', 'products', 'spare-parts', 'messages', 'roles', 'users', 'pages', 'services', 'settings', 'profile', 'auth', 'logs'])): ?>
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($title ?? 'لوحة التحكم') ?></h1>
            
            <!-- شريط البحث في الهيدر -->
            <div class="header-search-container">
                <i class="fa-solid fa-search header-search-icon"></i>
                <input type="text" class="header-search-input" placeholder="بحث سريع...">
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['flash']['success'] ?>
            <?php unset($_SESSION['flash']['success']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['flash']['error'] ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </main>
    
    <script>
        function toggleNav() {
            document.getElementById('navbarNav').classList.toggle('open');
        }
        
        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('show');
            document.getElementById('notificationMenu').classList.remove('show');
        }

        function toggleNotifications(e) {
            e.preventDefault();
            document.getElementById('notificationMenu').classList.toggle('show');
            document.getElementById('userMenu').classList.remove('show');
        }
        
        // Close dropdown when clicking outside
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const userDropdown = document.getElementById('userDropdown');
            const userMenu = document.getElementById('userMenu');
            
            // Close User Menu
            if (userDropdown && !userDropdown.contains(e.target) && userMenu.classList.contains('show')) {
                userMenu.classList.remove('show');
            }

            // Close Notification Menu
            const notifBtn = document.getElementById('notificationBtn');
            const notifMenu = document.getElementById('notificationMenu');
            if (notifBtn && !notifBtn.contains(e.target) && !notifMenu.contains(e.target) && notifMenu.classList.contains('show')) {
                notifMenu.classList.remove('show');
            }
        });
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/image-cropper.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Profile Modal (Advanced) -->
    <div id="profileModal" class="modal-overlay" style="z-index: 100000; backdrop-filter: blur(8px);">
        <div class="modal-content glass-profile-card">
            <button class="close-modal-btn" onclick="closeProfileModal()">&times;</button>
            
            <div class="profile-header-bg"></div>
            
            <div class="profile-avatar-container">
                <div class="large-avatar">
                   <?= mb_substr($user['full_name'] ?? 'م', 0, 1) ?>
                </div>
                <div class="status-indicator online"></div>
            </div>
            
            <div class="profile-info-center">
                <h2 class="profile-name"><?= htmlspecialchars($user['full_name'] ?? 'Admin') ?></h2>
                <div class="profile-badges">
                    <span class="role-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        <?= htmlspecialchars($user['role_name_ar'] ?? 'مدير') ?>
                    </span>
                    <span class="username-badge">@<?= htmlspecialchars($user['username'] ?? 'admin') ?></span>
                </div>
            </div>

            <div class="profile-details-grid">
                <div class="detail-item">
                    <span class="label">البريد الإلكتروني</span>
                    <span class="value"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">الهاتف</span>
                    <span class="value"><?= htmlspecialchars($user['phone'] ?? '-') ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">آخر دخول</span>
                    <span class="value" dir="ltr"><?= $user['last_login'] ?? 'الآن' ?></span>
                </div>
            </div>

            <div class="profile-actions-row" style="grid-template-columns: 1fr;">
                <a href="<?= BASE_URL ?>/admin/profile" class="profile-action-btn primary">
                    <i class="fa-solid fa-user-pen"></i>
                    تعديل الملف الشخصي
                </a>
            </div>
        </div>
    </div>

    <!-- System Alert Modal -->
    <div id="systemAlertModal" class="modal-overlay" style="z-index: 100000;">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="modal-header" style="justify-content: center; border: none; padding-bottom: 0;">
                <div id="systemAlertIcon" style="font-size: 3rem; margin-bottom: 1rem;"></div>
            </div>
            <div class="modal-body" style="padding: 0 1rem 1.5rem;">
                <h3 id="systemAlertTitle" style="margin: 0 0 0.5rem; color: #f1f5f9;"></h3>
                <p id="systemAlertMessage" style="color: #94a3b8; margin: 0;"></p>
            </div>
            <div class="modal-footer" style="justify-content: center; border: none; padding-top: 0;">
                <button onclick="closeSystemAlert()" class="btn-glass-primary">حسناً</button>
            </div>
        </div>
    </div>

    <!-- System Confirm Modal -->
    <div id="systemConfirmModal" class="modal-overlay" style="z-index: 100000;">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
             <div class="modal-header" style="justify-content: center; border: none; padding-bottom: 0;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: #f59e0b;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="modal-body" style="padding: 0 1rem 1.5rem;">
                <h3 id="systemConfirmTitle" style="margin: 0 0 0.5rem; color: #f1f5f9;">تأكيد الإجراء</h3>
                <p id="systemConfirmMessage" style="color: #94a3b8; margin: 0;"></p>
            </div>
            <div class="modal-footer" style="justify-content: center; border: none; padding-top: 0; gap: 1rem;">
                <button id="confirmYesBtn" class="btn-glass-danger">نعم، متأكد</button>
                <button onclick="closeSystemConfirm()" class="btn-glass-primary" style="background: transparent; border: 1px solid var(--glass-border);">إلغاء</button>
            </div>
        </div>
    </div>

    <script>
        // System Alert Functions
        function showSystemAlert(titleText, message, type = 'success') {
            const modal = document.getElementById('systemAlertModal');
            const icon = document.getElementById('systemAlertIcon');
            const title = document.getElementById('systemAlertTitle');
            const msg = document.getElementById('systemAlertMessage');
            
            if (type === 'success') {
                icon.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i>';
            } else {
                icon.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i>';
            }
            
            title.innerText = titleText;
            msg.innerText = message;
            
            modal.style.display = 'flex';
            modal.offsetHeight; // Trigger reflow
            modal.classList.add('show');
        }

        function closeSystemAlert() {
            const modal = document.getElementById('systemAlertModal');
            modal.classList.remove('show');
            setTimeout(() => { modal.style.display = 'none'; }, 300);
        }

        // System Confirm Functions
        let confirmCallback = null;

        function showSystemConfirm(title, message, callback) {
            const modal = document.getElementById('systemConfirmModal');
            const titleElem = document.getElementById('systemConfirmTitle');
            const msg = document.getElementById('systemConfirmMessage');
            const yesBtn = document.getElementById('confirmYesBtn');
            
            titleElem.innerText = title;
            msg.innerText = message;
            confirmCallback = callback;
            
            yesBtn.onclick = function() {
                if (confirmCallback) confirmCallback();
                closeSystemConfirm();
            };
            
            modal.style.display = 'flex';
            modal.offsetHeight; // Trigger reflow
            modal.classList.add('show');
        }

        function closeSystemConfirm() {
            const modal = document.getElementById('systemConfirmModal');
            modal.classList.remove('show');
            setTimeout(() => { modal.style.display = 'none'; }, 300);
            confirmCallback = null;
        }

        // Profile Modal Functions
        function openProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.style.display = 'flex';
            modal.offsetHeight; // Trigger reflow
            modal.classList.add('show');
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.classList.remove('show');
            setTimeout(() => { modal.style.display = 'none'; }, 300);
        }

        // Close on outside click
        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) closeProfileModal();
        });
    </script>
    
    <style>
    /* إخفاء زر القائمة موبايل على الشاشات الكبيرة */
    @media (min-width: 1024px) {
        .mobile-menu-btn {
            display: none !important;
        }
    }
    .mobile-menu-btn {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0 10px;
    }
    </style>
</body>
</html>

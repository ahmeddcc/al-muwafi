<?php
/**
 * القالب الأساسي للموقع العام
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Security;
use App\Services\Settings;

$companyInfo = $settings ?? Settings::getCompanyInfo();
$socialLinks = $social ?? Settings::getSocialLinks();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? '') ?>">
    <title><?= htmlspecialchars($title ?? 'الرئيسية') ?> - <?= htmlspecialchars($companyInfo['name'] ?? 'المُوَفِّي') ?></title>
    
    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- الأنماط -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css?v=<?= time() ?>">
    
    <!-- الأيقونة -->
    <?php if (!empty($companyInfo['favicon'])): ?>
    <link rel="icon" href="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['favicon'] ?>">
    <?php endif; ?>
    
    <?= Security::getFrontendProtectionScript() ?>

    <!-- Critical Mobile Nav CSS -->
    <style>
        /* ============================================
           MOBILE NAVIGATION - Professional Responsive Design
           ============================================ */
        @media (max-width: 992px) {
            /* Main Nav Container */
            .nav {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: auto !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 12px 16px !important;
                border: none !important;
                border-radius: 0 !important;
                border-bottom: 2px solid rgba(14,165,233,0.5) !important;
                background: linear-gradient(180deg, rgba(2,6,23,0.98) 0%, rgba(2,6,23,0.95) 100%) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                box-shadow: 
                    0 4px 30px rgba(0,0,0,0.4),
                    0 2px 20px rgba(14,165,233,0.3),
                    inset 0 -1px 0 rgba(14,165,233,0.2) !important;
                z-index: 100000 !important;
                transform: none !important;
                
                /* Flex Layout - Critical */
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 12px !important;
            }

            /* Logo Container */
            .logo {
                display: flex !important;
                align-items: center !important;
                gap: 10px !important;
                flex: 1 !important;
                min-width: 0 !important; /* Allows text truncation */
                text-decoration: none !important;
            }
            
            .logo-img {
                width: 40px !important;
                height: 40px !important;
                flex-shrink: 0 !important;
                border-radius: 8px !important;
                object-fit: contain !important;
            }
            
            .brand-name {
                font-size: 0.75rem !important;
                font-weight: 700 !important;
                line-height: 1.3 !important;
                color: #fff !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 160px !important;
                background: none !important;
                -webkit-text-fill-color: inherit !important;
            }

            /* Hamburger Menu Button */
            .mobile-menu-btn {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 44px !important;
                height: 44px !important;
                min-width: 44px !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 1.4rem !important;
                color: #fff !important;
                background: rgba(14,165,233,0.15) !important;
                border: 1px solid rgba(14,165,233,0.3) !important;
                border-radius: 10px !important;
                cursor: pointer !important;
                flex-shrink: 0 !important;
                transition: all 0.2s ease !important;
            }
            
            .mobile-menu-btn:hover,
            .mobile-menu-btn:active {
                background: rgba(14,165,233,0.3) !important;
                border-color: rgba(14,165,233,0.5) !important;
                transform: scale(1.05) !important;
            }

            /* Nav Links - Hidden by default on mobile */
            .nav-links {
                display: none !important;
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                max-height: 70vh !important;
                overflow-y: auto !important;
                background: linear-gradient(180deg, rgba(15,23,42,0.98) 0%, rgba(2,6,23,0.99) 100%) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border-top: 1px solid rgba(14,165,233,0.2) !important;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
                padding: 0 !important;
                margin: 0 !important;
                z-index: 99999 !important;
            }
            
            /* Nav Links - Visible when menu is active */
            .nav.active .nav-links {
                display: flex !important;
                flex-direction: column !important;
                padding: 20px !important;
                gap: 0 !important;
                animation: slideDown 0.3s ease-out !important;
            }
            
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Individual Nav Links - Professional Card Style */
            .nav.active .nav-links .nav-link {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 16px 24px !important;
                margin-bottom: 8px !important;
                font-size: 1.05rem !important;
                font-weight: 600 !important;
                letter-spacing: 0.3px !important;
                text-align: center !important;
                color: #e2e8f0 !important;
                text-decoration: none !important;
                background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100%) !important;
                border: 1px solid rgba(255,255,255,0.08) !important;
                border-radius: 14px !important;
                transition: all 0.25s ease !important;
                position: relative !important;
                overflow: hidden !important;
            }
            
            /* Subtle Glow Effect on Links */
            .nav.active .nav-links .nav-link::before {
                content: '' !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                height: 1px !important;
                background: linear-gradient(90deg, transparent, rgba(14,165,233,0.3), transparent) !important;
                opacity: 0 !important;
                transition: opacity 0.3s ease !important;
            }
            
            .nav.active .nav-links .nav-link:hover::before,
            .nav.active .nav-links .nav-link:active::before {
                opacity: 1 !important;
            }
            
            .nav.active .nav-links .nav-link:hover,
            .nav.active .nav-links .nav-link:active {
                background: linear-gradient(135deg, rgba(14,165,233,0.2) 0%, rgba(14,165,233,0.1) 100%) !important;
                border-color: rgba(14,165,233,0.4) !important;
                color: #0ea5e9 !important;
                transform: translateX(-4px) !important;
                box-shadow: 0 4px 20px rgba(14,165,233,0.15) !important;
            }
            
            /* Last link no margin */
            .nav.active .nav-links .nav-link:last-child {
                margin-bottom: 0 !important;
            }

            /* Hide CTA Button on Mobile */
            .nav-cta {
                display: none !important;
            }
        }
    </style>
    
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body>
    <!-- Advanced Immersive Background -->
    <div id="immersive-bg"></div>
    
    <!-- الشريط العلوي -->
    <!-- Navigation -->
    <nav class="nav" id="mainNav">
        <a href="<?= BASE_URL ?>" class="logo">
            <?php if (!empty($companyInfo['logo'])): ?>
            <img src="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['logo'] ?>" alt="الموافِ" class="logo-img">
            <?php endif; ?>
            <div class="brand-name">
                المُوَافِي لمهمات المكاتب
            </div>
        </a>
        
        <!-- Mobile Toggle -->
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-links">
            <?php
            // جلب روابط القائمة الديناميكية من قاعدة البيانات
            $menuPages = [];
            try {
                $db = \App\Services\Database::getInstance();
                $menuPages = $db->fetchAll("SELECT slug, title FROM pages WHERE is_active = 1 ORDER BY sort_order");
            } catch (\Exception $e) {
                // القيم الافتراضية
                $menuPages = [
                    ['slug' => 'home', 'title' => 'الرئيسية'],
                    ['slug' => 'products', 'title' => 'المنتجات'],
                    ['slug' => 'services', 'title' => 'الخدمات'],
                    ['slug' => 'spare-parts', 'title' => 'قطع الغيار'],
                    ['slug' => 'about', 'title' => 'من نحن'],
                    ['slug' => 'contact', 'title' => 'اتصل بنا'],
                ];
            }
            
            foreach ($menuPages as $menuPage):
                $pageSlug = $menuPage['slug'];
                $pageTitle = htmlspecialchars($menuPage['title']);
                $pageUrl = ($pageSlug === 'home') ? BASE_URL : BASE_URL . '/' . $pageSlug;
                $isActive = ($currentPage ?? '') === $pageSlug ? 'active' : '';
                
                // تخطي الصيانة والصفحة الرئيسية من القائمة العادية
                if ($pageSlug === 'maintenance' || $pageSlug === 'home') continue;
            ?>
            <a href="<?= $pageUrl ?>" class="nav-link <?= $isActive ?>"><?= $pageTitle ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (($currentPage ?? '') !== 'maintenance'): ?>
        <a href="<?= BASE_URL ?>/maintenance" class="nav-cta">تقديم طلب صيانة</a>
        <?php endif; ?>
    </nav>
                

    
    <!-- المحتوى -->
    <main>
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </main>
    
    <!-- التذييل -->
    <?php if (!isset($hideFooter) || !$hideFooter): ?>
    <footer class="footer">
        <div class="container">
            <!-- Footer Grid Removed -->
            
            <div class="footer-bottom" style="flex-direction: column; gap: 5px;">
                <p>© <?= date('Y') ?> <?= htmlspecialchars($companyInfo['name'] ?? 'المُوَفِّي') ?>. جميع الحقوق محفوظة.</p>
                <div style="font-size: 0.85rem; opacity: 0.7;">
                    تم بناء وتطوير هذا النظام بواسطة <strong style="color:var(--primary)">نور للحلول التكنولوجية</strong> - <span style="color:#D4AF37; font-weight:bold;">Ahmed Hamed - 01014093162</span>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>
    
    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
        }

        // === Advanced Mouse Parallax Effect ===
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 30; // Movement range
            const y = (e.clientY / window.innerHeight - 0.5) * 30;

            const bg = document.getElementById('immersive-bg');
            if (bg) {
                // Move background opposite to mouse used for depth
                bg.style.transform = `translate(${-x}px, ${-y}px) scale(1.1)`; 
            }
            
            // Subtle tilt for cards if any exist
            const cards = document.querySelectorAll('.card, .spare-part-card');
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                     // Only animate visible cards subtly
                     const cardX = (e.clientX - rect.left - rect.width/2) / 20;
                     const cardY = (e.clientY - rect.top - rect.height/2) / 20;
                     // card.style.transform = `perspective(1000px) rotateX(${-cardY}deg) rotateY(${cardX}deg)`;
                }
            });
        });
    </script>
    
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>

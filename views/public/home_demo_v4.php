<?php
/**
 * الصفحة الرئيسية - التصميم المعتمد (V4 Final - Luxury)
 * نظام المُوَفِّي - وكيل ريكو المعتمد
 * Robust Version: Works offline, Handles missing DB/Images
 */

use App\Services\Database;
use App\Services\Settings;

// --- 1. Robust Data Fetching ---
$companyName = 'المُوَفِّي';
$companyLogo = '';
$services = [];
$stats = [
    'products' => 50,
    'tickets_closed' => 1200,
    'clients' => 300
];

try {
    $db = Database::getInstance();

    // Company Info
    if (class_exists('App\Services\Settings')) {
        $companyInfo = Settings::getCompanyInfo();
        $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
        $companyLogo = $companyInfo['logo'] ?? '';
    }

    // Services
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");
    
    // Stats (Safely)
    $stats['products'] = $db->count('products', 'is_active = 1') ?: 50;
    $stats['tickets_closed'] = $db->count('maintenance_tickets', "status = 'closed'") ?: 1200;
    $stats['clients'] = $db->count('users', "role_id != 1") ?: 300;

} catch (\Throwable $e) {
    // Silent fail - use defaults
    // ErrorLogger::log(...) could go here
}

// Fallback Services if empty
if (empty($services)) {
    $services = [
        ['name' => 'عقود الصيانة', 'short_description' => 'صيانة دورية ووقائية لجميع أنواع ماكينات ريكو.', 'icon' => 'tools'],
        ['name' => 'قطع الغيار', 'short_description' => 'نوفر جميع قطع الغيار والأحبار الأصلية 100%.', 'icon' => 'cogs'],
        ['name' => 'الدعم التقني', 'short_description' => 'فريق متخصص جاهز للرد على استفساراتكم 24/7.', 'icon' => 'headset'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | التميز في حلول ريكو</title>
    
    <!-- Fonts (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Noto+Kufi+Arabic:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries (Optional - Page works without them) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg-rich: #080a0f;
            --bg-panel: #0f1218;
            --gold-primary: #bf953f;
            --gold-light: #fcf6ba;
            --gold-dark: #aa771c;
            --text-white: #f5f5f7;
            --text-grey: #a1a1aa;
        }

        body {
            background-color: var(--bg-rich);
            color: var(--text-white);
            font-family: 'Noto Kufi Arabic', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; }

        /* --- Header / Nav (Critical for Visibility) --- */
        .luxury-nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999; /* Highest Priority */
            background: rgba(8, 10, 15, 0.95); /* Solid background initially to ensure visibility */
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            display: flex; align-items: center; gap: 10px;
        }
        .brand i { color: var(--gold-primary); font-size: 1.5rem; }

        .nav-items { display: flex; gap: 2rem; }
        .nav-link { color: #ccc; font-size: 0.9rem; transition: 0.3s; font-weight: 500; }
        .nav-link:hover { color: var(--gold-primary); }

        .btn-gold-sm {
            padding: 0.6rem 1.5rem;
            border: 1px solid var(--gold-primary);
            color: var(--gold-primary);
            font-size: 0.85rem;
            border-radius: 4px;
            transition: 0.3s;
        }
        .btn-gold-sm:hover { background: var(--gold-primary); color: #000; }

        /* --- Hero Section --- */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding-top: 80px; /* Space for fixed nav */
        }

        /* 
           Background Pattern (CSS Only - No Image Dependency) 
           Ensures the page is NEVER just black.
        */
        .hero-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            background: 
                radial-gradient(circle at 15% 50%, rgba(191, 149, 63, 0.08), transparent 25%), 
                radial-gradient(circle at 85% 30%, rgba(191, 149, 63, 0.05), transparent 25%),
                linear-gradient(to bottom, #080a0f, #0f1218);
        }
        
        .hero-pattern {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.5;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            padding: 0 20px;
        }

        .subtitle {
            font-size: 0.9rem;
            letter-spacing: 3px;
            color: var(--gold-primary);
            margin-bottom: 1.5rem;
            display: inline-block;
            border: 1px solid rgba(191, 149, 63, 0.3);
            padding: 8px 16px;
        }

        .main-title {
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 2rem;
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-light), var(--gold-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            color: #cbd5e1;
            font-size: 1.2rem;
            line-height: 1.8;
            margin: 0 auto 3rem;
            max-width: 700px;
        }

        .btn-group {
            display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap;
        }

        .btn-gold {
            background: var(--gold-primary);
            color: #000;
            padding: 1rem 2.5rem;
            font-weight: 700;
            border-radius: 4px;
            transition: 0.3s;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-gold:hover { background: #fff; transform: translateY(-3px); }

        .btn-outline {
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 1rem 2.5rem;
            font-weight: 700;
            border-radius: 4px;
            transition: 0.3s;
        }
        .btn-outline:hover { border-color: #fff; background: rgba(255,255,255,0.05); }

        /* --- Stats Bar --- */
        .stats-bar {
            background: rgba(255,255,255,0.02);
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 4rem 10%;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
            position: relative;
            z-index: 2;
        }

        .stat-item { text-align: center; }
        .stat-val { display: block; font-size: 3rem; font-weight: 700; color: var(--gold-primary); font-family: 'Cinzel', serif; }
        .stat-txt { color: var(--text-grey); font-size: 0.9rem; margin-top: 5px; }

        /* --- Services Grid --- */
        .services-section {
            padding: 6rem 5%;
            position: relative;
            z-index: 2;
        }

        .sec-title {
            text-align: center; margin-bottom: 4rem;
        }
        .sec-title h2 { font-size: 3rem; margin-bottom: 1rem; }
        .sec-title p { color: var(--text-grey); font-size: 1.1rem; }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--bg-panel);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 2.5rem;
            transition: 0.3s;
            display: flex; flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            border-color: var(--gold-primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .card-icon {
            font-size: 2.5rem; margin-bottom: 1.5rem; color: var(--gold-primary);
        }
        .card-title { font-size: 1.5rem; margin-bottom: 1rem; font-weight: 700; color: #fff; }
        .card-desc { color: var(--text-grey); line-height: 1.6; }

        /* --- Split Section --- */
        .split-sec {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
            border-top: 1px solid rgba(255,255,255,0.05);
            position: relative; z-index: 2;
        }
        @media (max-width: 900px) { .split-sec { grid-template-columns: 1fr; } }

        .split-img {
            /* Fallback pattern if image missing */
            background: 
                linear-gradient(45deg, #111 25%, transparent 25%, transparent 75%, #111 75%, #111), 
                linear-gradient(45deg, #111 25%, transparent 25%, transparent 75%, #111 75%, #111);
            background-color: #222;
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            min-height: 400px;
            position: relative;
        }
        
        /* If online, overload with image */
        .split-img-real {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=2632&auto=format&fit=crop') center/cover;
            opacity: 0.6;
            mix-blend-mode: overlay;
        }

        .split-txt {
            padding: 5rem 10%;
            display: flex; flex-direction: column; justify-content: center;
            background: #0b0d12;
        }

        /* --- Footer --- */
        .footer {
            padding: 6rem 5% 3rem;
            text-align: center;
            background: #000;
            border-top: 1px solid var(--gold-dark);
            position: relative; z-index: 2;
        }

        /* Utilities */
        @media (max-width: 768px) {
            .nav-items { display: none; }
        }

    </style>
</head>
<body>

    <!-- NAV (Fixed & Solid) -->
    <nav class="luxury-nav">
        <a href="<?= BASE_URL ?>" class="brand">
            <?php if (!empty($companyLogo)): ?>
                <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($companyLogo) ?>" alt="M" style="height: 40px;">
            <?php else: ?>
                <i class="fas fa-crown"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($companyName) ?></span>
        </a>

        <div class="nav-items">
            <a href="<?= BASE_URL ?>/products" class="nav-link">ماكينات ريكو</a>
            <a href="<?= BASE_URL ?>/services" class="nav-link">خدمات الصيانة</a>
            <a href="<?= BASE_URL ?>/contact" class="nav-link">اتصل بنا</a>
        </div>

        <a href="<?= BASE_URL ?>/maintenance" class="btn-gold-sm">منطقة العملاء</a>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-pattern"></div>
        
        <div class="hero-content">
            <span class="subtitle">RICOH AUTHORIZED</span>
            <h1 class="main-title">
                نحول التحديات إلى <br>
                <span class="gold-gradient-text">حلول مستدامة</span>
            </h1>
            <p class="hero-desc">
                شريكك الاستراتيجي في حلول الطباعة الرقمية والأرشفة. 
                أداء موثوق، صيانة استباقية، وقطع غيار أصلية لضمان استمرارية أعمالك.
            </p>
            
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/maintenance" class="btn-gold">
                    <i class="fas fa-ticket-alt"></i> فتح تذكرة صيانة
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn-outline">
                    المنتجات الحديثة
                </a>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats-bar">
        <div class="stat-item">
            <span class="stat-val"><?= $stats['products'] ?>+</span>
            <span class="stat-txt">منتج متوفر</span>
        </div>
        <div class="stat-item">
            <span class="stat-val"><?= $stats['tickets_closed'] ?>+</span>
            <span class="stat-txt">مهمة ناجزة</span>
        </div>
        <div class="stat-item">
            <span class="stat-val"><?= $stats['clients'] ?>+</span>
            <span class="stat-txt">عميل سعيد</span>
        </div>
    </section>

    <!-- SERVICES -->
    <section class="services-section">
        <div class="sec-title">
            <h2>خدماتنا الأساسية</h2>
            <p>حلول مصممة خصيصاً لبيئات العمل المتطلبة</p>
        </div>

        <div class="grid-cards">
            <?php foreach ($services as $svc): ?>
            <div class="service-card">
                <div class="card-icon">
                    <!-- Icon logic placeholder - using generic cog for now -->
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="card-title"><?= htmlspecialchars($svc['name']) ?></h3>
                <p class="card-desc"><?= htmlspecialchars($svc['short_description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- VISION -->
    <section class="split-sec">
        <div class="split-img">
            <div class="split-img-real"></div>
        </div>
        <div class="txt-side">
            <h3 style="font-size: 2.5rem; margin-bottom: 2rem; color: #fff;">رؤية لا تقبل الحلول الوسط</h3>
            <p style="color: #aaa; font-size: 1.1rem; line-height: 1.8;">
                في "المُوَفِّي"، نؤمن بأن كل ورقة تُطبع تمثل جزءاً من نجاح عميلنا. 
                لذلك، لا نقدم مجرد ماكينات، بل نقدم راحة البال من خلال عقود صيانة تضمن "صفر توقف".
            </p>
            <div style="margin-top: 3rem;">
                <span style="color: var(--gold-primary); font-weight: 700; letter-spacing: 2px;">إدارة المُوَفِّي</span>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <h2 style="color: #fff; font-size: 2.5rem; margin-bottom: 2rem;">جاهز للبدء؟</h2>
        <a href="<?= BASE_URL ?>/maintenance" class="btn-gold" style="display: inline-block;">ابدأ الآن</a>
        <div style="margin-top: 4rem; color: #555; font-size: 0.9rem;">
            © <?= date('Y') ?> <?= htmlspecialchars($companyName) ?>. جميع الحقوق محفوظة.
        </div>
    </footer>

    <!-- OPTIONAL Animations (Progressive Enhancement) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof gsap !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);
                
                // Hero Entrance
                gsap.from('.hero-content > *', {
                    opacity: 0, y: 30, duration: 1, stagger: 0.2, ease: 'power3.out'
                });

                // Cards Reveal
                gsap.utils.toArray('.service-card').forEach((card) => {
                    gsap.from(card, {
                        opacity: 0, y: 50, duration: 0.8,
                        scrollTrigger: { trigger: card, start: "top 85%" }
                    });
                });
            }
        });
    </script>
</body>
</html>

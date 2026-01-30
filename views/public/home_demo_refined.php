<?php
/**
 * Demo: Refined Luxury
 * Perfect Balance: Simplicity + Luxury + Technology + Elegance
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
$services = [];
$stats = ['products' => 50, 'tickets' => 1200, 'uptime' => 99];

try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 LIMIT 3");
    $stats['products'] = $db->count('products', 'is_active = 1') ?: 50;
    $stats['tickets'] = $db->count('maintenance_tickets', "status = 'closed'") ?: 1200;
} catch (\Throwable $e) {}

if (empty($services)) {
    $services = [
        ['name' => 'الصيانة الشاملة', 'short_description' => 'عقود صيانة احترافية تضمن استمرارية أعمالك'],
        ['name' => 'قطع الغيار الأصلية', 'short_description' => 'ضمان الجودة اليابانية بقطع ريكو الأصلية'],
        ['name' => 'الدعم الفوري', 'short_description' => 'فريق تقني متخصص متاح على مدار الساعة'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | الشريك الرسمي لريكو</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg: #fafafa;
            --surface: #ffffff;
            --text-dark: #1a1a1a;
            --text-light: #666666;
            --accent: #1a1a1a;
            --gold: #b8860b;
            --border: rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            font-weight: 300;
            line-height: 1.7;
            overflow-x: hidden;
        }

        ::selection {
            background: var(--gold);
            color: #fff;
        }

        /* === SMOOTH SCROLLBAR === */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #ccc; }

        /* === NAVIGATION === */
        .nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 1.5rem 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(250, 250, 250, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-dark);
        }

        .logo-symbol {
            width: 40px; height: 40px;
            border: 2px solid var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.4s;
        }
        .logo:hover .logo-symbol {
            background: var(--text-dark);
            color: #fff;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 3rem;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            position: relative;
            transition: 0.3s;
        }
        .nav-link:hover { color: var(--text-dark); }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 1px;
            background: var(--gold);
            transition: width 0.4s;
        }
        .nav-link:hover::after { width: 100%; }

        .nav-cta {
            background: var(--text-dark);
            color: #fff;
            padding: 0.8rem 2rem;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: 0.3s;
        }
        .nav-cta:hover {
            background: var(--gold);
            transform: translateY(-2px);
        }

        /* === HERO === */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 150px 8% 100px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 650px;
            position: relative;
            z-index: 2;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gold);
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .hero-label::before {
            content: '';
            width: 30px; height: 1px;
            background: var(--gold);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 200;
            line-height: 1.2;
            margin-bottom: 2rem;
            letter-spacing: -1px;
        }

        .hero-title strong {
            font-weight: 600;
            position: relative;
            display: inline-block;
        }
        .hero-title strong::after {
            content: '';
            position: absolute;
            bottom: 5px; left: 0;
            width: 100%; height: 8px;
            background: rgba(184, 134, 11, 0.2);
            z-index: -1;
            transition: height 0.3s;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-light);
            line-height: 2;
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--text-dark);
            color: #fff;
            padding: 1.1rem 2.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.4s;
        }
        .btn-primary:hover {
            background: var(--gold);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(184, 134, 11, 0.2);
        }

        .btn-text {
            color: var(--text-dark);
            text-decoration: none;
            padding: 1.1rem 0;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .btn-text:hover { color: var(--gold); gap: 12px; }

        /* === HERO VISUAL: REFINED CIRCLE === */
        .hero-visual {
            position: absolute;
            right: 8%;
            top: 50%;
            transform: translateY(-50%);
            width: 500px;
            height: 500px;
        }

        .circle-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .circle-outer {
            position: absolute;
            width: 100%; height: 100%;
            border: 1px solid var(--border);
            border-radius: 50%;
            animation: spin 60s linear infinite;
        }

        .circle-inner {
            position: absolute;
            width: 70%; height: 70%;
            top: 15%; left: 15%;
            border: 1px solid var(--border);
            border-radius: 50%;
            animation: spin 40s linear infinite reverse;
        }

        .circle-accent {
            position: absolute;
            width: 12px; height: 12px;
            background: var(--gold);
            border-radius: 50%;
            top: 0; left: 50%;
            transform: translateX(-50%);
        }

        .circle-text {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        .circle-number {
            font-size: 5rem;
            font-weight: 100;
            color: var(--text-dark);
            line-height: 1;
        }
        .circle-label {
            font-size: 0.9rem;
            color: var(--text-light);
            letter-spacing: 2px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* === SECTION DIVIDER === */
        .divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        /* === STATS === */
        .stats-section {
            padding: 6rem 8%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4rem;
            background: var(--surface);
        }

        .stat-item {
            text-align: center;
            position: relative;
        }
        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -2rem; top: 20%;
            height: 60%; width: 1px;
            background: var(--border);
        }

        .stat-value {
            font-size: 3.5rem;
            font-weight: 100;
            color: var(--text-dark);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .stat-value span { color: var(--gold); }

        .stat-label {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* === SERVICES === */
        .services-section {
            padding: 8rem 8%;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 200;
        }
        .section-header h2 strong { font-weight: 600; }

        .section-header a {
            color: var(--gold);
            text-decoration: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .section-header a:hover { gap: 12px; }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .service-card {
            background: var(--surface);
            padding: 3rem 2.5rem;
            border: 1px solid var(--border);
            transition: 0.4s;
            position: relative;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 0; height: 3px;
            background: var(--gold);
            transition: width 0.4s;
        }

        .service-card:hover {
            border-color: rgba(0,0,0,0.1);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        .service-card:hover::before { width: 100%; }

        .service-num {
            font-size: 4rem;
            font-weight: 100;
            color: rgba(0,0,0,0.05);
            line-height: 1;
            margin-bottom: 1.5rem;
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .service-desc {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.8;
        }

        /* === CTA SECTION === */
        .cta-section {
            padding: 8rem 8%;
            background: var(--text-dark);
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 200;
            margin-bottom: 1.5rem;
        }
        .cta-section h2 strong { font-weight: 600; color: var(--gold); }

        .cta-section p {
            color: rgba(255,255,255,0.6);
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }

        .btn-cta {
            background: var(--gold);
            color: #000;
            padding: 1.2rem 3rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(184, 134, 11, 0.3);
        }

        /* === FOOTER === */
        .footer {
            padding: 4rem 8%;
            background: var(--bg);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-copy {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }
        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .footer-links a:hover { color: var(--gold); }

        /* === RESPONSIVE === */
        @media (max-width: 1200px) {
            .hero-visual { display: none; }
        }

        @media (max-width: 1024px) {
            .nav-links { display: none; }
            .services-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .stats-section { grid-template-columns: 1fr; gap: 2rem; }
            .stat-item::after { display: none; }
            .hero { padding: 120px 5% 60px; }
            .section-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .footer { flex-direction: column; gap: 1.5rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">
            <div class="logo-symbol">M</div>
            <span class="logo-text"><?= htmlspecialchars($companyName) ?></span>
        </a>
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/products" class="nav-link">المنتجات</a>
            <a href="<?= BASE_URL ?>/services" class="nav-link">الخدمات</a>
            <a href="<?= BASE_URL ?>/spare-parts" class="nav-link">قطع الغيار</a>
            <a href="<?= BASE_URL ?>/contact" class="nav-link">تواصل معنا</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="nav-cta">بوابة العملاء</a>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-label">الشريك الرسمي لريكو</span>
            <h1 class="hero-title">
                نقدم لك <strong>التميز</strong> <br>
                في كل تفصيلة.
            </h1>
            <p class="hero-desc">
                نجمع بين الحرفية اليابانية والخدمة المحلية المتفانية لنضمن لك تجربة لا مثيل لها في عالم حلول الطباعة والأرشفة.
            </p>
            <div class="hero-btns">
                <a href="<?= BASE_URL ?>/maintenance" class="btn-primary">
                    ابدأ الآن <i class="fas fa-arrow-left"></i>
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn-text">
                    اكتشف المنتجات <i class="fas fa-chevron-left"></i>
                </a>
            </div>
        </div>

        <!-- Refined Visual -->
        <div class="hero-visual">
            <div class="circle-wrapper">
                <div class="circle-outer">
                    <div class="circle-accent"></div>
                </div>
                <div class="circle-inner"></div>
                <div class="circle-text">
                    <div class="circle-number"><?= $stats['uptime'] ?></div>
                    <div class="circle-label">% UPTIME</div>
                </div>
            </div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- Stats -->
    <section class="stats-section">
        <div class="stat-item">
            <div class="stat-value" id="stat1"><?= $stats['products'] ?><span>+</span></div>
            <div class="stat-label">منتج ريكو أصلي</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" id="stat2"><?= $stats['tickets'] ?><span>+</span></div>
            <div class="stat-label">عملية صيانة ناجحة</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" id="stat3">24<span>/7</span></div>
            <div class="stat-label">دعم فني متواصل</div>
        </div>
    </section>

    <div class="divider"></div>

    <!-- Services -->
    <section class="services-section">
        <div class="section-header">
            <h2>خدمات <strong>استثنائية</strong></h2>
            <a href="<?= BASE_URL ?>/services">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="services-grid">
            <?php $i = 1; foreach ($services as $s): ?>
            <div class="service-card">
                <div class="service-num">0<?= $i++ ?></div>
                <h3 class="service-title"><?= htmlspecialchars($s['name']) ?></h3>
                <p class="service-desc"><?= htmlspecialchars($s['short_description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>جاهز <strong>للبدء</strong>؟</h2>
        <p>تواصل معنا اليوم ودعنا نساعدك في تحقيق أقصى استفادة من أجهزتك</p>
        <a href="<?= BASE_URL ?>/maintenance" class="btn-cta">
            <i class="fas fa-paper-plane"></i> طلب صيانة
        </a>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?>. جميع الحقوق محفوظة.</div>
        <div class="footer-links">
            <a href="<?= BASE_URL ?>/privacy">الخصوصية</a>
            <a href="<?= BASE_URL ?>/terms">الشروط</a>
            <a href="<?= BASE_URL ?>/contact">اتصل بنا</a>
        </div>
    </footer>

    <!-- Subtle Animations -->
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Hero Entrance (Subtle)
        gsap.from('.hero-label', { opacity: 0, y: 20, duration: 1, delay: 0.3 });
        gsap.from('.hero-title', { opacity: 0, y: 30, duration: 1, delay: 0.5 });
        gsap.from('.hero-desc', { opacity: 0, y: 20, duration: 1, delay: 0.7 });
        gsap.from('.hero-btns', { opacity: 0, y: 20, duration: 1, delay: 0.9 });
        gsap.from('.hero-visual', { opacity: 0, scale: 0.9, duration: 1.5, delay: 0.5 });

        // Stats Counter Animation
        gsap.from('.stat-item', {
            opacity: 0,
            y: 30,
            duration: 0.8,
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.stats-section',
                start: 'top 80%'
            }
        });

        // Service Cards
        gsap.from('.service-card', {
            opacity: 0,
            y: 40,
            duration: 0.8,
            stagger: 0.15,
            scrollTrigger: {
                trigger: '.services-grid',
                start: 'top 80%'
            }
        });

        // Parallax on Scroll (Subtle)
        gsap.to('.hero-visual', {
            y: 100,
            ease: 'none',
            scrollTrigger: {
                trigger: '.hero',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    </script>
</body>
</html>

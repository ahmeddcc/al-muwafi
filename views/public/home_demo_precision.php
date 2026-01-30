<?php
/**
 * Demo: Precision Architecture Design
 * Concept: Technology + Stability + Luxury
 * Inspired by: Engineering blueprints, Precision instruments, Architectural drawings
 * Unique to: Al-Muwafi
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
$companyLogo = '';
$services = [];
$stats = ['products' => 50, 'tickets' => 1200, 'years' => 15];

try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
    $companyLogo = $companyInfo['logo'] ?? '';
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 LIMIT 4");
    $stats['products'] = $db->count('products', 'is_active = 1') ?: 50;
    $stats['tickets'] = $db->count('maintenance_tickets', "status = 'closed'") ?: 1200;
} catch (\Throwable $e) {}

if (empty($services)) {
    $services = [
        ['name' => 'عقود الصيانة', 'short_description' => 'صيانة شاملة بأعلى معايير الدقة'],
        ['name' => 'قطع الغيار الأصلية', 'short_description' => 'ضمان الجودة اليابانية'],
        ['name' => 'الدعم التقني', 'short_description' => 'فريق هندسي متخصص'],
        ['name' => 'حلول الأرشفة', 'short_description' => 'رقمنة وتنظيم المستندات'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | هندسة الدقة</title>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-deep: #0a0c10;
            --bg-layer: #12151c;
            --gold: #c9a227;
            --gold-light: #e8d48a;
            --silver: #8892a0;
            --white: #f0f2f5;
            --grid-color: rgba(201, 162, 39, 0.03);
            --line-gold: rgba(201, 162, 39, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Almarai', sans-serif;
            background: var(--bg-deep);
            color: var(--white);
            overflow-x: hidden;
            position: relative;
        }

        /* === BLUEPRINT GRID BACKGROUND === */
        .blueprint-grid {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(var(--grid-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px),
                linear-gradient(var(--line-gold) 1px, transparent 1px),
                linear-gradient(90deg, var(--line-gold) 1px, transparent 1px);
            background-size: 20px 20px, 20px 20px, 100px 100px, 100px 100px;
            z-index: 0;
            pointer-events: none;
        }

        /* === GEOMETRIC CORNER ACCENTS === */
        .corner-accent {
            position: fixed;
            width: 150px; height: 150px;
            border: 1px solid var(--line-gold);
            z-index: 1;
            pointer-events: none;
        }
        .corner-accent.top-right {
            top: 30px; right: 30px;
            border-top: 2px solid var(--gold);
            border-right: 2px solid var(--gold);
            border-bottom: none; border-left: none;
        }
        .corner-accent.bottom-left {
            bottom: 30px; left: 30px;
            border-bottom: 2px solid var(--gold);
            border-left: 2px solid var(--gold);
            border-top: none; border-right: none;
        }

        /* === NAVIGATION === */
        .nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: linear-gradient(to bottom, var(--bg-deep), transparent);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--white);
        }

        .logo-mark {
            width: 50px; height: 50px;
            border: 2px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--gold);
            position: relative;
        }
        .logo-mark::before {
            content: '';
            position: absolute;
            width: 100%; height: 100%;
            border: 1px solid var(--line-gold);
            transform: rotate(45deg);
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            gap: 3rem;
        }

        .nav-link {
            color: var(--silver);
            text-decoration: none;
            font-size: 0.9rem;
            letter-spacing: 1px;
            position: relative;
            transition: 0.3s;
        }
        .nav-link:hover { color: var(--gold); }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px; left: 0;
            width: 0; height: 1px;
            background: var(--gold);
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        .nav-cta {
            border: 1px solid var(--gold);
            color: var(--gold);
            padding: 0.7rem 1.5rem;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        .nav-cta:hover { background: var(--gold); color: var(--bg-deep); }

        /* === HERO SECTION === */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 5% 80px;
            position: relative;
            z-index: 2;
        }

        .hero-content {
            max-width: 700px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--gold);
            font-size: 0.85rem;
            letter-spacing: 3px;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }
        .hero-badge::before, .hero-badge::after {
            content: '';
            width: 30px; height: 1px;
            background: var(--gold);
        }

        .hero-title {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 2rem;
            position: relative;
        }

        .hero-title .accent {
            color: var(--gold);
            position: relative;
            display: inline-block;
        }
        .hero-title .accent::after {
            content: '';
            position: absolute;
            bottom: 5px; left: 0;
            width: 100%; height: 3px;
            background: var(--gold);
            opacity: 0.3;
        }

        .hero-desc {
            color: var(--silver);
            font-size: 1.2rem;
            line-height: 2;
            margin-bottom: 3rem;
            max-width: 550px;
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--gold);
            color: var(--bg-deep);
            padding: 1rem 2.5rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-primary:hover { background: var(--gold-light); transform: translateY(-3px); }

        .btn-secondary {
            border: 1px solid var(--silver);
            color: var(--white);
            padding: 1rem 2.5rem;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-secondary:hover { border-color: var(--gold); color: var(--gold); }

        /* === GEOMETRIC HERO VISUAL === */
        .hero-visual {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            width: 400px;
            height: 400px;
            opacity: 0.8;
        }

        .precision-circle {
            position: absolute;
            border: 1px solid var(--line-gold);
            border-radius: 50%;
            animation: rotate 30s linear infinite;
        }
        .precision-circle:nth-child(1) { width: 100%; height: 100%; }
        .precision-circle:nth-child(2) { width: 80%; height: 80%; top: 10%; left: 10%; animation-direction: reverse; }
        .precision-circle:nth-child(3) { width: 60%; height: 60%; top: 20%; left: 20%; }

        .precision-lines {
            position: absolute;
            width: 100%; height: 100%;
            animation: rotate 60s linear infinite reverse;
        }
        .precision-lines::before, .precision-lines::after {
            content: '';
            position: absolute;
            background: var(--line-gold);
        }
        .precision-lines::before { width: 100%; height: 1px; top: 50%; }
        .precision-lines::after { height: 100%; width: 1px; left: 50%; }

        .precision-center {
            position: absolute;
            width: 20px; height: 20px;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: var(--gold);
            border-radius: 50%;
        }

        @keyframes rotate {
            from { transform: translateY(-50%) rotate(0deg); }
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* === STATS SECTION === */
        .stats-section {
            padding: 5rem 5%;
            border-top: 1px solid var(--line-gold);
            border-bottom: 1px solid var(--line-gold);
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
            position: relative;
            z-index: 2;
            background: rgba(10, 12, 16, 0.9);
        }

        .stat-item {
            text-align: center;
            position: relative;
            padding: 0 3rem;
        }
        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0; top: 0;
            height: 100%; width: 1px;
            background: var(--line-gold);
        }

        .stat-value {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--gold);
            display: block;
            line-height: 1;
        }
        .stat-label {
            color: var(--silver);
            font-size: 0.9rem;
            margin-top: 0.5rem;
            letter-spacing: 1px;
        }

        /* === SERVICES SECTION === */
        .services-section {
            padding: 8rem 5%;
            position: relative;
            z-index: 2;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-label {
            color: var(--gold);
            font-size: 0.85rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 1rem;
            display: block;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .service-card {
            background: var(--bg-layer);
            border: 1px solid rgba(201, 162, 39, 0.1);
            padding: 2.5rem;
            position: relative;
            transition: 0.4s;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px; height: 0;
            background: var(--gold);
            transition: height 0.4s;
        }

        .service-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
        }
        .service-card:hover::before { height: 100%; }

        .service-num {
            font-size: 3rem;
            font-weight: 800;
            color: rgba(201, 162, 39, 0.1);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--white);
        }

        .service-desc {
            color: var(--silver);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* === FOOTER === */
        .footer {
            padding: 5rem 5%;
            border-top: 1px solid var(--line-gold);
            position: relative;
            z-index: 2;
            background: var(--bg-deep);
            text-align: center;
        }

        .footer-cta {
            margin-bottom: 4rem;
        }
        .footer-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .footer-bottom {
            color: var(--silver);
            font-size: 0.85rem;
            border-top: 1px solid var(--line-gold);
            padding-top: 2rem;
        }

        /* === RESPONSIVE === */
        @media (max-width: 1024px) {
            .hero-visual { display: none; }
            .nav-links { display: none; }
        }

        @media (max-width: 768px) {
            .stat-item::after { display: none; }
            .hero-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

    <!-- Blueprint Grid Background -->
    <div class="blueprint-grid"></div>

    <!-- Corner Accents -->
    <div class="corner-accent top-right"></div>
    <div class="corner-accent bottom-left"></div>

    <!-- Navigation -->
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">
            <div class="logo-mark">M</div>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-badge">RICOH PRECISION PARTNER</span>
            <h1 class="hero-title">
                هندسة الدقة <br>
                في خدمة <span class="accent">أعمالك</span>
            </h1>
            <p class="hero-desc">
                نجمع بين الدقة اليابانية الأسطورية وخبرتنا المحلية العميقة لنقدم لك حلولاً مكتبية لا تقبل الفشل. كل تفصيلة محسوبة، كل خدمة مضمونة.
            </p>
            <div class="hero-btns">
                <a href="<?= BASE_URL ?>/maintenance" class="btn-primary">
                    <i class="fas fa-drafting-compass"></i> ابدأ مشروعك
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn-secondary">استكشف التقنيات</a>
            </div>
        </div>

        <!-- Geometric Visual -->
        <div class="hero-visual">
            <div class="precision-circle"></div>
            <div class="precision-circle"></div>
            <div class="precision-circle"></div>
            <div class="precision-lines"></div>
            <div class="precision-center"></div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="stat-item">
            <span class="stat-value"><?= $stats['products'] ?>+</span>
            <span class="stat-label">منتج متقن</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $stats['tickets'] ?>+</span>
            <span class="stat-label">مهمة ناجحة</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $stats['years'] ?>+</span>
            <span class="stat-label">سنة من الإتقان</span>
        </div>
    </section>

    <!-- Services -->
    <section class="services-section">
        <div class="section-header">
            <span class="section-label">ما نقدمه</span>
            <h2 class="section-title">حلول مهندسة بعناية</h2>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-cta">
            <h2>جاهز لتجربة الفرق؟</h2>
            <a href="<?= BASE_URL ?>/maintenance" class="btn-primary">
                <i class="fas fa-arrow-left"></i> ابدأ الآن
            </a>
        </div>
        <div class="footer-bottom">
            © <?= date('Y') ?> <?= htmlspecialchars($companyName) ?> — هندسة الدقة في خدمة الأعمال
        </div>
    </footer>

</body>
</html>

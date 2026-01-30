<?php
/**
 * Demo: ULTIMATE CINEMATIC
 * From Imagination: Dark gradients + Neon accents + Dramatic effects
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
$services = [];
$stats = ['products' => 50, 'tickets' => 1200, 'years' => 15];

try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 LIMIT 4");
    $stats['products'] = $db->count('products', 'is_active = 1') ?: 50;
    $stats['tickets'] = $db->count('maintenance_tickets', "status = 'closed'") ?: 1200;
} catch (\Throwable $e) {}

if (empty($services)) {
    $services = [
        ['name' => 'صيانة شاملة', 'short_description' => 'عقود صيانة متكاملة تضمن استمرارية عملك بلا توقف'],
        ['name' => 'قطع أصلية', 'short_description' => 'قطع غيار ريكو الأصلية بضمان كامل'],
        ['name' => 'دعم فوري', 'short_description' => 'فريق تقني متخصص على مدار الساعة'],
        ['name' => 'حلول ذكية', 'short_description' => 'أرشفة رقمية وربط الأنظمة'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | المستقبل يبدأ هنا</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            /* Harmonious Black-Gold-Silver Palette */
            --bg-deep: #080808;
            --bg-mid: #0d0d0d;
            --bg-light: #121212;
            --gold: #d4af37;
            --gold-bright: #ffd700;
            --gold-soft: #c9a227;
            --silver: #b8b8b8;
            --silver-light: #d4d4d4;
            --champagne: #f7e7ce;
            --white: #f5f5f5;
            --grey: #6b6b6b;
            /* Blended Gradients */
            --gradient-main: linear-gradient(135deg, var(--gold-soft), var(--gold), var(--champagne), var(--silver));
            --gradient-accent: linear-gradient(90deg, var(--gold), var(--silver), var(--gold));
            --gradient-dark: linear-gradient(180deg, rgba(212,175,55,0.1), rgba(184,184,184,0.05), transparent);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg-deep);
            color: var(--white);
            overflow-x: hidden;
            min-height: 100vh;
        }

        ::selection {
            background: var(--gold);
            color: #000;
        }

        /* === BLENDED GRADIENT BACKGROUND === */
        .gradient-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            background: 
                /* Gold glow top-right */
                radial-gradient(ellipse 80% 50% at 80% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 50%),
                /* Silver glow bottom-left */
                radial-gradient(ellipse 60% 40% at 20% 80%, rgba(184, 184, 184, 0.05) 0%, transparent 50%),
                /* Center champagne */
                radial-gradient(ellipse 100% 80% at 50% 50%, rgba(247, 231, 206, 0.03) 0%, transparent 40%),
                /* Base gradient */
                linear-gradient(135deg, var(--bg-deep) 0%, var(--bg-mid) 50%, var(--bg-light) 100%);
        }

        /* === MORPHING BLENDED BLOBS === */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            z-index: 0;
            animation: blobFloat 25s ease-in-out infinite;
        }
        .blob-1 {
            width: 700px; height: 700px;
            /* Gold to Champagne blend */
            background: radial-gradient(circle, var(--gold) 0%, var(--champagne) 50%, transparent 70%);
            top: -300px; right: -200px;
            opacity: 0.12;
        }
        .blob-2 {
            width: 600px; height: 600px;
            /* Silver to Gold subtle blend */
            background: radial-gradient(circle, var(--silver) 0%, var(--gold-soft) 60%, transparent 80%);
            bottom: -250px; left: -150px;
            opacity: 0.08;
            animation-delay: -10s;
        }
        .blob-3 {
            width: 500px; height: 500px;
            /* Champagne center */
            background: radial-gradient(circle, var(--champagne) 0%, var(--gold) 50%, transparent 70%);
            top: 40%; left: 30%;
            opacity: 0.06;
            animation-delay: -18s;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
            33% { transform: translate(30px, -40px) scale(1.03) rotate(3deg); }
            66% { transform: translate(-20px, 30px) scale(0.97) rotate(-2deg); }
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
            background: linear-gradient(to bottom, rgba(8, 8, 8, 0.95), rgba(8, 8, 8, 0.8));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--white);
        }

        .logo-icon {
            width: 50px; height: 50px;
            /* Blended gold-silver gradient */
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--gold) 40%, var(--champagne) 70%, var(--silver-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.5rem;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(212, 175, 55, 0.25), 0 0 50px rgba(184, 184, 184, 0.1);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            /* Blended text gradient */
            background: linear-gradient(90deg, var(--champagne) 0%, var(--gold) 30%, var(--silver-light) 70%, var(--white) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(50px, -30px) scale(1.05); }
            50% { transform: translate(-30px, 50px) scale(0.95); }
            75% { transform: translate(30px, 30px) scale(1.02); }
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
            background: rgba(15, 12, 41, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--white);
        }

        .logo-icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.4);
            animation: glow 3s ease-in-out infinite alternate;
        }

        @keyframes glow {
            0% { box-shadow: 0 0 20px rgba(212, 175, 55, 0.2); }
            100% { box-shadow: 0 0 40px rgba(212, 175, 55, 0.5); }
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--white), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 3rem;
        }

        .nav-link {
            color: var(--grey);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: 0.3s;
            position: relative;
        }
        .nav-link:hover { color: var(--champagne); }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px; left: 0;
            width: 0; height: 2px;
            background: linear-gradient(90deg, var(--gold-soft), var(--gold), var(--champagne));
            transition: width 0.4s;
        }
        .nav-link:hover::after { width: 100%; }

        .nav-cta {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--gold) 50%, var(--champagne) 100%);
            color: #000;
            text-decoration: none;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.25);
            transition: 0.3s;
        }
        .nav-cta:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.35);
        }

        /* === HERO === */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 150px 5% 100px;
            position: relative;
            z-index: 2;
        }

        .hero-content {
            max-width: 900px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 1.5rem;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(247, 231, 206, 0.05));
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 50px;
            font-size: 0.9rem;
            color: var(--champagne);
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }

        .hero-title {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
        }

        .hero-title .line {
            display: block;
            overflow: hidden;
        }

        .gradient-text {
            background: linear-gradient(90deg, var(--gold-soft) 0%, var(--gold) 25%, var(--champagne) 50%, var(--silver-light) 75%, var(--gold) 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientText 6s linear infinite;
        }

        @keyframes gradientText {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .hero-desc {
            font-size: 1.3rem;
            color: var(--grey);
            line-height: 2;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-gradient {
            padding: 1.2rem 3rem;
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--gold) 40%, var(--champagne) 80%, var(--silver-light) 100%);
            color: #000;
            font-size: 1.1rem;
            font-weight: 800;
            text-decoration: none;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 5px 25px rgba(212, 175, 55, 0.25), 0 0 60px rgba(247, 231, 206, 0.1);
            transition: 0.4s;
        }
        .btn-gradient:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.35), 0 0 80px rgba(247, 231, 206, 0.15);
        }

        .btn-glass {
            padding: 1.2rem 3rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(212, 175, 55, 0.03));
            border: 1px solid rgba(184, 184, 184, 0.15);
            color: var(--champagne);
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            transition: 0.4s;
        }
        .btn-glass:hover {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(247, 231, 206, 0.05));
            border-color: rgba(212, 175, 55, 0.3);
            transform: translateY(-3px);
        }

        /* === STATS SECTION === */
        .stats-section {
            padding: 6rem 5%;
            position: relative;
            z-index: 2;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.02), rgba(212, 175, 55, 0.02), rgba(184, 184, 184, 0.01));
            border: 1px solid rgba(212, 175, 55, 0.08);
            border-radius: 24px;
            padding: 3rem 2rem;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--gold-soft), var(--gold), var(--champagne), var(--silver-light));
            transform: scaleX(0);
            transition: transform 0.4s;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.2);
            box-shadow: 0 20px 50px rgba(212, 175, 55, 0.1), 0 0 80px rgba(247, 231, 206, 0.05);
        }
        .stat-card:hover::before { transform: scaleX(1); }

        .stat-value {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--gold) 40%, var(--champagne) 70%, var(--silver-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .stat-label {
            color: var(--grey);
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        /* === SERVICES === */
        .services-section {
            padding: 8rem 5%;
            position: relative;
            z-index: 2;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-tag {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(247, 231, 206, 0.04));
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 50px;
            color: var(--champagne);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 900;
        }
        .section-title span {
            background: linear-gradient(90deg, var(--gold-soft), var(--gold), var(--champagne));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .service-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.015), rgba(212, 175, 55, 0.01));
            border: 1px solid rgba(212, 175, 55, 0.05);
            border-radius: 24px;
            padding: 3rem 2rem;
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .service-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.04), rgba(247, 231, 206, 0.02), rgba(184, 184, 184, 0.01));
            opacity: 0;
            transition: opacity 0.4s;
        }

        .service-card:hover {
            transform: translateY(-8px);
            border-color: rgba(212, 175, 55, 0.15);
            box-shadow: 0 25px 50px rgba(212, 175, 55, 0.1), 0 0 60px rgba(247, 231, 206, 0.03);
        }
        .service-card:hover::after { opacity: 1; }

        .service-icon {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--gold) 40%, var(--champagne) 80%, var(--silver-light) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #000;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2);
        }

        .service-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            color: var(--champagne);
        }

        .service-desc {
            color: var(--grey);
            line-height: 1.8;
            position: relative;
            z-index: 1;
        }

        /* === CTA Section === */
        .cta-section {
            padding: 10rem 5%;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .cta-box {
            max-width: 800px;
            margin: 0 auto;
            padding: 5rem;
            background: linear-gradient(145deg, rgba(212, 175, 55, 0.05), rgba(247, 231, 206, 0.03), rgba(184, 184, 184, 0.02));
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-radius: 32px;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(212, 175, 55, 0.08), rgba(247, 231, 206, 0.04), transparent 40%);
            animation: rotateBorder 12s linear infinite;
        }

        @keyframes rotateBorder {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
            background: linear-gradient(90deg, var(--champagne), var(--gold), var(--silver-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-desc {
            color: var(--grey);
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        /* === FOOTER === */
        .footer {
            padding: 4rem 5%;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .footer-copy {
            color: var(--grey);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }
        .footer-links a {
            color: var(--grey);
            text-decoration: none;
            transition: 0.3s;
        }
        .footer-links a:hover { color: var(--champagne); }

        /* === RESPONSIVE === */
        @media (max-width: 1024px) {
            .nav-links { display: none; }
            .stats-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero { padding: 120px 5% 60px; }
            .cta-box { padding: 3rem 2rem; }
            .footer { flex-direction: column; gap: 1.5rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Background Effects -->
    <div class="gradient-bg"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Navigation -->
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">
            <div class="logo-icon">M</div>
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
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                <span>الشريك الرسمي لـ RICOH</span>
            </div>
            <h1 class="hero-title">
                <span class="line">المستقبل</span>
                <span class="line"><span class="gradient-text">يبدأ هنا</span></span>
            </h1>
            <p class="hero-desc">
                نقدم لك أحدث التقنيات اليابانية مع خدمة محلية استثنائية. حيث تلتقي الجودة بالابتكار، وتتحول التحديات إلى فرص.
            </p>
            <div class="hero-btns">
                <a href="<?= BASE_URL ?>/maintenance" class="btn-gradient">
                    <i class="fas fa-rocket"></i> ابدأ رحلتك
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn-glass">
                    استكشف عالمنا
                </a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['products'] ?>+</div>
                <div class="stat-label">منتج ريكو أصلي</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['tickets'] ?>+</div>
                <div class="stat-label">عملية صيانة ناجحة</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['years'] ?>+</div>
                <div class="stat-label">سنة من التميز</div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="services-section">
        <div class="section-header">
            <span class="section-tag">خدماتنا</span>
            <h2 class="section-title">حلول <span>استثنائية</span></h2>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $s): ?>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-check"></i></div>
                <h3 class="service-title"><?= htmlspecialchars($s['name']) ?></h3>
                <p class="service-desc"><?= htmlspecialchars($s['short_description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-box">
            <h2 class="cta-title">هل أنت مستعد للمستقبل؟</h2>
            <p class="cta-desc">انضم إلى آلاف العملاء الذين اختاروا التميز</p>
            <a href="<?= BASE_URL ?>/maintenance" class="btn-gradient">
                <i class="fas fa-bolt"></i> ابدأ الآن
            </a>
        </div>
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

    <!-- Animations -->
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Hero Dramatic Entrance
        const tl = gsap.timeline();
        tl.from('.hero-badge', { opacity: 0, y: 50, duration: 1, delay: 0.3 })
          .from('.hero-title .line', { opacity: 0, y: 80, duration: 1.2, stagger: 0.2 }, "-=0.5")
          .from('.hero-desc', { opacity: 0, y: 40, duration: 1 }, "-=0.7")
          .from('.hero-btns', { opacity: 0, y: 40, duration: 1 }, "-=0.7");

        // Stats Entrance
        gsap.from('.stat-card', {
            opacity: 0,
            y: 60,
            scale: 0.9,
            duration: 1,
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.stats-section',
                start: 'top 80%'
            }
        });

        // Services Entrance
        gsap.from('.service-card', {
            opacity: 0,
            y: 80,
            duration: 1,
            stagger: 0.15,
            scrollTrigger: {
                trigger: '.services-grid',
                start: 'top 80%'
            }
        });

        // CTA Box
        gsap.from('.cta-box', {
            opacity: 0,
            scale: 0.9,
            duration: 1.5,
            scrollTrigger: {
                trigger: '.cta-section',
                start: 'top 70%'
            }
        });

        // Parallax Blobs
        document.addEventListener('mousemove', (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
            
            gsap.to('.blob-1', { x: moveX * 20, y: moveY * 20, duration: 1 });
            gsap.to('.blob-2', { x: -moveX * 15, y: -moveY * 15, duration: 1 });
            gsap.to('.blob-3', { x: moveX * 10, y: moveY * 10, duration: 1 });
        });
    </script>
</body>
</html>

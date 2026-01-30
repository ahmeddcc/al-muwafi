<?php
/**
 * Demo: Editorial / Magazine Design
 * Bold typography, asymmetric layout, newspaper-style
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
$services = [];
try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 LIMIT 3");
} catch (\Throwable $e) {}

if (empty($services)) {
    $services = [
        ['name' => 'صيانة متكاملة', 'short_description' => 'نضمن لك عدم توقف العمل'],
        ['name' => 'قطع أصلية', 'short_description' => 'الجودة هي أولويتنا'],
        ['name' => 'دعم فني', 'short_description' => 'نحن معك دائماً'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | Editorial Style</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --black: #0a0a0a;
            --white: #ffffff;
            --grey: #666;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--white);
            color: var(--black);
        }

        /* NAV */
        .nav {
            border-bottom: 3px solid var(--black);
            padding: 1.5rem 5%;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logo { font-family: 'Amiri', serif; font-size: 2rem; color: var(--black); text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; }
        .nav-links a { color: var(--black); text-decoration: none; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }

        /* HERO */
        .hero {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            min-height: 80vh;
            border-bottom: 3px solid var(--black);
        }
        .hero-main {
            padding: 80px 5%;
            border-left: 3px solid var(--black);
            display: flex; flex-direction: column; justify-content: center;
        }
        .hero-title {
            font-family: 'Amiri', serif;
            font-size: clamp(3rem, 8vw, 6rem);
            line-height: 1;
            margin-bottom: 2rem;
            font-weight: 700;
        }
        .hero-sub { font-size: 1.2rem; color: var(--grey); line-height: 1.8; max-width: 500px; }

        .hero-side {
            background: var(--black);
            color: var(--white);
            padding: 80px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .hero-side h3 { font-size: 1.5rem; margin-bottom: 1rem; }
        .hero-side p { color: #aaa; font-size: 0.95rem; line-height: 1.7; }
        .hero-btn {
            background: var(--white);
            color: var(--black);
            padding: 1rem 2rem;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            margin-top: 2rem;
            transition: 0.3s;
        }
        .hero-btn:hover { background: #f0f0f0; }

        /* SERVICES */
        .section { padding: 80px 5%; }
        .section-header {
            display: flex; justify-content: space-between; align-items: flex-end;
            border-bottom: 3px solid var(--black);
            padding-bottom: 2rem;
            margin-bottom: 4rem;
        }
        .section-header h2 { font-family: 'Amiri', serif; font-size: 3rem; }
        .section-header span { color: var(--grey); }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .service-box {
            border-left: 1px solid #ddd;
            padding: 2rem;
        }
        .service-box:first-child { border-left: none; }
        .service-num { font-family: 'Amiri', serif; font-size: 4rem; color: #ddd; }
        .service-box h3 { font-size: 1.3rem; margin: 1rem 0; }
        .service-box p { color: var(--grey); font-size: 0.9rem; line-height: 1.6; }

        /* FOOTER */
        .footer {
            background: var(--black);
            color: var(--white);
            padding: 40px 5%;
            text-align: center;
        }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; }
            .hero-main { border-left: none; border-bottom: 3px solid var(--black); }
            .services-grid { grid-template-columns: 1fr; }
            .service-box { border-left: none; border-bottom: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo"><?= htmlspecialchars($companyName) ?></a>
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/products">المنتجات</a>
            <a href="<?= BASE_URL ?>/services">الخدمات</a>
            <a href="<?= BASE_URL ?>/contact">تواصل</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-main">
            <h1 class="hero-title">نحن لا نصلح الآلات، نحن نبني الثقة.</h1>
            <p class="hero-sub">منذ أكثر من 15 عاماً ونحن الشريك الأول للشركات الباحثة عن التميز في حلول الطباعة.</p>
        </div>
        <div class="hero-side">
            <div>
                <h3>جاهز للبدء؟</h3>
                <p>افتح تذكرة صيانة وسنتواصل معك خلال ساعات.</p>
                <a href="<?= BASE_URL ?>/maintenance" class="hero-btn">ابدأ الآن ←</a>
            </div>
            <p style="font-size: 0.8rem; opacity: 0.5;">RICOH AUTHORIZED</p>
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h2>خدماتنا</h2>
            <span>ما نقدمه لك</span>
        </div>
        <div class="services-grid">
            <?php $i = 1; foreach ($services as $s): ?>
            <div class="service-box">
                <span class="service-num"><?= $i++ ?></span>
                <h3><?= htmlspecialchars($s['name']) ?></h3>
                <p><?= htmlspecialchars($s['short_description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer">
        <p>© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?>. جميع الحقوق محفوظة.</p>
    </footer>
</body>
</html>

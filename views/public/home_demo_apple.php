<?php
/**
 * Demo: Apple-Inspired Design
 * Ultra-clean, whitespace-heavy, giant typography
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
        ['name' => 'الصيانة', 'short_description' => 'صيانة احترافية لجميع أجهزة ريكو'],
        ['name' => 'قطع الغيار', 'short_description' => 'قطع أصلية مضمونة 100%'],
        ['name' => 'الدعم الفني', 'short_description' => 'دعم متواصل على مدار الساعة'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | Apple Style</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background: #fff;
            color: #1d1d1f;
            line-height: 1.5;
        }

        /* NAV */
        .nav {
            position: fixed; top: 0; width: 100%;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(20px);
            padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            z-index: 1000;
            border-bottom: 1px solid #f5f5f7;
        }
        .logo { font-weight: 700; font-size: 1.5rem; color: #1d1d1f; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; }
        .nav-links a { color: #1d1d1f; text-decoration: none; font-size: 0.9rem; opacity: 0.8; transition: 0.2s; }
        .nav-links a:hover { opacity: 1; }
        .nav-btn { background: #0071e3; color: #fff; padding: 0.5rem 1.5rem; border-radius: 20px; text-decoration: none; font-size: 0.9rem; }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            text-align: center;
            padding: 120px 20px 80px;
        }
        .hero-title {
            font-size: clamp(3rem, 10vw, 7rem);
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
        }
        .hero-title span { color: #0071e3; }
        .hero-sub {
            font-size: 1.5rem;
            color: #86868b;
            max-width: 600px;
            margin-bottom: 3rem;
            font-weight: 400;
        }
        .hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }
        .btn-primary { background: #0071e3; color: #fff; padding: 1rem 2rem; border-radius: 30px; text-decoration: none; font-weight: 500; }
        .btn-secondary { color: #0071e3; padding: 1rem 2rem; text-decoration: none; font-weight: 500; }

        /* SECTION */
        .section {
            padding: 100px 5%;
            text-align: center;
        }
        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .section-sub {
            color: #86868b;
            font-size: 1.2rem;
            margin-bottom: 4rem;
        }

        /* CARDS */
        .cards-row {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: #f5f5f7;
            border-radius: 20px;
            padding: 3rem 2rem;
            flex: 1;
            min-width: 280px;
            max-width: 350px;
            text-align: right;
        }
        .card-icon { font-size: 2.5rem; color: #0071e3; margin-bottom: 1.5rem; }
        .card h3 { font-size: 1.5rem; margin-bottom: 1rem; }
        .card p { color: #86868b; }

        /* FOOTER */
        .footer {
            background: #f5f5f7;
            padding: 60px 5%;
            text-align: center;
        }
        .footer p { color: #86868b; font-size: 0.9rem; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo"><?= htmlspecialchars($companyName) ?></a>
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/products">المنتجات</a>
            <a href="<?= BASE_URL ?>/services">الخدمات</a>
            <a href="<?= BASE_URL ?>/contact">اتصل بنا</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="nav-btn">طلب صيانة</a>
    </nav>

    <section class="hero">
        <h1 class="hero-title">التميز <span>يبدأ</span> هنا.</h1>
        <p class="hero-sub">نقدم حلول ريكو المتكاملة لمكتب المستقبل. بساطة في التعامل، تميز في الأداء.</p>
        <div class="hero-btns">
            <a href="<?= BASE_URL ?>/maintenance" class="btn-primary">ابدأ الآن</a>
            <a href="<?= BASE_URL ?>/products" class="btn-secondary">اكتشف المزيد ›</a>
        </div>
    </section>

    <section class="section" style="background: #fff;">
        <h2 class="section-title">خدماتنا</h2>
        <p class="section-sub">حلول مصممة لتبسيط حياتك المهنية</p>
        <div class="cards-row">
            <?php foreach ($services as $s): ?>
            <div class="card">
                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
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

<?php
/**
 * Demo: Japanese Zen Design
 * Minimal, balanced, harmony-focused, inspired by Ricoh's Japanese heritage
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
        ['name' => 'الصيانة الوقائية', 'short_description' => 'نحافظ على أجهزتك قبل أن تحتاج للإصلاح'],
        ['name' => 'قطع الغيار الأصلية', 'short_description' => 'جودة يابانية لا تقبل المساومة'],
        ['name' => 'الدعم المتواصل', 'short_description' => 'فريق جاهز على مدار الساعة'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | Zen Style</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@100;300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --ink: #1a1a1a;
            --paper: #f8f6f0;
            --accent: #c41e3a; /* Traditional Japanese Red */
            --stone: #6b6b6b;
        }

        body {
            font-family: 'Noto Sans Arabic', sans-serif;
            background: var(--paper);
            color: var(--ink);
            font-weight: 300;
        }

        /* NAV */
        .nav {
            padding: 2rem 10%;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logo { font-size: 1.5rem; font-weight: 400; color: var(--ink); text-decoration: none; letter-spacing: 2px; }
        .nav-link { color: var(--stone); text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid transparent; padding-bottom: 2px; transition: 0.3s; }
        .nav-link:hover { border-color: var(--accent); color: var(--ink); }

        /* HERO */
        .hero {
            min-height: 80vh;
            display: flex; align-items: center;
            padding: 0 10%;
            position: relative;
        }
        .hero-content { max-width: 600px; }
        .hero-title {
            font-size: 4rem;
            font-weight: 100;
            line-height: 1.3;
            margin-bottom: 2rem;
            letter-spacing: -1px;
        }
        .hero-title span { color: var(--accent); font-weight: 400; }
        .hero-sub {
            font-size: 1.1rem;
            color: var(--stone);
            line-height: 2;
            margin-bottom: 3rem;
        }
        .hero-btn {
            display: inline-block;
            border: 1px solid var(--ink);
            padding: 1rem 2.5rem;
            text-decoration: none;
            color: var(--ink);
            font-weight: 400;
            transition: 0.3s;
        }
        .hero-btn:hover { background: var(--ink); color: var(--paper); }

        /* Decorative Line */
        .line-accent {
            width: 60px; height: 2px; background: var(--accent); margin-bottom: 2rem;
        }

        /* SECTION */
        .section { padding: 100px 10%; }
        .section-header { margin-bottom: 4rem; }
        .section-header h2 { font-size: 2.5rem; font-weight: 300; margin-bottom: 1rem; }
        .section-header p { color: var(--stone); }

        /* SERVICES */
        .services-list { display: flex; flex-direction: column; gap: 3rem; max-width: 800px; }
        .service-item {
            display: grid;
            grid-template-columns: 60px 1fr;
            gap: 2rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid #ddd;
        }
        .service-num { font-size: 2rem; color: var(--accent); font-weight: 100; }
        .service-item h3 { font-weight: 400; margin-bottom: 0.5rem; font-size: 1.3rem; }
        .service-item p { color: var(--stone); font-size: 0.95rem; }

        /* FOOTER */
        .footer {
            padding: 60px 10%;
            border-top: 1px solid #ddd;
            display: flex; justify-content: space-between; align-items: center;
        }
        .footer p { color: var(--stone); font-size: 0.85rem; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero { padding: 60px 5%; }
            .section { padding: 60px 5%; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo"><?= htmlspecialchars($companyName) ?></a>
        <div style="display: flex; gap: 2rem;">
            <a href="<?= BASE_URL ?>/products" class="nav-link">المنتجات</a>
            <a href="<?= BASE_URL ?>/services" class="nav-link">الخدمات</a>
            <a href="<?= BASE_URL ?>/maintenance" class="nav-link">طلب صيانة</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="line-accent"></div>
            <h1 class="hero-title">البساطة <span>هي</span> الكمال الحقيقي.</h1>
            <p class="hero-sub">نقدم لك حلول ريكو اليابانية بروح الإتقان والتوازن. لا إفراط، لا تفريط، فقط ما يحتاجه عملك.</p>
            <a href="<?= BASE_URL ?>/maintenance" class="hero-btn">ابدأ رحلتك</a>
        </div>
    </section>

    <section class="section" style="background: #fff;">
        <div class="section-header">
            <h2>ما نقدمه</h2>
            <p>ثلاث ركائز تضمن نجاحك</p>
        </div>
        <div class="services-list">
            <?php $i = 1; foreach ($services as $s): ?>
            <div class="service-item">
                <span class="service-num">0<?= $i++ ?></span>
                <div>
                    <h3><?= htmlspecialchars($s['name']) ?></h3>
                    <p><?= htmlspecialchars($s['short_description'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer">
        <p>© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?></p>
        <a href="<?= BASE_URL ?>/contact" class="nav-link">تواصل معنا</a>
    </footer>
</body>
</html>

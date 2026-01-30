<?php
/**
 * Demo: Corporate Blue Design
 * Professional blue gradients, structured layout, trust-building
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
$services = [];
$stats = ['products' => 50, 'clients' => 300, 'years' => 15];
try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 LIMIT 4");
    $stats['products'] = $db->count('products', 'is_active = 1') ?: 50;
} catch (\Throwable $e) {}

if (empty($services)) {
    $services = [
        ['name' => 'عقود الصيانة', 'short_description' => 'صيانة شاملة لجميع أجهزتكم'],
        ['name' => 'قطع الغيار', 'short_description' => 'قطع أصلية بضمان كامل'],
        ['name' => 'الدعم الفني', 'short_description' => 'فريق متخصص جاهز لمساعدتكم'],
        ['name' => 'التدريب', 'short_description' => 'تدريب فرق العمل على الأجهزة'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | Corporate Style</title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #0052cc;
            --primary-dark: #003d99;
            --secondary: #00c7e6;
            --dark: #172b4d;
            --light: #f4f5f7;
        }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #fff;
            color: var(--dark);
        }

        /* NAV */
        .nav {
            background: var(--dark);
            padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logo { color: #fff; font-weight: 700; font-size: 1.4rem; text-decoration: none; }
        .nav-links { display: flex; gap: 2rem; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.9rem; }
        .nav-links a:hover { color: #fff; }
        .nav-btn { background: var(--secondary); color: var(--dark); padding: 0.6rem 1.5rem; border-radius: 4px; text-decoration: none; font-weight: 600; }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 100px 5%;
            min-height: 70vh;
            display: flex; align-items: center;
        }
        .hero-content { max-width: 700px; }
        .hero-badge { background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.85rem; margin-bottom: 2rem; display: inline-block; }
        .hero-title { font-size: 3rem; font-weight: 700; margin-bottom: 1.5rem; line-height: 1.2; }
        .hero-sub { font-size: 1.2rem; opacity: 0.9; margin-bottom: 2rem; line-height: 1.7; }
        .hero-btn { background: #fff; color: var(--primary); padding: 1rem 2rem; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; }

        /* STATS */
        .stats-bar {
            background: var(--dark);
            padding: 3rem 5%;
            display: flex; justify-content: space-around; flex-wrap: wrap; gap: 2rem;
        }
        .stat { text-align: center; color: #fff; }
        .stat-num { font-size: 3rem; font-weight: 700; color: var(--secondary); }
        .stat-txt { font-size: 0.9rem; opacity: 0.8; }

        /* SERVICES */
        .section { padding: 80px 5%; }
        .section-header { text-align: center; margin-bottom: 4rem; }
        .section-header h2 { font-size: 2.5rem; margin-bottom: 1rem; color: var(--dark); }
        .section-header p { color: #666; font-size: 1.1rem; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto; }
        .card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            padding: 2rem;
            transition: 0.3s;
        }
        .card:hover { border-color: var(--primary); box-shadow: 0 10px 30px rgba(0,82,204,0.1); transform: translateY(-5px); }
        .card-icon { width: 50px; height: 50px; background: var(--light); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
        .card h3 { margin-bottom: 0.5rem; font-size: 1.2rem; }
        .card p { color: #666; font-size: 0.95rem; }

        /* FOOTER */
        .footer { background: var(--dark); color: #fff; padding: 40px 5%; text-align: center; }
        .footer p { opacity: 0.7; font-size: 0.9rem; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-title { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo"><i class="fas fa-building" style="margin-left: 10px;"></i> <?= htmlspecialchars($companyName) ?></a>
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/products">المنتجات</a>
            <a href="<?= BASE_URL ?>/services">الخدمات</a>
            <a href="<?= BASE_URL ?>/about">عن الشركة</a>
            <a href="<?= BASE_URL ?>/contact">اتصل بنا</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="nav-btn">بوابة العملاء</a>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <span class="hero-badge"><i class="fas fa-shield-alt"></i> وكيل ريكو المعتمد</span>
            <h1 class="hero-title">شريكك الاستراتيجي لحلول الأعمال المكتبية</h1>
            <p class="hero-sub">نقدم خدمات صيانة احترافية وحلول طباعة متكاملة للشركات التي تبحث عن الموثوقية والاستمرارية.</p>
            <a href="<?= BASE_URL ?>/maintenance" class="hero-btn"><i class="fas fa-arrow-left"></i> ابدأ الآن</a>
        </div>
    </section>

    <div class="stats-bar">
        <div class="stat"><span class="stat-num"><?= $stats['products'] ?>+</span><span class="stat-txt">منتج متوفر</span></div>
        <div class="stat"><span class="stat-num"><?= $stats['clients'] ?>+</span><span class="stat-txt">عميل نخدمه</span></div>
        <div class="stat"><span class="stat-num"><?= $stats['years'] ?>+</span><span class="stat-txt">سنة خبرة</span></div>
    </div>

    <section class="section">
        <div class="section-header">
            <h2>خدماتنا المتميزة</h2>
            <p>حلول شاملة لكل احتياجات مكتبك</p>
        </div>
        <div class="grid">
            <?php foreach ($services as $s): ?>
            <div class="card">
                <div class="card-icon"><i class="fas fa-check"></i></div>
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

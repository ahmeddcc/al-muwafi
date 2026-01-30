<?php
/**
 * Demo: Neo-Brutalism Design
 * Bold colors, thick borders, rebellious and attention-grabbing
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
        ['name' => 'صيانة شاملة', 'short_description' => 'نصلح كل شيء!'],
        ['name' => 'قطع غيار', 'short_description' => 'الأصلية فقط.'],
        ['name' => 'دعم فني', 'short_description' => 'نحن هنا 24/7.'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | Neo-Brutalism</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --yellow: #ffe600;
            --pink: #ff6b6b;
            --blue: #4ecdc4;
            --black: #000;
            --white: #fff;
        }

        body {
            font-family: 'Rubik', sans-serif;
            background: var(--yellow);
            color: var(--black);
        }

        /* NAV */
        .nav {
            background: var(--black);
            color: var(--white);
            padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 5px solid var(--yellow);
        }
        .logo { font-size: 1.5rem; font-weight: 900; text-decoration: none; color: var(--yellow); }
        .nav-links { display: flex; gap: 2rem; }
        .nav-links a { color: var(--white); text-decoration: none; font-weight: 700; }
        .nav-btn {
            background: var(--pink);
            color: var(--black);
            padding: 0.8rem 1.5rem;
            border: 4px solid var(--black);
            font-weight: 900;
            text-decoration: none;
            box-shadow: 4px 4px 0 var(--black);
            transition: 0.1s;
        }
        .nav-btn:hover { transform: translate(2px, 2px); box-shadow: 2px 2px 0 var(--black); }

        /* HERO */
        .hero {
            min-height: 80vh;
            padding: 100px 5%;
            display: flex; align-items: center; justify-content: center;
            text-align: center;
        }
        .hero-content { max-width: 900px; }
        .hero-title {
            font-size: clamp(3rem, 10vw, 8rem);
            font-weight: 900;
            line-height: 0.9;
            margin-bottom: 2rem;
            text-transform: uppercase;
        }
        .hero-title span { color: var(--pink); display: block; }
        .hero-sub {
            font-size: 1.3rem;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto; margin-right: auto;
        }
        .hero-btn {
            display: inline-block;
            background: var(--black);
            color: var(--yellow);
            padding: 1.5rem 3rem;
            font-size: 1.5rem;
            font-weight: 900;
            text-decoration: none;
            border: 5px solid var(--black);
            box-shadow: 8px 8px 0 var(--black);
            transition: 0.1s;
        }
        .hero-btn:hover { transform: translate(4px, 4px); box-shadow: 4px 4px 0 var(--black); background: var(--pink); color: var(--black); }

        /* SERVICES */
        .section {
            background: var(--white);
            padding: 80px 5%;
            border-top: 5px solid var(--black);
        }
        .section-header { text-align: center; margin-bottom: 4rem; }
        .section-header h2 { font-size: 3rem; font-weight: 900; text-transform: uppercase; }

        .cards-row {
            display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;
        }
        .card {
            background: var(--blue);
            border: 5px solid var(--black);
            padding: 2rem;
            max-width: 320px;
            box-shadow: 8px 8px 0 var(--black);
            transition: 0.1s;
        }
        .card:hover { transform: translate(4px, 4px); box-shadow: 4px 4px 0 var(--black); }
        .card h3 { font-size: 1.5rem; margin-bottom: 1rem; font-weight: 900; }
        .card p { font-size: 1rem; }

        /* FOOTER */
        .footer {
            background: var(--black);
            color: var(--yellow);
            padding: 40px 5%;
            text-align: center;
            font-weight: 900;
            border-top: 5px solid var(--yellow);
        }

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
            <a href="<?= BASE_URL ?>/contact">اتصل</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="nav-btn">طلب صيانة!</a>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">نحن <span>المُوَفِّي!</span></h1>
            <p class="hero-sub">ننقذ طابعتك من الموت البطيء. صيانة سريعة، قطع أصلية، وفريق لا يعرف المستحيل.</p>
            <a href="<?= BASE_URL ?>/maintenance" class="hero-btn">ابدأ الآن! →</a>
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h2>ماذا نفعل؟</h2>
        </div>
        <div class="cards-row">
            <?php foreach ($services as $s): ?>
            <div class="card">
                <h3><?= htmlspecialchars($s['name']) ?></h3>
                <p><?= htmlspecialchars($s['short_description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer">
        <p>© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?> — نحن الأفضل، ونقطة.</p>
    </footer>
</body>
</html>

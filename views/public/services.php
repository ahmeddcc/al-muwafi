<?php
/**
 * صفحة الخدمات
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Settings;

$currentPage = 'services';
$companyInfo = Settings::getCompanyInfo();
$title = 'خدماتنا | ' . ($companyInfo['name'] ?? 'المُوَفِّي');

// --- Extra CSS ---
ob_start();
?>
    <style>
        /* === SERVICES CINEMATIC THEME === */
        :root {
            --bg-deep: #020617;
            --bg-dark: #0f172a;
            --gold-1: #D4AF37;
            --gold-2: #F1D87E;
            --neon-blue: #0ea5e9;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(30, 41, 59, 0.6);
        }

        /* === UNIFIED BACKGROUND ORBS === */
        .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
        @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }

        /* === PAGE LOADER === */
        #page-loader {
            position: fixed; inset: 0; background: var(--bg-deep); z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.5s ease, visibility 0.5s;
        }
        .loader-spinner {
            width: 50px; height: 50px; border: 3px solid rgba(255,255,255,0.1);
            border-top-color: var(--gold-1); border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        body {
            background-color: var(--bg-deep);
            color: #f8fafc;
        }

        /* Hero Section */
        .services-hero {
            position: relative;
            padding: 8rem 0 4rem;
            text-align: center;
            overflow: hidden;
        }
        
        .hero-bg-glow {
            position: absolute; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%);
            top: -100px; left: 50%; transform: translateX(-50%);
            border-radius: 50%; pointer-events: none; z-index: 0;
        }

        .services-title {
            font-size: 4rem; font-weight: 900;
            background: linear-gradient(to bottom, #fff, var(--gold-2), var(--gold-1));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 20px; position: relative; z-index: 1;
        }

        .services-subtitle {
             font-size: 1.2rem; color: #94a3b8; max-width: 600px; margin: 0 auto;
             position: relative; z-index: 1;
        }

        /* Cards Grid */
        .services-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px; padding: 4rem 0;
        }

        .service-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            display: flex; flex-direction: column;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            border-color: var(--gold-1);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
        }

        .service-img-box {
            height: 200px; overflow: hidden; position: relative;
        }
        .service-img-box::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to top, var(--bg-dark), transparent);
        }
        .service-img {
            width: 100%; height: 100%; object-fit: cover;
            transition: 0.5s;
        }
        .service-card:hover .service-img { transform: scale(1.1); }
        
        .service-icon-float {
            position: absolute; bottom: 15px; right: 20px;
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--gold-1), var(--gold-3));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 2;
        }

        .service-content { padding: 25px; flex: 1; display: flex; flex-direction: column; }
        
        .service-title {
            color: #fff; font-size: 1.5rem; font-weight: 700; margin-bottom: 10px;
        }
        .service-desc {
            color: #ccc; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex: 1;
        }
        
        .service-btn {
            align-self: flex-start;
            padding: 8px 20px; border-radius: 8px;
            background: rgba(255,255,255,0.05); color: var(--gold-1);
            text-decoration: none; border: 1px solid rgba(212, 175, 55, 0.3);
            transition: 0.3s;
        }
        .service-btn:hover {
            background: var(--gold-1); color: #000; border-color: var(--gold-1);
        }

        /* Empty State */
        .empty-state {
            text-align: center; padding: 4rem;
            background: rgba(255,255,255,0.02);
            border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1);
        }

        /* CTA Section */
        .cta-section {
            padding: 4rem 1rem;
            text-align: center;
            background: linear-gradient(to right, rgba(14, 165, 233, 0.1), transparent);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            margin-top: 2rem;
        }
    </style>
<?php
$extraHead = ob_get_clean();

// --- Main Content ---
ob_start();
?>
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-spinner"></div>
    </div>

    <!-- Background Orbs -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <!-- Hero -->
    <section class="services-hero">
        <div class="hero-bg-glow"></div>
        <div class="container">
            <h1 class="services-title">خدماتنا المتميزة</h1>
            <p class="services-subtitle">حلول متكاملة مصممة خصيصاً لرفع كفاءة أعمالك إلى مستويات جديدة.</p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="section">
        <div class="container">
            <?php if (empty($services)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem; color: var(--gold-1); opacity: 0.5;">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 style="color: #fff; font-size: 1.5rem; margin-bottom: 10px;">لا توجد خدمات حالياً</h3>
                <p style="color: #94a3b8;">نقوم حالياً بتحديث قائمة خدماتنا، يرجى التحقق لاحقاً.</p>
            </div>
            <?php else: ?>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-img-box">
                        <img src="<?= $service['image'] ? BASE_URL . '/storage/uploads/' . htmlspecialchars($service['image']) : 'https://placehold.co/600x400/1e293b/FFF?text=Service' ?>" 
                             alt="<?= htmlspecialchars($service['name']) ?>" class="service-img">
                        <?php if (!empty($service['icon'])): ?>
                            <div class="service-icon-float"><?= $service['icon'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="service-content">
                        <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                        <p class="service-desc"><?= htmlspecialchars($service['short_description'] ?? '') ?></p>
                        <a href="<?= BASE_URL ?>/services/show/<?= htmlspecialchars($service['slug']) ?>" class="service-btn">
                            التفاصيل الكاملة <i class="fas fa-arrow-left" style="font-size: 0.8em; margin-right: 5px;"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2 style="font-size: 2rem; color: #fff; margin-bottom: 1rem;">هل تحتاج إلى حل مخصص؟</h2>
            <p style="color: #94a3b8; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                فريقنا الهندسي مستعد لتقديم استشارات فنية وحلول صيانة تناسب احتياجات مؤسستك بدقة.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="<?= BASE_URL ?>/contact" class="service-btn" style="background: var(--gold-1); color: #000; border: none; font-weight: 700; padding: 12px 30px;">
                    تواصل معنا الآن
                </a>
                <a href="<?= BASE_URL ?>/maintenance" class="service-btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 30px;">
                    طلب صيانة فورية
                </a>
            </div>
        </div>
    </section>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public_layout.php';
?>

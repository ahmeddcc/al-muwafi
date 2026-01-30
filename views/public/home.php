<?php
/**
 * Homepage - FINAL Design
 * Deep Blue + Gold + Cyan + Glass-morphism
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
        ['name' => 'عقود الصيانة', 'short_description' => 'صيانة شاملة بأعلى معايير الجودة'],
        ['name' => 'قطع الغيار الأصلية', 'short_description' => 'ضمان الجودة اليابانية'],
        ['name' => 'الدعم التقني', 'short_description' => 'فريق متخصص على مدار الساعة'],
        ['name' => 'حلول الأرشفة', 'short_description' => 'رقمنة وأتمتة سير العمل'],
    ];
}

$title = 'وكيل ريكو المعتمد';
$currentPage = 'home';

// --- Extra CSS ---
ob_start(); 
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<style>
    /* === UNIFIED BACKGROUND ORBS === */
    .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
    .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
    .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
    .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
    @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }

    /* === PAGE LOADER === */
    #page-loader {
        position: fixed; inset: 0; background: #020617; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s;
    }
    .loader-spinner {
        width: 50px; height: 50px; border: 3px solid rgba(255,255,255,0.1);
        border-top-color: #D4AF37; border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    /* === COMPONENT SPECIFIC STYLES === */
    /* Hero Badge & Typography - Keeping simplified local overrides if needed or moving to global in future */
    .hero {
        min-height: 38vh; /* Ultra compact */
        display: flex; align-items: center; 
        padding: 75px 5% 10px; /* Minimal padding */
        position: relative; z-index: 2;
        background: transparent; 
    }
    .hero-container {
        width: 100%; max-width: 1600px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem; align-items: center;
    }
    .hero-content { max-width: 600px; }
    .hero-badge {
        display: inline-flex; align-items: center; gap: 8px; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2); padding: 10px 25px; border-radius: 50px; font-size: 1.5rem; font-weight: bold; color: var(--neon-blue); margin-bottom: 2rem;
    }
    .hero-badge i { animation: pulse 2s infinite; }
    @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
    .hero-title {
        font-size: clamp(1.8rem, 4vw, 3.5rem); font-weight: 900; line-height: 1.5; margin-bottom: 2rem; text-shadow: 0 0 40px rgba(14, 165, 233, 0.3); position: relative;
    }
    .hero-title::after {
        content: ''; position: absolute; bottom: -10px; right: 0; width: 120px; height: 4px; background: linear-gradient(90deg, var(--gold-1), var(--gold-2), var(--neon-blue)); border-radius: 2px; animation: expandLine 2s ease-out forwards;
    }
    @keyframes expandLine { from { width: 0; opacity: 0; } to { width: 120px; opacity: 1; } }
    .hero-title .gold-text {
        background: linear-gradient(to right, var(--gold-1), var(--gold-2), var(--gold-3), var(--gold-1)); background-size: 300% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: goldShine 3s linear infinite; text-shadow: none; filter: drop-shadow(0 0 20px rgba(191, 149, 63, 0.4));
    }
    .hero-desc {
        font-size: 1.25rem; color: var(--text-muted); line-height: 1.9; margin-bottom: 2.5rem; background: linear-gradient(135deg, rgba(14, 165, 233, 0.05), rgba(191, 149, 63, 0.05)); padding: 1.5rem; border-radius: 12px; border-right: 3px solid var(--neon-blue); animation: fadeSlideIn 1s ease-out 0.5s both;
    }
    @keyframes fadeSlideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    .hero-btns { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .btn-primary {
        background: linear-gradient(135deg, var(--neon-blue), #3b82f6); color: #fff; padding: 1rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 20px var(--neon-glow); transition: all 0.3s;
    }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 30px var(--neon-glow); }
    .btn-gold {
        background: linear-gradient(135deg, var(--gold-1), var(--gold-3)); color: #000; padding: 1rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 20px rgba(191, 149, 63, 0.3); transition: all 0.3s;
    }
    .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(191, 149, 63, 0.4); }

    /* === HERO VISUAL (Neon Image) === */
    .hero-visual { position: relative; display: flex; align-items: center; justify-content: center; }
    .neon-image-container { position: relative; width: 100%; }
    .neon-image { width: 100%; height: auto; filter: drop-shadow(0 0 30px var(--neon-glow)) drop-shadow(0 0 60px rgba(191, 149, 63, 0.2)); animation: neonPulse 3s ease-in-out infinite; }
    @keyframes neonPulse { 0%, 100% { filter: drop-shadow(0 0 30px var(--neon-glow)) drop-shadow(0 0 60px rgba(191, 149, 63, 0.2)); } 50% { filter: drop-shadow(0 0 50px var(--neon-glow)) drop-shadow(0 0 80px rgba(191, 149, 63, 0.3)); } }

    /* === PAPER FEEDER INPUT ANIMATION === */
    .paper-feeder { position: absolute; top: 10%; right: 35%; width: 120px; height: 80px; pointer-events: none; z-index: 8; overflow: visible; perspective: 500px; }
    .feeder-paper { position: absolute; right: 0; width: 70px; height: 80px; background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%); border-radius: 2px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); opacity: 0; transform-origin: center center; transform: rotateX(60deg); }
    .feeder-paper::before { content: ''; position: absolute; top: 6px; left: 5px; right: 5px; height: 4px; background: linear-gradient(90deg, #64748b, #94a3b8); border-radius: 1px; }
    .feeder-paper::after { content: ''; position: absolute; top: 16px; left: 5px; width: 35px; height: 50px; background: repeating-linear-gradient(180deg, #d0d0d0 0px, #d0d0d0 2px, transparent 2px, transparent 8px); }
    .feeder-paper-1 { animation: feedPaperHorizontal 2.5s ease-in-out infinite; }
    .feeder-paper-2 { animation: feedPaperHorizontal 2.5s ease-in-out infinite; animation-delay: 0.9s; }
    .feeder-paper-3 { animation: feedPaperHorizontal 2.5s ease-in-out infinite; animation-delay: 1.8s; }
    @keyframes feedPaperHorizontal {
        0% { opacity: 0; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(60px) translateY(0); clip-path: inset(0 0 0 0); }
        10% { opacity: 1; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(40px) translateY(0); clip-path: inset(0 0 0 0); }
        25% { opacity: 1; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(20px) translateY(2px); clip-path: inset(0 0 0 15%); }
        40% { opacity: 1; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-10px) translateY(4px); clip-path: inset(0 0 0 40%); }
        60% { opacity: 1; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-40px) translateY(6px); clip-path: inset(0 0 0 65%); }
        80% { opacity: 0.8; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-70px) translateY(8px); clip-path: inset(0 0 0 85%); }
        100% { opacity: 0; transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-100px) translateY(10px); clip-path: inset(0 0 0 100%); }
    }

    /* === PROFESSIONAL PAPER OUTPUT ANIMATION === */
    .paper-output-area { position: absolute; bottom: 42%; left: 20%; width: 80px; height: 100px; pointer-events: none; z-index: 10; }
    .output-paper { position: absolute; width: 70px; height: 95px; background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%); border-radius: 2px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2), 0 0 20px rgba(14, 165, 233, 0.3); padding: 8px; opacity: 0; }
    .output-paper::before { content: ''; position: absolute; top: 6px; left: 6px; right: 6px; height: 4px; background: linear-gradient(90deg, #0ea5e9, #22d3ee); border-radius: 2px; }
    .output-paper .paper-lines { margin-top: 16px; display: flex; flex-direction: column; gap: 5px; }
    .output-paper .paper-line { height: 2px; background: #d0d7de; border-radius: 1px; }
    .output-paper .paper-line:nth-child(1) { width: 90%; }
    .output-paper .paper-line:nth-child(2) { width: 75%; }
    .output-paper .paper-line:nth-child(3) { width: 85%; }
    .output-paper .paper-line:nth-child(4) { width: 60%; }
    .output-paper .paper-line:nth-child(5) { width: 80%; }
    .output-paper-1 { animation: slidePaperOut 5s ease-out infinite; }
    .output-paper-1::before { background: linear-gradient(90deg, #0ea5e9, #22d3ee); }
    .output-paper-1 .paper-line { background: #bae6fd; }
    .output-paper-2 { animation: slidePaperOut 5s ease-out infinite; animation-delay: 1s; }
    .output-paper-2::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .output-paper-2 .paper-line { background: #fde68a; }
    .output-paper-3 { animation: slidePaperOut 5s ease-out infinite; animation-delay: 2s; }
    .output-paper-3::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .output-paper-3 .paper-line { background: #a7f3d0; }
    .output-paper-4 { animation: slidePaperOut 5s ease-out infinite; animation-delay: 3s; }
    .output-paper-4::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .output-paper-4 .paper-line { background: #ddd6fe; }
    .output-paper-5 { animation: slidePaperOut 5s ease-out infinite; animation-delay: 4s; }
    .output-paper-5::before { background: linear-gradient(90deg, #ec4899, #f472b6); }
    .output-paper-5 .paper-line { background: #fbcfe8; }
    @keyframes slidePaperOut {
        0% { opacity: 0; transform: translateX(0) translateY(0) rotate(0deg); }
        10% { opacity: 1; transform: translateX(-20px) translateY(5px) rotate(-2deg); }
        30% { opacity: 1; transform: translateX(-50px) translateY(15px) rotate(-5deg); }
        50% { opacity: 1; transform: translateX(-80px) translateY(30px) rotate(-8deg); }
        70% { opacity: 0.8; transform: translateX(-100px) translateY(45px) rotate(-10deg); }
        100% { opacity: 0; transform: translateX(-120px) translateY(60px) rotate(-12deg); }
    }

    /* === RESPONSIVE === */
    @media (max-width: 1024px) {
        .hero-container { grid-template-columns: 1fr; text-align: center; }
        .hero-visual { display: flex; justify-content: center; margin-top: 3rem; order: -1; }
        .hero-content { margin: 0 auto; padding: 0 20px; }
        .hero-title { font-size: 2rem; }
        .hero-btns { justify-content: center; flex-wrap: wrap; gap: 12px; }
    }
    @media (max-width: 768px) {
        .hero { padding: 100px 20px 60px; }
        .hero-content { padding: 0 15px; text-align: center; }
        .hero-title { font-size: 1.6rem; }
        .hero-desc { font-size: 1rem; padding: 1rem; }
        .hero-btns { flex-direction: column; align-items: center; }
        .hero-btns a { width: 100%; max-width: 280px; justify-content: center; }
    }
    @media (max-width: 480px) {
        .hero { padding: 90px 15px 50px; }
        .hero-title { font-size: 1.4rem; }
        .hero-badge { font-size: 1rem; padding: 8px 16px; }
    }
</style>
<?php 
$extraHead = ob_get_clean();

// --- MAIN CONTENT ---
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
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                <span>الموافي لمهمات المكاتب</span>
            </div>
            <h1 class="hero-title">
                شريكك في <span class="gold-text">التميز</span> والابتكار التقني
            </h1>
            <p class="hero-desc">
                نقدم لك أحدث حلول ريكو اليابانية مع خدمة محلية لا مثيل لها. 
                من الصيانة الشاملة إلى قطع الغيار الأصلية، نحن هنا لضمان نجاحك.
            </p>
            <div class="hero-btns">
                <a href="<?= BASE_URL ?>/products" class="btn-primary">
                    <i class="fas fa-magic"></i> اكتشف الحلول الذكية
                </a>
                <a href="<?= BASE_URL ?>/contact" class="btn-gold">
                    <i class="fas fa-headset"></i> استشارة خبير
                </a>
            </div>
        </div>

        <!-- Neon Image Area -->
        <div class="hero-visual">
            <div class="neon-image-container">
                <!-- Paper Feeder Input Animation -->
                <div class="paper-feeder">
                    <div class="feeder-paper feeder-paper-1"></div>
                    <div class="feeder-paper feeder-paper-2"></div>
                    <div class="feeder-paper feeder-paper-3"></div>
                </div>
                <!-- Professional Paper Output Animation -->
                <div class="paper-output-area">
                    <div class="output-paper output-paper-1">
                        <div class="paper-lines">
                            <div class="paper-line" style="width: 90%"></div>
                            <div class="paper-line" style="width: 70%"></div>
                            <div class="paper-line" style="width: 85%"></div>
                            <div class="paper-line" style="width: 60%"></div>
                            <div class="paper-line" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="output-paper output-paper-2">
                        <div class="paper-lines">
                            <div class="paper-line" style="width: 100%"></div>
                            <div class="paper-line" style="width: 100%"></div>
                            <div class="paper-line" style="width: 80%"></div>
                            <div class="paper-line" style="width: 100%"></div>
                            <div class="paper-line" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="output-paper output-paper-3">
                        <div class="paper-lines">
                            <div class="paper-line" style="width: 60%"></div>
                            <div class="paper-line" style="width: 90%"></div>
                            <div class="paper-line" style="width: 75%"></div>
                            <div class="paper-line" style="width: 95%"></div>
                            <div class="paper-line" style="width: 40%"></div>
                        </div>
                    </div>
                    <div class="output-paper output-paper-4">
                        <div class="paper-lines">
                            <div class="paper-line" style="width: 80%"></div>
                            <div class="paper-line" style="width: 65%"></div>
                            <div class="paper-line" style="width: 90%"></div>
                            <div class="paper-line" style="width: 70%"></div>
                            <div class="paper-line" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="output-paper output-paper-5">
                        <div class="paper-lines">
                            <div class="paper-line" style="width: 70%"></div>
                            <div class="paper-line" style="width: 90%"></div>
                            <div class="paper-line" style="width: 80%"></div>
                            <div class="paper-line" style="width: 50%"></div>
                            <div class="paper-line" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Main Device Image -->
                <?php
                // Try to find a hero image in uploads
                $heroImg = 'hero-neon.png';
                if (!file_exists(dirname(__DIR__, 2) . '/storage/uploads/' . $heroImg)) {
                    // Fallback or use a placeholder if not found
                    $heroImg = 'default-hero.png'; // Placeholder fallback
                }
                ?>
                <img src="<?= BASE_URL ?>/storage/uploads/<?= $heroImg ?>" alt="Ricoh IM C6000" class="neon-image">
            </div>
        </div>
    </div>
</section>

<!-- Additional sections (About, Services, Stats) can be included here similarly -->
<!-- ... -->

<?php
$content = ob_get_clean();

// --- Extra Scripts ---
ob_start();
?>
<script>
    // Hide page loader when content is ready
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    });

    gsap.registerPlugin(ScrollTrigger);
    
    // Hero Animations
    gsap.from(".hero-content", {
        duration: 1.2,
        y: 50,
        opacity: 0,
        ease: "power3.out"
    });
    
    gsap.from(".hero-visual", {
        duration: 1.5,
        x: 50,
        opacity: 0,
        ease: "power3.out",
        delay: 0.3
    });
</script>
<?php
$extraScripts = ob_get_clean();

// Include Layout
include VIEWS_PATH . '/layouts/public_layout.php';
?>

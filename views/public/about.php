<?php
// Setup Navigation Data
use App\Services\Database;
use App\Services\Settings;

$currentPage = 'about';
$companyInfo = Settings::getCompanyInfo();
$companyName = $companyInfo['name'] ?? 'المُوَفِّي';

// Dynamic Data (Fallback to defaults)
$vision = 'نسعى لنكون الخيار الأول في حلول الطباعة الرقمية والذكية في المنطقة.';
$mission = 'تمكين الشركات والمؤسسات من العمل بلا توقف عبر توفير أحدث معدات ريكو.';
$values = 'الشفافية المطلقة، السرعة في الإنجاز، والجودة التي لا تقبل المساومة.';

$title = 'من نحن | ' . htmlspecialchars($companyName);

// --- Extra CSS ---
ob_start();
?>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Oswald:wght@400;500;700&family=Katibeh&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* === CORE THEME === */
        :root {
            --bg-deep: #020617;
            --bg-dark: #0f172a;
            --gold-1: #D4AF37;
            --gold-2: #F1D87E;
            --gold-3: #BF953F;
            --neon-blue: #0ea5e9;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(30, 41, 59, 0.4);
        }

        body {
            background-color: var(--bg-deep);
            color: #f8fafc;
            font-family: 'Cairo', sans-serif;
            margin: 0; overflow-x: hidden;
        }

        /* === UNIFIED BACKGROUND (Matching Products Page) === */
        .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
        @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }

        /* === PAGE LOADER === */
        .page-loader {
            position: fixed; inset: 0; z-index: 9999;
            background: var(--bg-deep);
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.5s, visibility 0.5s;
        }
        .page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .loader-spinner {
            width: 50px; height: 50px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: var(--gold-1);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* === HERO SECTION === */
        .page-hero {
            height: 100vh; position: relative;
            display: flex; align-items: center; justify-content: center;
            text-align: center; overflow: hidden;
            z-index: 1;
        }

        .hero-title {
            font-size: 5.5rem; font-weight: 900;
            background: linear-gradient(to bottom, #fff, var(--gold-2), var(--gold-1));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px; text-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        
        .hero-tag {
            color: var(--gold-1); font-size: 1.2rem; letter-spacing: 5px;
            text-transform: uppercase; margin-bottom: 10px; display: block;
        }

        @keyframes spin { to { transform: rotate(360deg); } }


        /* B. WHY CHOOSE US (Replaces Timeline) */
        .features-section { padding: 80px 5%; background: rgba(255,255,255,0.02); }
        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px; max-width: 1200px; margin: 0 auto;
        }
        .feature-item {
            display: flex; align-items: flex-start; gap: 20px;
            padding: 30px; border-radius: 12px;
            background: linear-gradient(135deg, rgba(30,41,59,0.4), transparent);
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
        }
        .feature-item:hover {
            transform: translateY(-5px);
            background: rgba(30,41,59,0.7);
            border-color: var(--gold-1);
        }
        .feature-icon-box {
            min-width: 60px; height: 60px;
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold-1);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
        }
        .feature-item:hover .feature-icon-box { background: var(--gold-1); color: #000; }
        
        .feature-text h3 { margin: 0 0 10px; color: #fff; font-size: 1.3rem; }
        .feature-text p { margin: 0; color: #94a3b8; line-height: 1.5; font-size: 0.95rem; }
        
        /* === HERO SPINNING CIRCLE (The "First Shape") === */
        @keyframes rotateCircle { from { transform: translate(-50%, -50%) rotate(0deg); } to { transform: translate(-50%, -50%) rotate(360deg); } }
        .hero-gear-bg {
            position: absolute;
            top: 50%; left: 50%;
            width: 700px; height: 700px;
            transform: translate(-50%, -50%);
            z-index: 1;
            opacity: 0.15; /* Clearer visibility */
            animation: rotateCircle 60s linear infinite;
            background: transparent;
            border: 2px dashed var(--gold-1);
            border-radius: 50%;
            pointer-events: auto; /* Enable hover interaction */
            transition: all 0.5s ease;
        }
        
        .hero-gear-bg:hover {
            opacity: 0.8;
            border-color: #fff;
            box-shadow: 0 0 30px var(--gold-1), inset 0 0 30px var(--gold-1);
            cursor: pointer;
        }

        /* === VALUES (C) === */
        .values-section { padding: 100px 5%; margin-top: -100px; position: relative; z-index: 10; }
        .values-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; max-width: 1400px; margin: 0 auto;
        }
        .value-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px; padding: 40px;
            transition: 0.4s; transform-style: preserve-3d;
        }
        .value-card:hover {
            transform: translateY(-15px) rotateX(5deg);
            border-color: var(--gold-1);
            background: rgba(30, 41, 59, 0.8);
        }

        /* === STATS (D) === */
        /* Updated to be a "Strip" as requested */
        .stats-strip {
            background: linear-gradient(90deg, transparent, rgba(15, 23, 42, 0.9), transparent);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            padding: 60px 0;
            margin: 100px 0;
            display: flex; justify-content: center; align-items: center;
        }
        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 50px; max-width: 1200px; width: 100%;
        }
        .stat-box { text-align: center; }
        .stat-num {
            font-size: 4rem; font-weight: 800; color: #fff;
            display: block; font-family: 'Oswald';
            text-shadow: 0 0 20px rgba(255,255,255,0.2);
        }
        .stat-label { color: var(--gold-2); font-size: 1.1rem; letter-spacing: 1px; }

        /* === TEAM/EXPERTS (E) - ULTIMATE MODERN === */
        .team-section { 
            padding: 100px 5%; 
            position: relative; 
            background: #020617;
            text-align: center;
        }

        .section-header { margin-bottom: 70px; }
        .section-header h2 { font-size: 3rem; color: #fff; margin-bottom: 20px; font-weight: 800; letter-spacing: -1px; }
        .section-header p { color: #94a3b8; font-size: 1.2rem; max-width: 700px; margin: 0 auto; }

        .experts-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 40px;
            max-width: 1300px; margin: 0 auto;
        }

        .expert-card {
            background: rgba(30, 41, 59, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 50px 30px;
            border-radius: 4px; /* Sharp corners for professional look */
            position: relative;
            transition: all 0.4s ease;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            overflow: hidden;
        }
        
        /* Top Gold Line Accent */
        .expert-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--gold-3), var(--gold-1));
            transform: scaleX(0); transform-origin: center; transition: 0.4s;
        }
        
        .expert-card:hover {
            background: rgba(30, 41, 59, 0.6);
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.2);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
        }
        .expert-card:hover::before { transform: scaleX(1); }

        /* Icon Circle */
        .expert-icon-box {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 30px;
            transition: 0.4s;
        }
        .expert-card:hover .expert-icon-box {
            border-color: var(--gold-1);
            background: rgba(212, 175, 55, 0.1);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.2);
        }

        .expert-icon { font-size: 2.5rem; color: #cbd5e1; transition: 0.4s; }
        .expert-card:hover .expert-icon { color: var(--gold-1); transform: scale(1.1); }

        .expert-title { 
            color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: 10px; 
            letter-spacing: 0.5px; 
        }
        .expert-desc { 
            color: #94a3b8; font-size: 1rem; line-height: 1.7; 
            max-width: 90%;
        }

        /* === CTA (F) === */
        .cta-section {
            padding: 100px 0; text-align: center;
            background: radial-gradient(circle at center, #1e293b 0%, #020617 70%);
        }
        .cta-btn {
            background: transparent; border: 2px solid var(--gold-1);
            color: var(--gold-1); padding: 15px 50px; font-size: 1.2rem;
            border-radius: 50px; cursor: pointer; transition: 0.3s;
            text-decoration: none; display: inline-block; margin-top: 30px;
        }
        .cta-btn:hover {
            background: var(--gold-1); color: #000;
            box-shadow: 0 0 30px var(--gold-1);
        }

        /* Custom Font & Signature Styles */
        @font-face {
            font-family: 'Arslan Wessam';
            src: url('<?= BASE_URL ?>/assets/fonts/ArslanWessam.ttf') format('truetype');
            font-weight: normal; font-style: normal;
        }
        .signature-text {
            font-family: 'Arslan Wessam', serif; 
            font-size: 4.5rem; 
            color: var(--gold-1); 
            transform: rotate(-8deg) skewX(-5deg); 
            font-weight: 400; 
            line-height: 1;
            transition: all 0.5s ease;
            cursor: default;
            text-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }
        .signature-wrapper:hover .signature-text {
            color: #fff; /* White hot center */
            text-shadow: 
                0 0 10px #D4AF37,
                0 0 20px #D4AF37,
                0 0 40px #D4AF37,
                0 0 80px #D4AF37;
            filter: drop-shadow(0 0 10px var(--gold-2));
        }

        /* === RESPONSIVE DESIGN === */
        @media (max-width: 992px) {
            .hero-title { font-size: 3rem; }
            .signature-text { font-size: 3rem; }
            .hero-gear-bg { width: 500px; height: 500px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 30px; }
            .stat-num { font-size: 3rem; }
        }

        @media (max-width: 768px) {
            .page-hero { height: auto; min-height: 100vh; padding: 120px 20px 60px; }
            .hero-title { font-size: 2.2rem; line-height: 1.4; }
            .signature-text { font-size: 2.5rem; transform: rotate(-5deg) skewX(-3deg); }
            .signature-wrapper { transform: translateX(0) !important; }
            .hero-gear-bg { width: 350px; height: 350px; opacity: 0.1; }
            
            .values-section { padding: 60px 5%; margin-top: 0; }
            .values-grid { gap: 20px; }
            .value-card { padding: 30px 20px; }
            
            .features-section { padding: 60px 5%; }
            .features-grid { gap: 20px; }
            .feature-item { flex-direction: column; text-align: center; }
            .feature-icon-box { margin: 0 auto 15px; }
            
            .team-section { padding: 60px 5%; }
            .section-header h2 { font-size: 2rem; }
            .experts-grid { gap: 20px; }
            .expert-card { padding: 35px 20px; }
            
            .stats-strip { padding: 40px 20px; flex-wrap: wrap; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 25px; }
            .stat-num { font-size: 2.5rem; }
            .stat-label { font-size: 0.9rem; }
            
            .cta-section { padding: 60px 20px; }
            .cta-section h2 { font-size: 1.8rem; }
            .cta-btn { padding: 12px 35px; font-size: 1rem; }
        }

        @media (max-width: 480px) {
            .hero-title { font-size: 1.8rem; }
            .signature-text { font-size: 2rem; }
            .hero-gear-bg { width: 280px; height: 280px; }
            
            .value-card h3 { font-size: 1.4rem; }
            .value-card i { font-size: 2rem; }
            
            .feature-text h3 { font-size: 1.1rem; }
            .feature-icon-box { width: 50px; height: 50px; font-size: 1.4rem; }
            
            .expert-title { font-size: 1.3rem; }
            .expert-icon-box { width: 70px; height: 70px; }
            .expert-icon { font-size: 2rem; }
            
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 20px; }
            .stat-num { font-size: 2rem; }
            
            .section-header h2 { font-size: 1.6rem; }
            .section-header p { font-size: 1rem; }
        }
    </style>
<?php
$extraHead = ob_get_clean();

// --- Main Content ---
ob_start();
?>
    <!-- Page Loader -->
    <div class="page-loader" id="page-loader">
        <div class="loader-spinner"></div>
    </div>

    <!-- Background Orbs (Unified with other pages) -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <!-- A. HERO -->
    <header class="page-hero">
        <!-- Spinning Circle Background -->
        <div class="hero-gear-bg"></div>
        
        <div style="position: relative; z-index: 2;">
            <span class="hero-tag" data-aos="fade-down" style="
                font-family: 'Cairo', sans-serif; 
                font-size: 3.5rem; 
                font-weight: 900; 
                background: linear-gradient(to bottom, #fff 20%, var(--gold-1) 80%); 
                -webkit-background-clip: text; 
                -webkit-text-fill-color: transparent; 
                text-shadow: 0 0 30px rgba(212, 175, 55, 0.4);
                display: block;
                margin-bottom: 10px;
            ">المُوَافِي</span>
            <h1 class="hero-title" data-aos="zoom-in" data-aos-duration="1200" style="font-size: 3.5rem; line-height: 1.3; margin-bottom: 25px;">خِبْرَةٌ تَتَجَاوَزُ الـ 25 عَامًا</h1>
            <p style="font-size: 1.4rem; color: #cbd5e1; max-width: 800px; margin: 0 auto; line-height: 1.8;" data-aos="fade-up">
                جَوْدَةٌ يُعْتَمَدُ عَلَيْهَا، وَخِدْمَةُ مَا بَعْدَ الْبَيْعِ تُجَسِّدُ الِاسْتِمْرَارِيَّةَ.
            </p>
            
            <!-- Signature -->
            <div class="signature-wrapper" data-aos="zoom-in" data-aos-delay="300" style="margin-top: 60px; position: relative; display: inline-block; transform: translateX(-80px);">
                <div class="signature-text">
                    م / عماد الموافي
                </div>
                
                <!-- Tagline moved here -->
                <div style="
                    margin-top: 20px;
                    color: rgba(255,255,255,0.8);
                    font-size: 1.3rem;
                    letter-spacing: 2px;
                    font-family: 'Cairo', sans-serif;
                    transform: rotate(0deg);
                    font-weight: 600;
                ">اِسْمٌ لَهُ تَارِيخٌ</div>
            </div>
        </div>
    </header>

    <!-- C. PILLARS (Values) -->
    <section class="values-section">
        <div class="values-grid">
            <div class="value-card" data-aos="fade-up" data-aos-delay="0">
                <i class="fas fa-eye" style="font-size: 3rem; color: var(--gold-1); margin-bottom: 20px;"></i>
                <h3 style="color: #fff; font-size: 1.8rem; margin-bottom: 15px;">الرؤية</h3>
                <p style="color: #94a3b8; line-height: 1.6;"><?= $vision ?></p>
            </div>
            <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-bullseye" style="font-size: 3rem; color: var(--gold-1); margin-bottom: 20px;"></i>
                <h3 style="color: #fff; font-size: 1.8rem; margin-bottom: 15px;">الرسالة</h3>
                <p style="color: #94a3b8; line-height: 1.6;"><?= $mission ?></p>
            </div>
            <div class="value-card" data-aos="fade-up" data-aos-delay="400">
                <i class="fas fa-gem" style="font-size: 3rem; color: var(--gold-1); margin-bottom: 20px;"></i>
                <h3 style="color: #fff; font-size: 1.8rem; margin-bottom: 15px;">القيم</h3>
                <p style="color: #94a3b8; line-height: 1.6;"><?= $values ?></p>
            </div>
        </div>
    </section>

    <!-- B. WHY CHOOSE US (Features) -->
    <section class="features-section">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.5rem; color: #fff; margin-bottom: 15px;">لماذا تختار المُوَفِّي؟</h2>
            <p style="color: #94a3b8; max-width: 600px; margin: 0 auto;">معاييرنا لا تقبل المنافسة، لأننا نضع الجودة فوق كل اعتبار.</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-item" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-icon-box"><i class="fas fa-check-circle"></i></div>
                <div class="feature-text">
                    <h3>قطع غيار أصلية</h3>
                    <p>نضمن لك قطع غيار ريكو الأصلية 100% لعمر افتراضي أطول لأجهزتك.</p>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-item" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon-box"><i class="fas fa-shield-alt"></i></div>
                <div class="feature-text">
                    <h3>ضمان حقيقي</h3>
                    <p>سياسات ضمان واضحة وشاملة تمنحك راحة البال التامة بعد الصيانة.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="feature-item" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon-box"><i class="fas fa-bolt"></i></div>
                <div class="feature-text">
                    <h3>سرعة الإنجاز</h3>
                    <p>نقدر قيمة وقتك، لذا نلتزم بأسرع مدة تسليم مع الحفاظ على الجودة.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- E. EXPERTS (Clean & Organized) -->
    <section class="team-section">
        <div class="section-header">
            <h2>نخبة الخبراء</h2>
            <p>فريقنا هو سر قوتنا. مهندسون وفنيون على أعلى مستوى من الكفاءة.</p>
        </div>
        
        <div class="experts-grid">
            <!-- 1. Consultants -->
            <div class="expert-card" data-aos="fade-up" data-aos-delay="0">
                <div class="expert-icon-box">
                    <i class="fas fa-certificate expert-icon"></i>
                </div>
                <h3 class="expert-title">خبراء متخصصون</h3>
                <p class="expert-desc">استشارات فنية دقيقة لضمان<br>أفضل الحلول لأعمالك.</p>
            </div>
            
            <!-- 2. Engineers -->
            <div class="expert-card" data-aos="fade-up" data-aos-delay="150">
                <div class="expert-icon-box">
                    <i class="fas fa-microchip expert-icon"></i>
                </div>
                <h3 class="expert-title">نخبة المهندسين</h3>
                <p class="expert-desc">كفاءة هندسية عالية وتشخيص<br>احترافي للأعطال.</p>
            </div>

             <!-- 3. Technicians -->
             <div class="expert-card" data-aos="fade-up" data-aos-delay="300">
                <div class="expert-icon-box">
                    <i class="fas fa-tools expert-icon"></i>
                </div>
                <h3 class="expert-title">دعم فني محترف</h3>
                <p class="expert-desc">دقة في التنفيذ وصيانة دورية<br>لراحة بالك.</p>
            </div>
        </div>
    </section>

    <!-- F. CTA -->
    <section class="cta-section">
        <h2 style="font-size: 2.5rem; color: #fff; margin-bottom: 20px;">هل أنت مستعد لتغيير قواعد اللعبة؟</h2>
        <p style="color: #94a3b8; font-size: 1.2rem;">دعنا نتولى أمور الطباعة، لتركز أنت على تنمية أعمالك.</p>
        <a href="<?= BASE_URL ?>/contact" class="cta-btn">ابدأ الرحلة معنا</a>
    </section>
<?php
$content = ob_get_clean();

// --- Extra Scripts ---
ob_start();
?>
    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Hide page loader when content is ready
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.classList.add('hidden');
            }
        });

        AOS.init({ duration: 800, offset: 50 });
        
        // Counter Animation Logic
        const stats = document.querySelectorAll('.stat-num');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                   // Add counter logic here if needed, or keeping it static for stability
                }
            });
        });
    </script>
<?php
$extraScripts = ob_get_clean();

// Include Layout
include VIEWS_PATH . '/layouts/public_layout.php';
?>

<?php
/**
 * Demo: Premium Visual Identity
 * Advanced visual effects, 3D background, sophisticated animations
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
        ['name' => 'عقود الصيانة الشاملة', 'short_description' => 'نضمن لك استمرارية العمل بلا توقف'],
        ['name' => 'قطع الغيار الأصلية', 'short_description' => 'جودة يابانية معتمدة 100%'],
        ['name' => 'الدعم التقني الفوري', 'short_description' => 'استجابة في غضون ساعات'],
        ['name' => 'حلول الأرشفة الذكية', 'short_description' => 'رقمنة وأتمتة سير العمل'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | التميز الرقمي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Three.js & GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg: #0a0a0a;
            --surface: #141414;
            --primary: #00d4ff;
            --secondary: #7b2cbf;
            --gold: #ffd700;
            --white: #ffffff;
            --grey: #888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Tajawal', sans-serif;
            background: var(--bg);
            color: var(--white);
            overflow-x: hidden;
        }

        /* === LOADING SCREEN === */
        .loader {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: opacity 0.5s, visibility 0.5s;
        }
        .loader.hidden { opacity: 0; visibility: hidden; }
        
        .loader-logo {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 2rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 2s infinite;
        }
        
        .loader-bar {
            width: 200px; height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            overflow: hidden;
        }
        .loader-progress {
            width: 0; height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            animation: load 2s ease forwards;
        }
        
        @keyframes load { to { width: 100%; } }
        @keyframes shimmer { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.3); } }

        /* === 3D CANVAS === */
        #canvas-container {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            opacity: 0.4;
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
            mix-blend-mode: difference;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 45px; height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #000;
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        }

        .nav-links {
            display: flex;
            gap: 3rem;
        }
        .nav-link {
            color: var(--white);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
            transition: 0.3s;
        }
        .nav-link:hover { color: var(--primary); }

        .nav-cta {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #000;
            text-decoration: none;
            font-weight: 700;
            clip-path: polygon(10px 0%, 100% 0%, calc(100% - 10px) 100%, 0% 100%);
            transition: 0.3s;
        }
        .nav-cta:hover { transform: scale(1.05); filter: brightness(1.1); }

        /* === HERO === */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 150px 10% 100px;
            position: relative;
            z-index: 2;
        }

        .hero-content {
            max-width: 800px;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 1.5rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--primary);
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }

        .hero-title {
            font-size: clamp(3rem, 7vw, 6rem);
            font-weight: 900;
            line-height: 1;
            margin-bottom: 2rem;
        }

        .hero-title .line {
            display: block;
            overflow: hidden;
        }

        .hero-title .gradient-text {
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--gold), var(--primary));
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-flow 5s linear infinite;
        }

        @keyframes gradient-flow {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        .hero-desc {
            font-size: 1.3rem;
            color: var(--grey);
            line-height: 1.9;
            margin-bottom: 3rem;
            max-width: 600px;
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-glow {
            padding: 1.2rem 3rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #000;
            font-size: 1.1rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            position: relative;
            transition: 0.3s;
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
        }
        .btn-glow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 212, 255, 0.5);
        }

        .btn-outline {
            padding: 1.2rem 3rem;
            border: 2px solid rgba(255,255,255,0.2);
            color: var(--white);
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            backdrop-filter: blur(5px);
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0, 212, 255, 0.05);
        }

        /* === FLOATING ELEMENTS === */
        .floating-shapes {
            position: absolute;
            right: 10%;
            top: 50%;
            transform: translateY(-50%);
            width: 400px;
            height: 400px;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border: 1px solid rgba(255,255,255,0.1);
            animation: float 6s ease-in-out infinite;
        }
        .shape-1 { width: 300px; height: 300px; border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; animation-delay: 0s; }
        .shape-2 { width: 200px; height: 200px; top: 100px; left: 100px; border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation-delay: 1s; border-color: var(--primary); opacity: 0.3; }
        .shape-3 { width: 100px; height: 100px; top: 50px; left: 50px; border-radius: 50%; animation-delay: 2s; background: linear-gradient(135deg, var(--primary), var(--secondary)); opacity: 0.1; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* === STATS === */
        .stats-section {
            padding: 6rem 10%;
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3rem;
            background: linear-gradient(to bottom, transparent, rgba(20,20,20,0.8));
        }

        .stat-card {
            text-align: center;
            padding: 3rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            transition: 0.4s;
        }
        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 212, 255, 0.1);
        }

        .stat-value {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .stat-label {
            color: var(--grey);
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        /* === SERVICES === */
        .services-section {
            padding: 8rem 10%;
            position: relative;
            z-index: 2;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        .section-tag {
            color: var(--primary);
            font-size: 0.9rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 1rem;
            display: block;
        }
        .section-title {
            font-size: 3rem;
            font-weight: 900;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: var(--surface);
            padding: 3rem;
            position: relative;
            overflow: hidden;
            transition: 0.4s;
            border: 1px solid transparent;
        }
        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s;
        }
        .service-card:hover { border-color: rgba(255,255,255,0.1); transform: translateY(-5px); }
        .service-card:hover::before { transform: scaleX(1); }

        .service-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #000;
            margin-bottom: 2rem;
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
        }

        .service-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .service-desc {
            color: var(--grey);
            line-height: 1.7;
        }

        /* === FOOTER === */
        .footer {
            padding: 6rem 10% 3rem;
            position: relative;
            z-index: 2;
            border-top: 1px solid rgba(255,255,255,0.05);
            text-align: center;
        }

        .footer-cta {
            margin-bottom: 4rem;
        }
        .footer-cta h2 {
            font-size: 3rem;
            margin-bottom: 2rem;
        }

        .footer-bottom {
            color: var(--grey);
            font-size: 0.9rem;
        }

        /* === CURSOR === */
        .cursor {
            width: 20px; height: 20px;
            border: 2px solid var(--primary);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s, opacity 0.3s;
            mix-blend-mode: difference;
        }
        .cursor-dot {
            width: 5px; height: 5px;
            background: var(--primary);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
        }

        /* === RESPONSIVE === */
        @media (max-width: 1024px) {
            .floating-shapes { display: none; }
            .nav-links { display: none; }
            .stats-section { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hero { padding: 120px 5% 60px; }
        }
    </style>
</head>
<body>

    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="loader-logo"><?= htmlspecialchars($companyName) ?></div>
        <div class="loader-bar"><div class="loader-progress"></div></div>
    </div>

    <!-- Custom Cursor -->
    <div class="cursor" id="cursor"></div>
    <div class="cursor-dot" id="cursor-dot"></div>

    <!-- 3D Background -->
    <div id="canvas-container"></div>

    <!-- Navigation -->
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">
            <div class="logo-icon">M</div>
            <span><?= htmlspecialchars($companyName) ?></span>
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
                <i class="fas fa-certificate"></i>
                <span>الشريك الرسمي لـ RICOH</span>
            </div>
            <h1 class="hero-title">
                <span class="line">نحن لا نقدم</span>
                <span class="line"><span class="gradient-text">خدمات عادية</span></span>
            </h1>
            <p class="hero-desc">
                نحن نعيد تعريف تجربة الصيانة. تقنيات يابانية، فريق محترف، واستجابة برق. مرحباً بك في المستقبل.
            </p>
            <div class="hero-btns">
                <a href="<?= BASE_URL ?>/maintenance" class="btn-glow">
                    <i class="fas fa-rocket"></i> ابدأ التجربة
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn-outline">اكتشف المزيد</a>
            </div>
        </div>

        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <div class="stat-card">
            <div class="stat-value" data-target="<?= $stats['products'] ?>">0</div>
            <div>+</div>
            <div class="stat-label">منتج ريكو</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" data-target="<?= $stats['tickets'] ?>">0</div>
            <div>+</div>
            <div class="stat-label">عملية ناجحة</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" data-target="<?= $stats['years'] ?>">0</div>
            <div>+</div>
            <div class="stat-label">سنة خبرة</div>
        </div>
    </section>

    <!-- Services -->
    <section class="services-section">
        <div class="section-header">
            <span class="section-tag">خدماتنا</span>
            <h2 class="section-title">حلول متكاملة</h2>
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

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-cta">
            <h2>هل أنت مستعد؟</h2>
            <a href="<?= BASE_URL ?>/maintenance" class="btn-glow">
                <i class="fas fa-bolt"></i> ابدأ الآن
            </a>
        </div>
        <div class="footer-bottom">
            © <?= date('Y') ?> <?= htmlspecialchars($companyName) ?> — جميع الحقوق محفوظة
        </div>
    </footer>

    <script>
        // === LOADER ===
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
            }, 2000);
        });

        // === CUSTOM CURSOR ===
        const cursor = document.getElementById('cursor');
        const cursorDot = document.getElementById('cursor-dot');
        
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX - 10 + 'px';
            cursor.style.top = e.clientY - 10 + 'px';
            cursorDot.style.left = e.clientX - 2.5 + 'px';
            cursorDot.style.top = e.clientY - 2.5 + 'px';
        });

        // === THREE.JS BACKGROUND ===
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        // Particles
        const particlesGeometry = new THREE.BufferGeometry();
        const particlesCount = 3000;
        const posArray = new Float32Array(particlesCount * 3);

        for (let i = 0; i < particlesCount * 3; i++) {
            posArray[i] = (Math.random() - 0.5) * 15;
        }

        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

        const particlesMaterial = new THREE.PointsMaterial({
            size: 0.02,
            color: 0x00d4ff,
            transparent: true,
            opacity: 0.8,
            blending: THREE.AdditiveBlending
        });

        const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particlesMesh);

        camera.position.z = 3;

        // Animation
        const clock = new THREE.Clock();
        
        function animate() {
            const elapsedTime = clock.getElapsedTime();
            particlesMesh.rotation.y = elapsedTime * 0.03;
            particlesMesh.rotation.x = elapsedTime * 0.01;
            renderer.render(scene, camera);
            requestAnimationFrame(animate);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Mouse Parallax
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 0.5;
            const y = (e.clientY / window.innerHeight - 0.5) * 0.5;
            
            gsap.to(particlesMesh.rotation, {
                x: y * 0.3,
                y: x * 0.3,
                duration: 2
            });
        });

        // === GSAP ANIMATIONS ===
        gsap.registerPlugin(ScrollTrigger);

        // Hero Entrance
        gsap.from('.hero-badge', { opacity: 0, y: 30, duration: 1, delay: 2.2 });
        gsap.from('.hero-title .line', { opacity: 0, y: 50, duration: 1, stagger: 0.2, delay: 2.4 });
        gsap.from('.hero-desc', { opacity: 0, y: 30, duration: 1, delay: 2.8 });
        gsap.from('.hero-btns', { opacity: 0, y: 30, duration: 1, delay: 3 });

        // Stats Counter
        const counters = document.querySelectorAll('.stat-value');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            
            ScrollTrigger.create({
                trigger: counter,
                start: 'top 80%',
                onEnter: () => {
                    gsap.to(counter, {
                        innerText: target,
                        duration: 2,
                        snap: { innerText: 1 },
                        ease: 'power2.out'
                    });
                }
            });
        });

        // Service Cards
        gsap.from('.service-card', {
            opacity: 0,
            y: 50,
            duration: 0.8,
            stagger: 0.15,
            scrollTrigger: {
                trigger: '.services-grid',
                start: 'top 80%'
            }
        });
    </script>
</body>
</html>

<?php
/**
 * الصفحة الرئيسية - النموذج الثالث المحسن (V3.1 - The Network Core)
 * نظام المُوَفِّي - تصميم من الخيال (احترافي)
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المُوَفِّي | بُعد جديد للأعمال</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;400;700&family=IBM+Plex+Sans+Arabic:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg-deep: #030305;
            --accent-cyan: #00f3ff;
            --accent-purple: #bc13fe;
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
        }

        * {
            box-sizing: border-box;
            cursor: none;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-primary);
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            margin: 0;
            overflow-x: hidden;
            font-size: 16px;
        }

        /* --- Custom Cursor --- */
        .cursor-trail {
            position: fixed;
            top: 0; left: 0;
            width: 20px; height: 20px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 10000;
            transition: width 0.3s, height 0.3s, background 0.3s;
            mix-blend-mode: difference;
        }
        
        .cursor-dot {
            position: fixed;
            top: 0; left: 0;
            width: 4px; height: 4px;
            background: var(--accent-cyan);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 10001;
        }

        body:hover .cursor-trail { opacity: 1; }

        /* --- Background --- */
        #canvas-wrapper {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: linear-gradient(to bottom, #030305, #0a0a12);
        }
        
        #webgl-canvas {
            width: 100%; height: 100%;
            opacity: 0.7;
        }

        /* --- Header / Nav --- */
        .header-bar {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            padding: 2rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            mix-blend-mode: difference;
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .menu-trig {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            padding-bottom: 5px;
        }
        
        .menu-trig::after {
            content: '';
            position: absolute;
            bottom: 0; right: 0;
            width: 30%; height: 1px;
            background: #fff;
            transition: width 0.3s;
        }
        
        .menu-trig:hover::after { width: 100%; }

        /* --- Smooth Scroll Wrapper --- */
        .smooth-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        /* --- Hero Section --- */
        .hero-section {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 10%;
            position: relative;
        }

        .hero-label {
            color: var(--accent-cyan);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: 'Outfit', sans-serif;
            opacity: 0;
            animation: fadeIn 1s 0.5s forwards;
        }

        .hero-headline {
            font-size: 6vw;
            font-weight: 300;
            line-height: 1.1;
            margin: 0;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0;
            animation: slideUp 1s 0.8s forwards;
        }
        
        .hero-headline strong {
            font-weight: 700;
            color: #fff;
        }

        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes slideUp { 
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .scroll-hint {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            opacity: 0.5;
            animation: pulseHint 2s infinite;
        }
        
        .mouse-icon {
            width: 20px; height: 32px;
            border: 1px solid #fff;
            border-radius: 12px;
            position: relative;
        }
        .mouse-icon::before {
            content: '';
            position: absolute;
            top: 6px; left: 50%;
            transform: translateX(-50%);
            width: 2px; height: 6px;
            background: #fff;
            border-radius: 2px;
        }

        @keyframes pulseHint { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.8; } }

        /* --- Horizontal Scroller --- */
        .h-scroll-container {
            width: 400%; /* 4 Sections wide */
            height: 100vh;
            display: flex;
            flex-wrap: nowrap;
        }

        .panel {
            width: 100vw;
            height: 100vh;
            padding: 5% 10%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid rgba(255,255,255,0.03);
            position: relative;
        }
        
        .panel-number {
            position: absolute;
            top: 10%;
            right: 5%;
            font-size: 15vw;
            font-weight: 900;
            color: rgba(255,255,255,0.02);
            font-family: 'Outfit', sans-serif;
            line-height: 1;
            pointer-events: none;
        }

        .panel-content {
            z-index: 2;
            max-width: 800px;
        }

        /* --- Cards System --- */
        .glass-cards-row {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
            perspective: 1000px;
        }

        .glass-card {
            flex: 1;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            backdrop-filter: blur(8px);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 3px; height: 0;
            background: var(--accent-cyan);
            transition: height 0.4s ease;
        }
        
        .glass-card:hover {
            background: rgba(255,255,255,0.05);
            transform: translateY(-10px) rotateX(2deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .glass-card:hover::before { height: 100%; }

        .card-icon {
            font-size: 2rem;
            color: var(--accent-cyan);
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .card-desc {
            color: var(--text-secondary);
            line-height: 1.8;
            font-weight: 300;
        }

        /* --- Process Steps --- */
        .step-list {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .step-num {
            font-family: 'Outfit', sans-serif;
            color: var(--accent-purple);
            font-weight: 700;
        }

        /* --- CTA Section --- */
        .cta-box {
            text-align: center;
            position: relative;
        }
        
        .giant-cta-link {
            font-size: 8vw;
            font-weight: 900;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255,255,255,0.5);
            text-decoration: none;
            transition: all 0.5s;
            position: relative;
            display: inline-block;
        }
        
        .giant-cta-link::before {
            content: 'ابدأ الآن';
            position: absolute;
            top: 0; left: 0;
            width: 0;
            height: 100%;
            color: #fff;
            -webkit-text-stroke: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: width 0.5s cubic-bezier(0.19, 1, 0.22, 1);
            border-right: 2px solid var(--accent-cyan);
        }
        
        .giant-cta-link:hover::before { width: 100%; }

    </style>
</head>
<body>

    <!-- Cursor -->
    <div class="cursor-trail"></div>
    <div class="cursor-dot"></div>

    <!-- WebGL -->
    <div id="canvas-wrapper">
        <canvas id="webgl-canvas"></canvas>
    </div>

    <!-- Interface -->
    <header class="header-bar">
        <div class="brand-logo">AL-MUWAFI</div>
        <div class="menu-trig">القائمة</div>
    </header>

    <!-- Scroll Content -->
    <div class="smooth-wrapper">
        <!-- Vertical Scroll for Hero -->
        <section class="hero-section">
            <div class="hero-label">01 / المقدمة</div>
            <h1 class="hero-headline">
                التميز التقني <br>
                <strong>بمفهوم آخر.</strong>
            </h1>
            <div class="scroll-hint">
                <div class="mouse-icon"></div>
                <span style="font-size: 0.7rem; letter-spacing: 2px;">SCROLL</span>
            </div>
        </section>

        <!-- Horizontal Scroll Trigger -->
        <div class="h-scroll-wrapper" style="width: 100%; height: 100vh; overflow: hidden;">
            <div class="h-scroll-container">
                
                <!-- Panel 1: Concept -->
                <div class="panel">
                    <span class="panel-number">01</span>
                    <div class="panel-content">
                        <h2 style="font-size: 3rem; margin-bottom: 2rem;">أكثر من مجرد صيانة</h2>
                        <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px;">
                            نحن نبني منظومة رقمية متكاملة لمكتبك. ندمج بين أحدث أجهزة ريكو اليابانية وبين خبرة تقنية تمتد لسنوات، لنضمن لك استمرارية لا تتوقف.
                        </p>
                    </div>
                </div>

                <!-- Panel 2: Services -->
                <div class="panel">
                    <span class="panel-number">02</span>
                    <div class="panel-content" style="width: 100%;">
                        <h2 style="font-size: 2.5rem; margin-bottom: 3rem;">خدمات النخبة</h2>
                        <div class="glass-cards-row">
                            <div class="glass-card">
                                <div class="card-icon"><i class="fas fa-layer-group"></i></div>
                                <h3 class="card-title">تكامل الأنظمة</h3>
                                <p class="card-desc">ربط أجهزة الطباعة بالبنية السحابية لمؤسستك بسلاسة تامة وأمان عالي.</p>
                            </div>
                            <div class="glass-card">
                                <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
                                <h3 class="card-title">الصيانة الوقائية</h3>
                                <p class="card-desc">لا ننتظر العطل. أنظمتنا تتنبأ بالحاجة للصيانة قبل أن يتوقف العمل.</p>
                            </div>
                            <div class="glass-card">
                                <div class="card-icon"><i class="fas fa-microchip"></i></div>
                                <h3 class="card-title">حلول ذكية</h3>
                                <p class="card-desc">أتمتة طلبات الأحبار وقطع الغيار عبر خوارزميات ذكية.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Process -->
                <div class="panel">
                    <span class="panel-number">03</span>
                    <div class="panel-content">
                        <h2 style="font-size: 2.5rem; margin-bottom: 2rem;">رحلة العميل</h2>
                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-num">01</span>
                                <div>
                                    <h4 style="margin: 0 0 5px 0;">التحليل الشامل</h4>
                                    <span style="color: #64748b; font-size: 0.9rem;">دراسة احتياجات مكتبك بدقة متناهية.</span>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-num">02</span>
                                <div>
                                    <h4 style="margin: 0 0 5px 0;">التصميم والتنفيذ</h4>
                                    <span style="color: #64748b; font-size: 0.9rem;">تركيب الأجهزة والأنظمة بأعلى معايير الجودة.</span>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-num">03</span>
                                <div>
                                    <h4 style="margin: 0 0 5px 0;">الدعم المستمر</h4>
                                    <span style="color: #64748b; font-size: 0.9rem;">مراقبة على مدار الساعة واستجابة فورية.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Panel 4: Footer -->
                <div class="panel" style="justify-content: center; align-items: center; text-align: center;">
                    <div class="cta-box">
                        <p style="letter-spacing: 3px; color: var(--text-secondary); margin-bottom: 1rem; text-transform: uppercase;">Ready to upgrade?</p>
                        <a href="<?= BASE_URL ?>/maintenance" class="giant-cta-link">ابدأ الآن</a>
                    </div>
                    
                    <div style="margin-top: 5rem; display: flex; gap: 3rem; opacity: 0.5;">
                        <a href="#" style="color: #fff;">LINKEDIN</a>
                        <a href="#" style="color: #fff;">TWITTER</a>
                        <a href="#" style="color: #fff;">INSTAGRAM</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Logic -->
    <script>
        // --- 1. Custom Cursor Logic ---
        const trail = document.querySelector('.cursor-trail');
        const dot = document.querySelector('.cursor-dot');
        
        let mouseX = 0, mouseY = 0;
        let trailX = 0, trailY = 0;
        
        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            
            dot.style.left = mouseX + 'px';
            dot.style.top = mouseY + 'px';
        });
        
        function animateCursor() {
            trailX += (mouseX - trailX) * 0.15;
            trailY += (mouseY - trailY) * 0.15;
            
            trail.style.left = trailX + 'px';
            trail.style.top = trailY + 'px';
            
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        // Hover effects for cursor
        document.querySelectorAll('a, .menu-trig, .glass-card').forEach(el => {
            el.addEventListener('mouseenter', () => {
                trail.style.width = '50px';
                trail.style.height = '50px';
                trail.style.background = 'rgba(255,255,255,0.1)';
            });
            el.addEventListener('mouseleave', () => {
                trail.style.width = '20px';
                trail.style.height = '20px';
                trail.style.background = 'transparent';
            });
        });

        // --- 2. GSAP ScrollTrigger ---
        gsap.registerPlugin(ScrollTrigger);
        
        const hContainer = document.querySelector('.h-scroll-container');
        
        gsap.to(hContainer, {
            x: () => -(hContainer.scrollWidth - window.innerWidth),
            ease: "none",
            scrollTrigger: {
                trigger: ".h-scroll-wrapper",
                pin: true,
                scrub: 0.5, // Smoother scrubbing
                end: () => "+=" + hContainer.scrollWidth
            }
        });
        
        // Reveal animations for panels
        gsap.utils.toArray('.panel-content').forEach((content, i) => {
            if (i === 0) return; // Skip first panel
            gsap.from(content, {
                y: 50,
                opacity: 0,
                duration: 1,
                scrollTrigger: {
                    trigger: content.parentElement,
                    containerAnimation: gsap.getById("hScroll"), // Requires ID if used this way, simpler:
                    start: "left center",
                    toggleActions: "play reverse play reverse"
                }
            });
        });

        // --- 3. Three.js Particle Network ---
        const canvas = document.querySelector('#webgl-canvas');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)); // optimize performance

        // Particles
        const particlesGeometry = new THREE.BufferGeometry();
        const count = 1500;
        const posArray = new Float32Array(count * 3);
        
        for(let i = 0; i < count * 3; i++) {
            posArray[i] = (Math.random() - 0.5) * 15; // Wider spread
        }
        
        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
        
        const material = new THREE.PointsMaterial({
            size: 0.015,
            color: 0x00f3ff, // Cyan
            transparent: true,
            opacity: 0.5,
        });
        
        const particlesMesh = new THREE.Points(particlesGeometry, material);
        scene.add(particlesMesh);

        // Connecting Lines (Simplified for performance)
        // Note: Real-time lines are heavy. We'll stick to particles with slight movement for "smooth" feel.
        
        camera.position.z = 2;

        const clock = new THREE.Clock();
        
        function tick() {
            const time = clock.getElapsedTime();
            
            // Slow rotation
            particlesMesh.rotation.y = time * 0.03;
            particlesMesh.rotation.x = time * 0.01;
            
            // Gentle wave
            // particlesMesh.position.y = Math.sin(time * 0.5) * 0.05;

            renderer.render(scene, camera);
            requestAnimationFrame(tick);
        }
        tick();

        // Parallax Mouse
        window.addEventListener('mousemove', (e) => {
             const x = (e.clientX / window.innerWidth) - 0.5;
             const y = (e.clientY / window.innerHeight) - 0.5;
             
             gsap.to(particlesMesh.rotation, {
                 x: y * 0.2, // Subtle tilt
                 y: x * 0.2,
                 duration: 2
             });
        });

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

    </script>
</body>
</html>

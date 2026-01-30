<?php
/**
 * Products Page - Cinematic Showcase Demo (v2)
 * Advanced 3D Transitions + Full Navigation
 */
use App\Services\Settings;

$companyInfo = Settings::getCompanyInfo();
$companyName = $companyInfo['name'] ?? 'المُوَفِّي';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المعرض السينمائي | <?= htmlspecialchars($companyName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        :root {
            --bg-deep: #050505;
            --accent: #0ea5e9;
            --gold: #d4af37;
            --text-w: #ffffff;
            --text-g: #888888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-deep);
            color: var(--text-w);
            font-family: 'Cairo', sans-serif;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
        }

        /* === LOADER === */
        .loader {
            position: fixed; inset: 0; background: #000; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
        }
        .bar { width: 0%; height: 2px; background: var(--accent); transition: width 0.5s; }

        /* === NAV === */
        .nav {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: 95%; max-width: 1400px;
            display: flex; justify-content: space-between; align-items: center; z-index: 100;
            padding: 15px 30px; border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: 0.3s;
        }
        .nav:hover { background: rgba(255, 255, 255, 0.08); }

        .logo { font-size: 1.4rem; font-weight: 800; letter-spacing: -1px; color: #fff; text-decoration: none; }
        .logo span { color: var(--accent); }

        .nav-links { display: flex; gap: 30px; }
        .nav-link {
            color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.95rem; font-weight: 500;
            transition: 0.3s; position: relative;
        }
        .nav-link.active, .nav-link:hover { color: #fff; }
        .nav-link::after {
            content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px;
            background: var(--accent); transition: 0.3s;
        }
        .nav-link.active::after, .nav-link:hover::after { width: 100%; }

        .nav-cta {
            padding: 10px 25px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            color: #fff; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 0.9rem;
            transition: 0.3s; display: flex; align-items: center; gap: 8px;
        }
        .nav-cta:hover { background: var(--accent); border-color: var(--accent); color: #000; box-shadow: 0 0 20px var(--neon-glow); }

        /* === SLIDER CONTAINER === */
        .slider-container {
            position: relative; width: 100%; height: 100%;
            perspective: 1500px; /* Enable 3D depth */
            overflow: hidden;
        }

        .slide {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transform-style: preserve-3d; /* Important for 3D anims */
        }
        .slide.active { opacity: 1; pointer-events: all; }

        /* === BACKGROUND TYPOGRAPHY === */
        .bg-text {
            position: absolute;
            top: 50%; left: 50%; transform: translate(-50%, -50%);
            font-family: 'Oswald', sans-serif;
            font-size: 25vw;
            color: rgba(255,255,255,0.03);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }

        /* === PRODUCT VISUAL (CENTER) === */
        .product-visual {
            position: relative;
            z-index: 2;
            width: 45vw; /* Large Hero Image */
            height: 45vw;
            display: flex; align-items: center; justify-content: center;
        }
        
        .product-img {
            max-width: 100%; max-height: 100%;
            filter: drop-shadow(0 30px 60px rgba(0,0,0,0.8));
            transform-origin: center bottom;
        }

        /* === INFO SECTION (LEFT/RIGHT) === */
        .info-panel {
            position: absolute;
            top: 50%; right: 10%; transform: translateY(-50%);
            width: 300px;
            z-index: 10;
            text-align: right;
        }

        .category-label {
            color: var(--accent); font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase;
            margin-bottom: 10px; display: block;
        }

        .product-title {
            font-size: 3rem; font-weight: 900; line-height: 1.1; margin-bottom: 20px;
            text-transform: uppercase;
        }

        .product-desc {
            color: var(--text-g); font-size: 0.95rem; line-height: 1.6; margin-bottom: 30px;
        }

        .btn-explore {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 12px 30px; border: 1px solid rgba(255,255,255,0.2);
            color: #fff; text-decoration: none; border-radius: 50px;
            transition: 0.3s;
        }
        .btn-explore:hover { background: #fff; color: #000; }

        /* === SPECS HUD (LEFT) === */
        .specs-hud {
            position: absolute;
            top: 50%; left: 10%; transform: translateY(-50%);
            z-index: 10;
        }
        
        .stat-box {
            margin-bottom: 30px;
            position: relative;
            padding-left: 20px;
            border-left: 2px solid rgba(255,255,255,0.1);
        }
        .stat-box.highlight { border-left-color: var(--gold); }
        
        .stat-val { font-size: 2.5rem; font-weight: 700; font-family: 'Oswald', sans-serif; line-height: 1; }
        .stat-label { font-size: 0.8rem; color: var(--text-g); letter-spacing: 1px; text-transform: uppercase; }

        /* === CONTROLS === */
        .controls {
            position: absolute; bottom: 40px; right: 10%;
            display: flex; gap: 20px; z-index: 20;
        }
        .ctrl-btn {
            width: 60px; height: 60px; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.2);
            background: transparent; color: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        .ctrl-btn:hover { background: #fff; color: #000; transform: scale(1.1); }

        /* === PAGINATION dots === */
        .pagination {
            position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 10px; z-index: 20;
        }
        .dot {
            width: 50px; height: 4px; background: rgba(255,255,255,0.2); cursor: pointer; transition: 0.3s;
        }
        .dot.active { background: var(--accent); width: 80px; }

        /* === ORNAMENTS === */
        .grid-lines {
            position: fixed; inset: 0; pointer-events: none; z-index: 1;
            background: 
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 100px 100px;
            mask-image: radial-gradient(circle, #000 0%, transparent 80%);
        }

    </style>
</head>
<body>

    <div class="loader"></div>

    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">RICOH<span>PRO</span></a>
        
        <div class="nav-links">
            <a href="<?= BASE_URL ?>/products" class="nav-link active">المنتجات</a>
            <a href="<?= BASE_URL ?>/services" class="nav-link">الخدمات</a>
            <a href="<?= BASE_URL ?>/spare-parts" class="nav-link">قطع الغيار</a>
            <a href="<?= BASE_URL ?>/contact" class="nav-link">تواصل معنا</a>
        </div>

        <a href="<?= BASE_URL ?>/maintenance" class="nav-cta">
            <i class="fas fa-user-circle"></i> منطقة العملاء
        </a>
    </nav>

    <div class="grid-lines"></div>

    <div class="slider-container">
        <?php foreach ($products as $i => $item): 
            $specs = [
                ['val' => '45', 'label' => 'PPM / SPEED'],
                ['val' => '1200', 'label' => 'DPI / RES'],
                ['val' => 'A3', 'label' => 'FORMAT']
            ];
            
            $imgUrl = "https://placehold.co/800x800/png?text=RICOH+SERIES+" . ($i+1);
            if (!empty($item['image']) && $item['image'] != 'default_product.png') {
                 $imgUrl = BASE_URL . '/storage/uploads/products/' . $item['image'];
            }
        ?>
        <div class="slide <?= $i===0?'active':'' ?>" data-id="<?= $i ?>">
            <div class="bg-text"><?= explode(' ', $item['name'])[1] ?? 'RICOH' ?></div>
            
            <div class="specs-hud">
                <?php foreach($specs as $si => $spec): ?>
                <div class="stat-box <?= $si===0?'highlight':'' ?>">
                    <div class="stat-val"><?= $spec['val'] ?></div>
                    <div class="stat-label"><?= $spec['label'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="product-visual">
                <img src="<?= $imgUrl ?>" class="product-img">
            </div>

            <div class="info-panel">
                <span class="category-label"><?= htmlspecialchars($item['category_name'] ?? 'Premium Series') ?></span>
                <h1 class="product-title"><?= htmlspecialchars($item['name']) ?></h1>
                <p class="product-desc">
                    <?= htmlspecialchars(mb_substr(strip_tags($item['description'] ?? 'قم بتجربة أداء لا مثيل له مع تقنيات ريكو المتقدمة التي تجمع بين السرعة والجودة.'), 0, 150)) ?>...
                </p>
                <a href="<?= BASE_URL ?>/products/show/<?= $item['slug'] ?>" class="btn-explore">
                    استكشف المزيد <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="controls">
        <button class="ctrl-btn prev"><i class="fas fa-chevron-right"></i></button>
        <button class="ctrl-btn next"><i class="fas fa-chevron-left"></i></button>
    </div>

    <div class="pagination">
        <?php foreach($products as $i => $p): ?>
        <div class="dot <?= $i===0?'active':'' ?>" data-id="<?= $i ?>"></div>
        <?php endforeach; ?>
    </div>

    <script>
        // Init
        gsap.to('.loader', { opacity: 0, pointerEvents:'none', duration: 1 });

        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        let current = 0;
        let isAnimating = false;
        const total = slides.length;

        function gotoSlide(index, direction = 'next') {
            if (isAnimating || index === current) return;
            isAnimating = true;

            const nextSlide = slides[index];
            const currSlide = slides[current];
            const nextImg = nextSlide.querySelector('.product-img');
            const currImg = currSlide.querySelector('.product-img');
            const nextText = nextSlide.querySelector('.bg-text');
            const nextInfo = nextSlide.querySelector('.info-panel');
            const nextHud = nextSlide.querySelector('.specs-hud');

            // 1. Setup Next Slide (Start from background)
            gsap.set(nextSlide, { opacity: 1, zIndex: 2 });
            gsap.set(currSlide, { zIndex: 1 });
            
            // Initial state for Incoming
            gsap.fromTo(nextSlide, 
                { z: 500, opacity: 0, rotationX: direction === 'next' ? 10 : -10 },
                { z: 0, opacity: 1, rotationX: 0, duration: 1.5, ease: "power3.out" }
            );

            // 2. Animate Outgoing (Fade into depth)
            gsap.to(currSlide, { 
                z: -500, 
                opacity: 0, 
                rotationX: direction === 'next' ? -5 : 5, 
                scale: 0.8,
                filter: "blur(10px)",
                duration: 1.2, 
                ease: "power2.inOut" 
            });

            // 3. Elements Stagger (Visual pop)
            gsap.fromTo(nextImg, 
                { scale: 1.5, opacity: 0 },
                { scale: 1, opacity: 1, duration: 1.5, ease: "expo.out", delay: 0.2 }
            );

            // 4. Text/HUD Entrance (Cinematic Sweep)
            gsap.fromTo([nextText, nextInfo], 
                { x: direction === 'next' ? 50 : -50, opacity: 0 },
                { x: 0, opacity: 1, duration: 1, stagger: 0.1, delay: 0.4, ease: "power2.out" }
            );
            
            // HUD Stats "Count/Scramble" effect
            gsap.fromTo(nextHud.querySelectorAll('.stat-val'), 
                { textContent: "000", opacity: 0 },
                { 
                    opacity: 1, 
                    duration: 1.5, 
                    delay: 0.5, 
                    snap: { textContent: 1 }, 
                    stagger: 0.2 
                }
            );

            // Cleanup
            setTimeout(() => {
                currSlide.classList.remove('active');
                gsap.set(currSlide, { clearProps: "all" }); // Reset props
                nextSlide.classList.add('active');
                current = index;
                isAnimating = false;
                updateDots();
            }, 1500);
        }

        function updateDots() {
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
        }

        // Events
        document.querySelector('.next').onclick = () => gotoSlide((current + 1) % total, 'next');
        document.querySelector('.prev').onclick = () => gotoSlide((current - 1 + total) % total, 'prev');
        
        dots.forEach(dot => {
            dot.onclick = () => {
                const id = parseInt(dot.dataset.id);
                gotoSlide(id, id > current ? 'next' : 'prev');
            }
        });

        // Intro Anim
        const activeS = slides[0];
        gsap.from(activeS.querySelector('.product-img'), { y: 100, opacity: 0, duration: 1.5, ease: "power3.out" });
        gsap.from(activeS.querySelector('.info-panel'), { x: 50, opacity: 0, delay: 0.5, duration: 1 });

    </script>
</body>
</html>

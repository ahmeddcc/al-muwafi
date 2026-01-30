<?php
/**
 * Products Page - New Global Identity - Executive Platform Edition
 * The "Executive Platform" design features a Split Hero layout with a Glass Floor.
 * 
 * Data is passed from ProductsController::index()
 */
use App\Services\Database;
use App\Services\Settings;

$companyName = 'المُوَفِّي';
try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
} catch (\Throwable $e) { 
    $companyInfo = [];
    // Database connection might have failed, we will handle this in sub-logic
}

// Ensure $products is defined to prevent errors if loaded directly (though it shouldn't be)
$products = $products ?? [];

$title = 'المنتجات | ' . htmlspecialchars($companyName);
$currentPage = 'products';

// --- Extra CSS ---
ob_start();
?>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Oswald:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    /* =========================================
        1. CORE IDENTITY STYLES (From home.php)
        ========================================= */
    :root {
        --bg-deep: #020617;
        --bg-dark: #0f172a;
        --bg-glass: rgba(30, 41, 59, 0.6);
        --glass-border: rgba(255, 255, 255, 0.08);
        --neon-blue: #0ea5e9;
        --neon-glow: rgba(14, 165, 233, 0.3);
        /* Gold Gradient - Refined for "Premium" look */
        --gold-1: #D4AF37;
        --gold-2: #F1D87E;
        --gold-3: #BF953F;
        --gold-4: #FCF6BA;
        --gold-5: #aa771c; /* Added missing variable */
        --gold-glow: rgba(212, 175, 55, 0.4);
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html { scroll-behavior: smooth; }
    
    /* Loader */
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
        font-family: 'Cairo', sans-serif;
        background-color: var(--bg-deep);
        color: var(--text-main);
        overflow-x: hidden;
        min-height: 100vh;
    }
    
    /* Background Orbs */
    .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
    .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
    .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
    .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
    @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }



    /* =========================================
        2. CINEMATIC SLIDER STYLES
        ========================================= */
    .page-wrapper {
        /* Standard layout structure */
        display: flex; flex-direction: column; min-height: 100vh;
    }
    
    /* The Slider Section - SPLIT LAYOUT */
    #cinematic-showcase {
        position: relative; width: 100%; height: 85vh; /* Increased to fit lowered content */
        padding-top: 80px; 
        overflow: hidden; display: flex; align-items: center; z-index: 1;
    }

    .c-slider-container {
        position: absolute; inset: 0; width: 100%; height: 100%;
        perspective: 1500px; overflow: hidden;
    }

    .c-slide {
        position: absolute; inset: 0;
        display: flex; flex-direction: row-reverse; /* Fix: Image Left, Content Right */
        align-items: center; justify-content: space-between;
        opacity: 0; pointer-events: none;
        padding: 0 5%;
        will-change: opacity, transform; /* Performance Hint */
    }
    .c-slide.active { opacity: 1; pointer-events: all; }

    /* Typography Background - Above Machine Area */
    .c-bg-text {
        position: absolute; 
        top: 17%; /* Raised to clear machine */
        left: 28%;
        transform: translateX(-50%);
        font-size: 5vw; 
        font-family: 'Oswald', sans-serif; 
        color: rgba(255,255,255,0.05);
        z-index: 2; /* Increased Z-index to be interactive */
        line-height: 1; 
        cursor: default;
        text-transform: uppercase;
        white-space: nowrap;
        letter-spacing: 0.1em;
        transition: all 0.4s ease;
    }
    .c-bg-text:hover {
        color: rgba(14, 165, 233, 0.3); /* Neon Blue Hint */
        text-shadow: 0 0 30px rgba(14, 165, 233, 0.6), 0 0 60px rgba(14, 165, 233, 0.4);
        transform: translateX(-50%) scale(1.05);
    }

    /* LEFT SIDE: The Platform */
    .c-stage-left {
        flex: 1; height: 100%; position: relative;
        display: flex; align-items: center; justify-content: center;
        perspective: 1200px; /* For 3D floor */
    }

    .c-plat-container {
        position: relative; width: 100%; max-width: 600px;
        display: flex; flex-direction: column; align-items: center;
        transform-style: preserve-3d;
        margin-top: 350px; /* Shifted adjust down again */
    }

    .c-product-visual {
        /* No container background anymore, just the floating machine */
        width: 100%; height: auto; max-height: 48vh; 
        display: flex; align-items: flex-end; justify-content: center;
        position: relative; z-index: 10;
        filter: drop-shadow(0 30px 60px rgba(0,0,0,0.6));
        transition: transform 0.5s ease;
        transform: translateY(85px); /* Centered deeper into the circle */
    }
    /* Hover: Float up slightly */
    .c-product-visual:hover { transform: translateY(20px); }

    .c-product-img {
        width: 100%; height: auto; object-fit: contain;
        /* Enhance contrast for "cutout" look */
    }

    /* The Glass Floor */
    .c-glass-floor {
        width: 140%; height: 350px;
        background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.01) 50%, transparent 80%);
        border-top: 1px solid rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: rotateX(75deg);
        margin-top: -260px;
        box-shadow: 
            0 0 80px rgba(14, 165, 233, 0.15),
            inset 0 20px 50px rgba(255,255,255,0.05);
        position: relative; z-index: 5;
        opacity: 0.9;
        transition: all 0.5s ease; /* Smooth transition for hover */
    }
    
    /* Golden Glow on Hover */
    .c-plat-container:hover .c-glass-floor {
        box-shadow: 
            0 0 100px rgba(212, 175, 55, 0.4), /* Strong Golden Glow */
            inset 0 20px 80px rgba(212, 175, 55, 0.2);
        border-top-color: rgba(212, 175, 55, 0.8); /* Golden Border */
    }

    /* Professional Gold Dust */
    .c-particle {
        position: absolute;
        left: 50%; top: 90%;
        width: 4px; height: 4px; /* Tiny Dust */
        background: var(--gold-1);
        box-shadow: 0 0 15px 2px var(--gold-1); /* Soft luxurious glow */
        border-radius: 50%;
        pointer-events: none;
        opacity: 0;
        z-index: 7; /* Behind machine */
        transform: translate(-50%, -50%);
    }
    
    /* Floor Reflection simulation */
    .c-reflection {
        position: absolute; top: 100%; left: 0; width: 100%; height: 100%;
        transform: scaleY(-1); opacity: 0.2; filter: blur(5px);
        mask-image: linear-gradient(transparent, black);
        pointer-events: none; z-index: 4;
    }

    /* RIGHT STAGE: Content (Redesigned) */
    .c-stage-right {
        flex: 1; height: 100%;
        display: flex; flex-direction: column; justify-content: center; align-items: flex-start;
        padding-right: 50px; text-align: right; /* RTL */
        position: relative; z-index: 20; direction: rtl;
        padding-top: 320px; /* Shifted adjust down again */
    }

    .c-category { 
        color: var(--gold-1); font-size: 0.85rem; letter-spacing: 1px; font-weight: 600;
        text-transform: uppercase; margin-bottom: 15px;
        display: inline-block; padding: 5px 15px; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 20px;
        background: rgba(212, 175, 55, 0.05);
    }

    .c-title { 
        font-size: 3.5rem; font-weight: 700; line-height: 1.1; margin-bottom: 25px; 
        color: #fff;
        text-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }
    
    .c-desc {
        font-size: 1rem; line-height: 1.7; color: #cbd5e1; max-width: 550px; margin-bottom: 40px;
        border-right: 3px solid var(--gold-1); padding-right: 20px; /* Quote style */
    }

    /* NEW SPECS TABLE */
    .c-specs-modern {
        display: flex; gap: 30px; margin-bottom: 45px; width: 100%;
    }
    .c-spec-item {
        display: flex; flex-direction: column; gap: 8px; position: relative;
    }
    .c-spec-item::after { 
        content: ''; position: absolute; top: 50%; left: -15px; width: 1px; height: 30px; 
        background: rgba(255,255,255,0.1); transform: translateY(-50%);
    }
    .c-spec-item:last-child::after { display: none; }
    
    .c-spec-label { font-size: 0.75rem; color: var(--gold-3); text-transform: uppercase; letter-spacing: 1px; }
    .c-spec-val { font-size: 1.25rem; font-weight: 600; color: #fff; font-family: 'Oswald', sans-serif; }

    .c-btn-modern {
        position: relative;
        background: transparent; color: #fff; padding: 15px 40px; font-weight: 600;
        text-decoration: none; overflow: hidden; border: 1px solid rgba(255,255,255,0.2);
        display: inline-flex; align-items: center; gap: 10px; transition: 0.4s;
    }
    .c-btn-modern::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: var(--gold-1); transform: scaleX(0); transform-origin: right;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: -1;
    }
    .c-btn-modern:hover::before { transform: scaleX(1); }
    .c-btn-modern:hover { border-color: var(--gold-1); color: #000; }
    .c-btn-modern i { transition: 0.3s; }
    .c-btn-modern:hover i { transform: translateX(-5px); }

    /* Controls Modern */
    .c-controls { 
        position: absolute; bottom: 5px; left: 50px; /* Moved controls closer to bottom */
        display: flex; gap: 15px; z-index: 50;
    }
    .c-ctrl-btn { 
        width: 50px; height: 50px; 
        border: 1px solid rgba(255,255,255,0.2); 
        background: rgba(255,255,255,0.05); 
        color: #fff; cursor: pointer; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(5px);
    }
    .c-ctrl-btn:hover { 
        background: var(--gold-1); 
        border-color: var(--gold-1); 
        color: #000; 
        transform: scale(1.1); 
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
    }
    .c-ctrl-btn i { font-size: 1.1rem; }


    @media (max-width: 1024px) {
        .c-slide { flex-direction: column-reverse; /* Stack: Content below, Image above */ padding: 80px 20px 20px; overflow-y: auto; align-items: flex-start; }
        .c-stage-left { width: 100%; height: 45%; flex: none; align-items: flex-end; }
        .c-product-visual { max-height: 40vh; transform: translateY(0); filter: drop-shadow(0 15px 30px rgba(0,0,0,0.5)); }
        .c-plat-container { margin-bottom: 2rem; transform: scale(0.9); }
        .c-stage-right { width: 100%; height: auto; flex: none; padding-right: 0; text-align: center; align-items: center; padding-bottom: 100px; /* Space for controls */ }
        .c-desc { border-right: none; padding-right: 0; max-width: 100%; font-size: 0.95rem; }
        .c-specs-modern { justify-content: center; width: 100%; flex-wrap: wrap; gap: 15px; }
        .c-spec-item::after { display: none; }
        .c-spec-item { background: rgba(255,255,255,0.05); padding: 10px 15px; border-radius: 10px; width: 30%; }
        .c-controls { left: 50%; transform: translateX(-50%); bottom: 20px; width: auto; }
        .c-stat-box { min-width: 120px; height: 70px; padding-left: 15px; } 
        .c-title { font-size: 2.2rem; }
        .c-bg-text { font-size: 30vw; top: 15%; opacity: 0.02; }
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

    <!-- Background Orbs & Grid -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>
    <div class="bg-grid"></div>

    <!-- Main Content Wrapper -->
    <div class="page-wrapper">
        
        <!-- Cinematic Slider Section -->
        <section id="cinematic-showcase">
            <div class="c-slider-container">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: var(--neon-blue); margin-bottom: 20px; opacity: 0.5;"></i>
                    <h2>لا توجد منتجات حالياً</h2>
                    <p style="color: var(--text-muted);">يرجى إضافة منتجات من لوحة التحكم</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $i => $item): 
                    // استخراج بيانات حقيقية للمواصفات
                    $specsText = !empty($item['specifications']) ? explode("\n", trim($item['specifications'])) : [];
                    $spec1 = $item['model'] ?? 'Standard';
                    
                    // البحث عن السرعة
                    $speedVal = isset($specsText[0]) ? $specsText[0] : 'غير محدد';
                    foreach($specsText as $t) {
                        if(mb_stripos($t, 'سرعة') !== false || mb_stripos($t, 'Speed') !== false || mb_stripos($t, 'ppm') !== false) {
                            // استخراج الرقم والوحدة فقط إذا أمكن
                            if(preg_match('/(\d+)/', $t, $m)) {
                                $speedVal = $m[1] . ' ورقة/دقيقة';
                            } else {
                                $speedVal = mb_substr(trim($t), 0, 15);
                            }
                            break;
                        }
                    }
                    
                    $spec2 = $speedVal;
                    $spec3 = $item['category_name'] ?? 'System';

                    $specs = [
                        ['val' => $spec1, 'label' => 'الموديل'],
                        ['val' => $spec2, 'label' => 'السرعة'],
                        ['val' => $spec3, 'label' => 'النوع']
                    ];

                    $baseUrl = defined('BASE_URL') ? BASE_URL : '/al-muwafi';
                    $imgUrl = "https://placehold.co/800x800/png?text=RICOH+SERIES";

                    
                    // استخدام 'thumbnail' حيث يتم التخزين فيه، مع دعم 'image' للتوافق
                    $imgField = !empty($item['thumbnail']) ? $item['thumbnail'] : ($item['image'] ?? null);
                    
                    if (!empty($imgField) && $imgField != 'default_product.png') {
                         // التحقق مما إذا كان المسار يحتوي بالفعل على products/
                         if (strpos($imgField, 'products/') === 0) {
                             $imgUrl = $baseUrl . '/storage/uploads/' . $imgField;
                         } else {
                             $imgUrl = $baseUrl . '/storage/uploads/products/' . $imgField;
                         }
                    }
                ?>
                <div class="c-slide <?= $i===0?'active':'' ?>" data-id="<?= $i ?>">
                    
                    <!-- Background Typography (Machine Model) -->
                    <div class="c-bg-text"><?= htmlspecialchars($item['model'] ?? 'RICOH') ?></div>
                    
                    <!-- LEFT STAGE: Platform & Product -->
                    <div class="c-stage-left">
                        <div class="c-plat-container">
                            <div class="c-product-visual">
                                <img src="<?= $imgUrl ?>" class="c-product-img">
                                <!-- Reflection is now purely CSS based if possible, or we duplicate img -->
                            </div>
                            <!-- The Glass Floor -->
                            <div class="c-glass-floor"></div>
                        </div>
                    </div>

                    <!-- RIGHT STAGE: Content -->
                    <div class="c-stage-right">
                        <div>
                            <span class="c-category"><?= htmlspecialchars($item['category_name'] ?? 'Office Solutions') ?></span>
                        </div>
                        <h1 class="c-title"><?= htmlspecialchars($item['name']) ?></h1>
                        <p class="c-desc">
                            <?= htmlspecialchars(mb_substr(strip_tags($item['description'] ?? 'أداء استثنائي يلبي كافة احتياجات العمل المتطورة بدقة وسرعة لا تضاهى.'), 0, 150)) ?>...
                        </p>
                        
                        <!-- Modern Specs Hybrid Table -->
                        <div class="c-specs-modern">
                            <?php foreach($specs as $si => $spec): ?>
                            <div class="c-spec-item">
                                <span class="c-spec-label"><?= $spec['label'] ?></span>
                                <span class="c-spec-val"><?= $spec['val'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <a href="<?= BASE_URL ?>/products/show/<?= $item['slug'] ?>" class="c-btn-modern">
                             عرض التفاصيل <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>

            <?php if(count($products) > 1): ?>
            <div class="c-controls">
                <button class="c-ctrl-btn c-prev" aria-label="Previous">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button class="c-ctrl-btn c-next" aria-label="Next">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            <?php endif; ?>
        </section>

    </div>
<?php
$content = ob_get_clean();

// --- Extra Scripts ---
ob_start();
?>
    <!-- Cinematic Logic Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Hide Loader
        const loader = document.getElementById('page-loader');
        if(loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
            }, 800); // Slight delay to ensure content is ready
        }

        // Register GSAP ScrollTrigger
        gsap.registerPlugin(ScrollTrigger);

        const slides = document.querySelectorAll('.c-slide');
        if (slides.length < 2) {
            // Anim single slide entrance
            if(slides.length === 1) {
                gsap.from(slides[0].querySelector('.c-stage-left'), { opacity:0, x:-50, duration:1.5, ease: "power3.out" });
                gsap.from(slides[0].querySelector('.c-stage-right'), { opacity:0, x:50, duration:1.5, delay:0.2, ease: "power3.out" });
            }
            return;
        }

        let current = 0;
        let isAnimating = false;
        const total = slides.length;

        function gotoSlide(index, direction = 'next') {
            if (isAnimating || index === current) return;
            isAnimating = true;

            const nextSlide = slides[index];
            const currSlide = slides[current];
            
            // Elements
            const nextLeft = nextSlide.querySelector('.c-stage-left');
            const nextRight = nextSlide.querySelector('.c-stage-right');
            const nextText = nextSlide.querySelector('.c-bg-text'); // BG Text

            const currLeft = currSlide.querySelector('.c-stage-left');
            const currRight = currSlide.querySelector('.c-stage-right');
            const currText = currSlide.querySelector('.c-bg-text'); // BG Text

            // 1. Setup Next Slide (Hidden but ready)
            gsap.set(nextSlide, { opacity: 1, zIndex: 10 });
            gsap.set(currSlide, { zIndex: 5 });
            
            // Initial Positions for "Split Entrance" (Optimized: No Blur)
            const enterFromX_Left = direction === 'next' ? 100 : -100; // Product
            const enterFromX_Right = direction === 'next' ? -50 : 50;  // Content

            gsap.set(nextLeft, { xPercent: enterFromX_Left, opacity: 0, scale: 0.9 }); // Reduced scale change
            gsap.set(nextRight, { x: enterFromX_Right, opacity: 0 });
            if(nextText) gsap.set(nextText, { opacity: 0 }); // Hide next text initially

            // 2. Animate OUT Current (Performance Optimized)
            const exitToX_Left = direction === 'next' ? -100 : 100;

            const tl = gsap.timeline({
                onComplete: () => {
                    currSlide.classList.remove('active');
                    gsap.set(currSlide, { clearProps: "all" });
                    gsap.set(currLeft, { clearProps: "all" });
                    gsap.set(currRight, { clearProps: "all" });
                    
                    nextSlide.classList.add('active');
                    current = index;
                    isAnimating = false;
                }
            });

            // EXIT PHASE
            if(currText) {
                 tl.to(currText, { opacity: 0, duration: 0.3 }, 0); // Fade out old text instantly
            }
            tl.to(currLeft, { 
                xPercent: exitToX_Left, 
                opacity: 0, 
                scale: 1.05, 
                duration: 0.8, 
                ease: "power2.inOut" 
            }, 0)
            .to(currRight, { 
                x: -exitToX_Left / 2, 
                opacity: 0, 
                duration: 0.6, 
                ease: "power2.in" 
            }, 0);

            // ENTER PHASE
            if(nextText) {
                // Fade in new text LATE (after machine starts appearing)
                tl.to(nextText, { opacity: 1, duration: 0.8, ease: "power2.out" }, 0.6); 
            }
            tl.to(nextLeft, { 
                xPercent: 0, 
                opacity: 1, 
                scale: 1,
                duration: 1.2, 
                ease: "power3.out" // Snappier
            }, 0.5) 
            .to(nextRight, { 
                x: 0, 
                opacity: 1, 
                duration: 1, 
                ease: "power3.out"
            }, 0.7);

            // Stagger Specs
            const specs = nextSlide.querySelectorAll('.c-spec-item');
            if(specs.length) {
                tl.fromTo(specs, 
                    { y: 15, opacity: 0 },
                    { y: 0, opacity: 1, stagger: 0.05, duration: 0.5, ease: "power2.out" }, // Faster stagger
                    1
                );
            }
        }

        const nextBtn = document.querySelector('.c-next');
        const prevBtn = document.querySelector('.c-prev');

        if(nextBtn) nextBtn.onclick = () => gotoSlide((current + 1) % total, 'next');
        if(prevBtn) prevBtn.onclick = () => gotoSlide((current - 1 + total) % total, 'prev');
        
        // Initial Anim
        const first = slides[0];
        if (first) {
            gsap.from(first.querySelector('.c-stage-left'), { opacity:0, x:-50, duration:1.5 });
            gsap.from(first.querySelector('.c-stage-right'), { opacity:0, x:50, duration:1.5, delay:0.2 });
        }

        // Spawn Professional Gold Dust
        function createParticle() {
            const activeSlide = document.querySelector('.c-slide.active');
            if(!activeSlide) return;
            
            const stage = activeSlide.querySelector('.c-plat-container');
            if(!stage) return;
            
            const p = document.createElement('div');
            p.classList.add('c-particle');
            stage.appendChild(p);
            
            // Strictly Rim Position
            const angle = Math.random() * Math.PI * 2;
            const bgRadius = 260; // Rim location
            
            // No randomness in radius - Perfect Ring
            const x = Math.cos(angle) * bgRadius;
            const y = Math.sin(angle) * bgRadius * 0.35; // Perspective
            
            gsap.set(p, { x: x, y: 50 + y, opacity: 0, scale: 0.5 });
            
            // Elegant Rise
            gsap.to(p, {
                y: -100 - Math.random() * 50, // Gentle rise
                opacity: 0.6, // Soft opacity
                scale: 1.5, // Grow slightly (Nebula feel)
                duration: 4 + Math.random() * 2, // Slow
                ease: "power1.out",
                onComplete: () => {
                    gsap.to(p, { opacity: 0, duration: 2, onComplete: () => p.remove() });
                }
            });
        }
        
        // Frequent soft spawn (Optimized: Slower rate + Check visibility)
        setInterval(() => {
            if(!isAnimating && document.visibilityState === 'visible') {
                createParticle();
            }
        }, 200);

    });
    </script>
<?php
$extraScripts = ob_get_clean();
$hideFooter = true; // Cinematic slider probably handles its own footer or is full height, but checking the original code it had a footer inside the wrapper. However, the original code had a footer inside page-wrapper. Wait, layout appends footer. Let's hide layout footer and let the page content handle it if it needs special placement, OR let layout handle it.
// Looking at original code, the footer was INSIDE page-wrapper.
// The public_layout.php appends footer at the end of body if not hidden.
// To match original exact structure, we might want to hide global footer and include it in content, 
// OR simpler: let global footer be there.
// BUT original products.php had footer inside .page-wrapper and .page-wrapper had min-height 100vh.
// If we use global footer, it will be outside .page-wrapper.
// Let's keep it simple: Use global footer, but we need to ensure styles match.
// Actually, looking at the layout, it puts $content then Footer. 
// In products.php, footer was last element in .page-wrapper.
// The visual difference is minimal unless .page-wrapper has specific flex properties affecting footer.
// .page-wrapper is flex column min-h-100vh.
// public_layout.php body structure is: Nav -> Main($content) -> Footer.
// This is identical to .page-wrapper structure effectively.
// So we can enable global footer and remove it from our $content.
// BUT, the original products.php had a specific footer style inside the file? 
// No, it just used class "footer".
// So we can rely on Global Footer.
$hideFooter = false;

// Include Layout
include VIEWS_PATH . '/layouts/public_layout.php';
?>

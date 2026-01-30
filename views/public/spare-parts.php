<?php
/**
 * Spare Parts Page - 3D Cinematic Gallery Edition
 * "The Showroom" Style
 */

use App\Services\Database;
use App\Services\Settings;

$currentPage = 'spare-parts';
$companyName = 'المُوَفِّي';
try {
    $db = Database::getInstance();
    $companyInfo = Settings::getCompanyInfo();
    $companyName = $companyInfo['name'] ?? 'المُوَفِّي';
} catch (\Throwable $e) {
    $companyInfo = [];
}

$title = 'قطع الغيار | ' . htmlspecialchars($companyName);

// --- Extra CSS ---
ob_start();
?>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <style>
        /* =========================================
           CORE IDENTITY STYLES
           Uses global variables from style.css
           ========================================= */

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* === BACKGROUND & ATMOSPHERE === */
        .gallery-stage {
            flex: 1; /* Pushes footer down */
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Changed from center to fix footer position */
            overflow: hidden;
            padding-top: 100px; /* Header Space */
            padding-bottom: 30px; /* Space before footer */
        }
        
        /* Background Orbs - Matching products.php */
        .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
        @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }

        /* Page Loader */
        .page-loader {
            position: fixed; inset: 0; z-index: 9999;
            background: #020617;
            display: flex; align-items: center; justify-content: center;
            transition: opacity 0.5s, visibility 0.5s;
        }
        .page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .loader-spinner {
            width: 50px; height: 50px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: var(--gold-1, #D4AF37);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* === 3D SWIPER CONTAINER === */
        .swiper-3d-container {
            width: 100%;
            padding-top: 50px;
            padding-bottom: 50px;
            position: relative;
            z-index: 10;
            perspective: 1000px;
        }

        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 260px; /* Reduced from 300px */
            height: 340px; /* Reduced from 400px */
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Changed from center to accommodate layout better */
            padding-top: 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.5);
            user-select: none;
            transition: all 0.5s;
            -webkit-box-reflect: below 1px linear-gradient(transparent, transparent #0004, rgba(0,0,0,0.3));
        }
        
        .swiper-slide-active {
            border-color: var(--gold-1);
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.3);
            z-index: 100;
        }

        .slide-img-box {
            width: 90%; 
            height: 55%; /* Fixed height occupation */
            display: flex; align-items: center; justify-content: center;
            position: relative;
            margin-bottom: 10px;
        }
        
        .slide-img {
            max-width: 100%; max-height: 100%; 
            object-fit: contain;
            background: transparent !important; /* Ensure no bg */
            /* filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)); Removed to prevent box outline */
            transition: 0.5s;
        }
        
        .swiper-slide-active .slide-img { transform: scale(1.1) translateY(-10px); }

        .slide-content {
            text-align: center; margin-top: 20px;
            opacity: 0; transform: translateY(20px); transition: 0.5s;
        }
        .swiper-slide-active .slide-content { opacity: 1; transform: translateY(0); }
        
        .slide-title { font-size: 1.2rem; font-weight: 700; color: #fff; margin-bottom: 5px; }
        .slide-pn { font-family: 'Oswald'; color: var(--gold-1); font-size: 0.9rem; }

        /* === ACTIVE DETAILS PANE (Bottom) === */
        
        /* Container Constraint */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .active-details {
            position: relative; z-index: 20;
            text-align: right;
            width: 100%;
            
            /* 2-Column Grid Layout - Compact */
            display: grid;
            grid-template-columns: 2fr auto 0.8fr; /* Description takes most space */
            gap: 30px; /* Reduced gap */
            align-items: center;
            padding: 30px 25px; /* Significantly reduced padding */

            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.98));
            backdrop-filter: blur(25px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 
                0 30px 60px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.1);
            margin-top: 10px; margin-bottom: 20px;
            opacity: 0; transform: translateY(30px);
            transition: 0.5s;
        }
        
        @media (max-width: 992px) {
            .active-details {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 30px;
                padding: 40px 20px;
            }
        }
        .active-details.visible { 
            opacity: 1; 
            transform: translateY(0); 
        }

        /* Staggered Animation for Children */
        .ad-title, .ad-desc, .ad-specs, .ad-btn {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1); /* Custom Easing */
        }

        .active-details.visible .ad-title { 
            opacity: 1; transform: translateY(0); 
            transition-delay: 0.1s; 
        }
        .active-details.visible .ad-desc { 
            opacity: 1; transform: translateY(0); 
            transition-delay: 0.2s; 
        }
        .active-details.visible .ad-specs { 
            opacity: 1; transform: translateY(0); 
            transition-delay: 0.3s; 
        }
        .active-details.visible .ad-btn { 
            opacity: 1; transform: translateY(0); 
            transition-delay: 0.4s; 
        }

        .ad-title {
            font-size: 2.2rem; /* Reduced from 3rem */
            font-weight: 800; color: #fff; margin-bottom: 15px;
            background: linear-gradient(to right, var(--gold-1), var(--gold-2), #fff);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .ad-desc { 
            color: #cbd5e1; 
            font-size: 1.05rem; /* Increased slightly */
            line-height: 1.7;
            margin-bottom: 0; 
            max-width: 100%; 
        }
        
        .ad-specs { 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
            margin-bottom: 30px; 
            width: 100%;
        }
        
        .ad-tag {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(255,255,255,0.15);
            border-left: 2px solid var(--gold-1, #D4AF37);
            border-radius: 6px;
            padding: 8px 12px;
            width: 100%;
            justify-content: center;
            gap: 10px;
            display: flex;
            font-size: 0.9rem;
            align-items: center;
            transition: 0.3s;
            color: #fff;
        }
        .ad-tag:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(-5px);
        }
        
        .ad-btn {
            width: 100%;
            justify-content: center;
            background: linear-gradient(135deg, var(--gold-1), var(--gold-3)); 
            color: #000;
            padding: 10px 20px;
            font-size: 1rem; font-weight: 700;
            border-radius: 8px; 
            text-decoration: none;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
            transition: 0.3s, opacity 0.6s, transform 0.6s; /* Ensure hover and entrance don't conflict, though specific properties handle this */
            display: inline-flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .ad-btn:hover { 
            transform: translateY(-3px) !important; /* Force hover effect over entrance animation if needed, but entrance settles at 0 */
            box-shadow: 0 10px 40px rgba(212, 175, 55, 0.6); 
        }

        /* === FILTER BAR === */
        .gallery-filter {
            position: relative; 
            top: 0; left: 0; right: 0; z-index: 30;
            display: flex; justify-content: center; gap: 10px; padding: 0 20px;
            margin-bottom: 30px;
        }
        .gf-btn {
            background: rgba(0,0,0,0.3); color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 20px;
            cursor: pointer; transition: 0.3s; text-decoration: none; font-size: 0.9rem;
        }
        .gf-btn.active, .gf-btn:hover { background: var(--neon-blue); color: #fff; border-color: var(--neon-blue); }

        /* === CONTROLS === */
        .swiper-button-next, .swiper-button-prev { color: var(--gold-1); }

        @media (max-width: 768px) {
            .swiper-slide { width: 220px; height: 320px; }
            .ad-title { font-size: 1.8rem; }
            .gallery-filter { overflow-x: auto; justify-content: flex-start; padding-bottom: 10px; }
            .gf-btn { white-space: nowrap; }
        }
        
        @media (max-width: 480px) {
            .swiper-slide { width: 180px; height: 280px; }
            .slide-title { font-size: 1rem; }
            .slide-pn { font-size: 0.8rem; }
            .ad-title { font-size: 1.4rem; }
            .ad-desc { font-size: 0.9rem; line-height: 1.6; }
            .ad-tag { font-size: 0.8rem; padding: 6px 10px; width: 100%; }
            .ad-btn { font-size: 0.9rem; padding: 12px 20px; width: 100%; }
            .ad-specs { gap: 10px; }
            .gallery-filter { gap: 8px; }
            .gf-btn { padding: 6px 14px; font-size: 0.8rem; }
            .gallery-stage { padding-top: 80px; }
            .swiper-3d-container { padding-top: 30px; padding-bottom: 30px; }
            
            /* Details Panel Mobile - Stack columns */
            .active-details { 
                grid-template-columns: 1fr !important; 
                padding: 20px 15px !important; 
                gap: 20px !important;
            }
            .ad-col-right, .ad-col-left { padding: 0 !important; text-align: center; }
            /* Hide separator on mobile */
            .active-details > div[style*="width: 2px"] { display: none; }
        }
    </style>
<?php
$extraHead = ob_get_clean();

// --- Main Content ---
ob_start();
?>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>

    <!-- Background (Global) -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="gallery-stage">
        
        <!-- Filters -->
        <div class="gallery-filter">
            <a href="<?= BASE_URL ?>/spare-parts" class="gf-btn <?= !$currentCategory ? 'active' : '' ?>">الكل</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/spare-parts?category=<?= $cat['id'] ?>" 
               class="gf-btn <?= ($currentCategory && $currentCategory['id'] == $cat['id']) ? 'active' : '' ?>">
               <?= htmlspecialchars($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- 3D Slider -->
        <div class="swiper swiper-3d-container">
            <div class="swiper-wrapper">
                <?php if (empty($spareParts)): ?>
                    <div class="swiper-slide">
                        <div style="color:#fff; text-align:center;">لا توجد قطع</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($spareParts as $part): ?>
                    <div class="swiper-slide" 
                         data-title="<?= htmlspecialchars($part['name']) ?>"
                         data-pn="<?= htmlspecialchars($part['part_number']) ?>"
                         data-desc="<?= htmlspecialchars($part['description'] ?? 'قطعة غيار أصلية عالية الجودة.') ?>"
                         data-contact="<?= BASE_URL ?>/contact?inquiry=<?= urlencode($part['part_number'] . ' - ' . $part['name']) ?>"
                         data-category="<?= htmlspecialchars($part['category_name'] ?? 'عام') ?>"
                         data-models='<?= json_encode(array_column($part['compatible_models'], 'name')) ?>'
                         >
                        
                        <div class="slide-img-box">
                            <img src="<?= $part['image'] ? BASE_URL . '/storage/uploads/' . htmlspecialchars($part['image']) : 'https://placehold.co/300x400/png?text=PART' ?>" class="slide-img" alt="<?= htmlspecialchars($part['name']) ?>">
                        </div>

                        <div class="slide-content">
                            <div class="slide-title"><?= htmlspecialchars($part['name']) ?></div>
                            <div class="slide-pn"><?= htmlspecialchars($part['part_number']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>

        <!-- Active Details Pane -->
        <div class="container">
            <div class="active-details visible" id="details-pane">
                
                <!-- Right Column: Description -->
                <div class="ad-col-right" style="padding-left: 20px;">
                    <h1 class="ad-title" id="d-title">اختر قطعة</h1>
                    <p class="ad-desc" id="d-desc">قم بالسحب يميناً ويساراً لاستعراض التشكيلة، أو اختر من القوائم السابقة.</p>
                </div>
                
                <!-- The Golden Separator -->
                <div style="width: 2px; height: 80%; background: linear-gradient(to bottom, transparent, var(--gold-1), transparent); opacity: 0.6;"></div>
                
                <!-- Left Column: Data & Action -->
                <div class="ad-col-left" style="padding-right: 20px;">
                    <div class="ad-specs" id="d-specs">
                        <!-- Specs injected here -->
                    </div>
                    
                    <a href="#" class="ad-btn" id="d-btn">
                        <span>استفسار عن السعر</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>

            </div>
        </div>

    </div>
<?php
$content = ob_get_clean();

// --- Extra Scripts ---
ob_start();
?>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Hide loader
            const loader = document.getElementById('pageLoader');
            if(loader) loader.classList.add('hidden');
            // Init Swiper 3D
            const swiper = new Swiper('.swiper-3d-container', {
                effect: 'coverflow',
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: 'auto',
                initialSlide: 1, 
                coverflowEffect: {
                    rotate: 20,
                    stretch: 0,
                    depth: 200,
                    modifier: 1,
                    slideShadows: true,
                },
                loop: false, 
                pagination: { el: '.swiper-pagination', clickable: true },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                on: {
                    init: function() {
                        if(this.slides.length > 0) {
                            updateDetails(this.slides[this.activeIndex]);
                        }
                    },
                    slideChange: function() {
                        updateDetails(this.slides[this.activeIndex]);
                    }
                }
            });

            // Function to update details pane
            function updateDetails(slide) {
                if(!slide) return;
                
                const title = slide.getAttribute('data-title');
                const pn = slide.getAttribute('data-pn');
                const desc = slide.getAttribute('data-desc');
                const contact = slide.getAttribute('data-contact');
                const category = slide.getAttribute('data-category');
                
                const pane = document.getElementById('details-pane');
                pane.classList.remove('visible');
                
                setTimeout(() => {
                    document.getElementById('d-title').textContent = title;
                    document.getElementById('d-desc').textContent = desc || 'تواصل معنا لمعرفة المزيد عن هذه القطعة.';
                    document.getElementById('d-btn').href = contact;
                    
                    const specsContainer = document.getElementById('d-specs');
                    specsContainer.innerHTML = '';
                    
                    specsContainer.innerHTML += `<div class="ad-tag"><i class="fas fa-barcode"></i> <span>PN: ${pn}</span></div>`;
                    
                    if(category) specsContainer.innerHTML += `<div class="ad-tag"><i class="fas fa-layer-group"></i> <span>${category}</span></div>`;
                    
                    try {
                        const models = JSON.parse(slide.getAttribute('data-models') || '[]');
                        if(models.length > 0) {
                            specsContainer.innerHTML += `<div class="ad-tag"><i class="fas fa-print"></i> <span>متوافق مع: ${models.slice(0,3).join(', ')}</span></div>`;
                        }
                    } catch(e) {}
                    
                    pane.classList.add('visible');
                }, 300);
            }
        });
    </script>
<?php
$extraScripts = ob_get_clean();

// Include Layout
include VIEWS_PATH . '/layouts/public_layout.php';
?>

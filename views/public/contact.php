<?php
/**
 * صفحة اتصل بنا - التصميم السينمائي (Final Fixed Version)
 * استخدام كلاسات جديدة لضمان عدم وجود تضارب في التنسيق
 */

use App\Services\Settings;
use App\Services\Security;

$currentPage = 'contact';
$hideFooter = true;
$companyInfo = Settings::getCompanyInfo();
$socialLinks = Settings::getSocialLinks();

// productInquiry is passed from ContactController
$productInquiry = $productInquiry ?? '';

// Use dynamic map from settings if available, otherwise fallback
// Fallback to default map for now to ensure visibility
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
$mapEmbedSrc = $defaultMap; 
// $mapEmbedSrc = !empty($companyInfo['map_embed_url']) ? $companyInfo['map_embed_url'] : $defaultMap;

$title = 'اتصل بنا | ' . ($companyInfo['name'] ?? 'المُوَفِّي');

// --- Extra CSS ---
ob_start();
?>
    <!-- FontAwesome & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');
        
        /* --- GLOBAL RESET --- */
        * { box-sizing: border-box; }
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; }
        @media (max-width: 1000px) {
            body, html { overflow: auto; height: auto; }
        }
        
        /* === PAGE LOADER === */
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
            border-top-color: #D4AF37;
            border-radius: 50%;
            animation: loaderSpin 1s linear infinite;
        }
        @keyframes loaderSpin { to { transform: rotate(360deg); } }

        /* === UNIFIED BACKGROUND ORBS === */
        .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
        @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }

        /* --- MAIN LAYOUT WRAPPER --- */
        .cinematic-wrapper-v2 {
            position: fixed;
            top: 90px;
            left: 0;
            width: 100vw;
            height: calc(100vh - 90px);
            display: flex;
            justify-content: flex-end; /* Sidebar aligns to Right */
            align-items: flex-start;
            z-index: 50;
            font-family: 'Tajawal', sans-serif;
            direction: ltr; /* Wrapper is LTR */
        }
        
        /* 1. MAP SECTION (FULL SCREEN ABSOLUTE) */
        /* 1. MAP SECTION (FLOATING BACKGROUND) */
        .map-zone {
            position: absolute;
            /* Floating Margins */
            top: 30px; 
            left: 30px; 
            width: calc(100% - 60px); 
            height: calc(100% - 60px);
            z-index: 0; /* Behind Styles */
            background: #020617;
            border-radius: 30px; /* Rounded Corners */
            overflow: hidden; /* Clip iframe */
            border: 1px solid #cfaa5d; /* Golden Border */
            box-shadow: 0 0 50px rgba(0,0,0,0.5); /* Depth Shadow */
        }
        .map-zone iframe {
            width: 100%; height: 100%; border: none;
            filter: grayscale(100%) invert(92%) hue-rotate(190deg) brightness(0.85) contrast(1.1);
        }
        .map-gradient {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            /* Gradient matches rounded corners because of parent overflow:hidden */
            background: linear-gradient(90deg, rgba(2, 6, 23, 0.4) 0%, rgba(2, 6, 23, 0.9) 100%);
            pointer-events: none;
        }
        
        /* 2. SIDEBAR SECTION (RIGHT - FLOATING OVER MAP) */
        .sidebar-zone {
            position: relative; 
            z-index: 10; /* Above Map */
            flex: 0 0 460px; /* Reduced from 500px to make form narrower */
            max-width: 90vw;
            /* Crystal Clear Glass */
            background: rgba(0, 0, 0, 0.2); 
            backdrop-filter: blur(3px); /* Minimal blur to show map details */
            margin: 60px 45px 60px 0; /* Adjusted margin slightly */
            border-radius: 30px;
            padding: 30px; /* Restored and symmetric Padding */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            direction: rtl; 
            /* border: 1px solid rgba(255, 255, 255, 0.08); Removed for live border */
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            height: calc(100% - 120px); /* Restored height calc */
            z-index: 10; /* Ensure stacking context */
        }
        .sidebar-zone::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 30px; 
            padding: 1px; /* Border Width */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
            -webkit-mask: 
            linear-gradient(#fff 0 0) content-box, 
            linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            z-index: 2; /* On Top */
            pointer-events: none; /* Allow clicks */
            background-size: 200% auto;
            animation: shine 5s linear infinite;
        }
        
        /* -- BRANDING -- */
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center; /* Centered */
            margin-bottom: 10px; /* Reduced to pull elements closer */
        }
        .brand-title {
            font-size: 2.5rem; 
            font-weight: 900; 
            margin: 0; 
            line-height: 1.5; /* Increased from 1 to 1.5 to show dots */
            padding-bottom: 5px; /* Extra space for descenders */
            /* MATCHING ABOUT US GOLD GRADIENT */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
            filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.2));
            animation: shine 5s linear infinite; /* Optional subtle animation */
        }
        @keyframes shine { to { background-position: 200% center; } }


        /* -- NEW CONTACT CARDS (Horizontal) -- */
        .cards-container {
            display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; /* Reduced gap (15->8) and margin (30->15) */
        }
        .contact-card-horizontal {
            display: flex !important;
            flex-direction: row !important; /* STRICTLY ROW */
            align-items: center !important;
            justify-content: flex-start !important;
            background: rgba(15, 23, 42, 0.3); /* High Transparency */
            /* border: 1px solid #cfaa5d; Removed for live border */
            border-radius: 12px;
            padding: 8px 12px; /* Restored padding */
            min-height: 50px; /* Restored height */
            transition: 0.3s;
            position: relative;
            z-index: 1;
        }
        .contact-card-horizontal::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 12px; 
            padding: 1px; /* Border Width */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
            -webkit-mask: 
            linear-gradient(#fff 0 0) content-box, 
            linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            z-index: 2; /* Brought to front */
            pointer-events: none; /* Allow clicks through */
            background-size: 200% auto;
            animation: shine 5s linear infinite;
        }
        .contact-card-horizontal:hover {
            background: rgba(207, 170, 93, 0.1);
            transform: translateX(-5px);
        }
        
        .card-icon-gold {
            width: 32px; height: 32px; /* Smaller Icon Box */
            /* MATCHING ABOUT US GOLD GRADIENT */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
            background-size: 200% auto;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #1a1a1a; /* Darker icon for better contrast on metallic gold */
            font-size: 0.9rem; /* Smaller Font */
            flex-shrink: 0;
            margin-left: 12px; /* Reduced spacing */
            box-shadow: 0 2px 10px rgba(180, 83, 9, 0.3);
            animation: shine 5s linear infinite;
        }
        
        .card-content {
            display: flex; 
            flex-direction: row; 
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            color: white;
        }
        .card-label { /* No longer used but kept for safety */
            display: none; 
        }
        .card-value {
            color: #fff; 
            font-weight: 400; /* Removed Bold (was 800) */
            font-size: 0.85rem; /* Reduced size (was 0.95rem) */
            font-family: 'Segoe UI', sans-serif;
        }
        
        /* -- FORM STYLES -- */
        .form-box {
            background: rgba(255,255,255,0.02);
            /* Removed redundant border to let pseudo-element shine */
            /* border: 1px solid rgba(255,255,255,0.05); */
            border-radius: 15px;
            padding: 25px; /* Restored padding */
            position: relative; /* For pseudo-element absolute positioning */
            z-index: 1;
        }
        /* Live Animated Gold Border */
        .form-box::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 15px; 
            padding: 1px; /* Border Width */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
            -webkit-mask: 
            linear-gradient(#fff 0 0) content-box, 
            linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            z-index: 2; /* Brought to front */
            pointer-events: none; /* Allow clicks through */
            background-size: 200% auto;
            animation: shine 5s linear infinite;
        }
        
        .form-title { color: #cfaa5d; font-size: 1.4rem; font-weight: 800; margin-bottom: 18px; display: block; }
        
        .inp-group { margin-bottom: 10px; } /* Reduced gap as requested */
        .inp-field {
            width: 100%; 
            background: rgba(11, 17, 32, 0.75); /* Increased visibility (was 0.4) */
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px; padding: 14px; color: white; font-family: 'Tajawal'; 
        }
        .inp-field::placeholder {
            color: rgba(255, 255, 255, 0.9); /* White text for instructions */
            opacity: 1;
        }
        .inp-field:focus { border-color: #cfaa5d; outline: none; }
        
        /* Phone Input: Placeholder Right (RTL), Typing Left (LTR) */
        input[name="phone"] {
            text-align: right;
            direction: rtl;
        }
        input[name="phone"]:focus,
        input[name="phone"]:not(:placeholder-shown) {
            text-align: left;
            direction: ltr;
        }
        
        .submit-btn {
            width: 100%; padding: 14px; /* Restored padding */
            /* MATCHING HEADER GOLD GRADIENT */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            background-size: 200% auto;
            animation: shine 5s linear infinite;
            color: black;
            font-weight: bold; border: none; border-radius: 8px; cursor: pointer;
            font-size: 1.1rem; margin-top: 5px; /* Reduced gap */
        }
        .submit-btn:hover { 
            background-position: right center; /* animate gradient on hover */
            box-shadow: 0 0 15px rgba(207, 170, 93, 0.4); 
        }
        
        /* -- SOCIAL -- */
        .social-links { display: flex; gap: 15px; margin-top: 25px; opacity: 0.6; } /* Restored margin */
        .social-links a { color: white; font-size: 1.2rem; }
        
        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .cinematic-wrapper-v2 { 
                flex-direction: column; 
                width: 100%; /* Force full width, not viewport width (avoids scrollbar issues) */
                height: auto !important; 
                min-height: auto !important;
                position: static; 
                overflow: visible; 
                display: flex;
                align-items: center; /* Center Items Horizontally */
                padding: 100px 0 30px 0; /* Adjusted padding */
            }
            .sidebar-zone { 
                width: 90%; 
                max-width: 500px; 
                flex: none; 
                margin: 0 auto 30px auto; 
                height: auto;
                min-height: auto;
                order: 1; 
            }
            .map-zone { 
                position: relative; 
                top: auto; left: auto;
                width: 90%; 
                max-width: 500px;
                height: 350px;
                margin: 0 auto 20px auto; 
                order: 2; 
            }
            .map-gradient { background: linear-gradient(to bottom, rgba(2, 6, 23, 0.3) 0%, transparent 50%, rgba(2, 6, 23, 0.5) 100%); }
        }

        @media (max-width: 768px) {
            .brand-title { font-size: 2rem; }
            .sidebar-zone { padding: 25px; margin-bottom: 25px; } /* Clean margins */
            .form-box { padding: 20px; }
            .form-title { font-size: 1.2rem; }
            .inp-field { padding: 12px; font-size: 0.9rem; }
            .submit-btn { padding: 12px; font-size: 1rem; }
            .contact-card-horizontal { padding: 10px; }
            .card-icon-gold { width: 28px; height: 28px; font-size: 0.8rem; }
            .card-value { font-size: 0.8rem; }
            .map-zone { 
                height: 300px; 
                border-radius: 20px; 
            }
        }

        @media (max-width: 480px) {
            .brand-title { font-size: 1.6rem; }
            .sidebar-zone { 
                width: 90%; 
                padding: 15px; 
                border-radius: 20px; 
                margin-bottom: 20px;
            }
            .form-box { padding: 15px; border-radius: 12px; }
            .form-title { font-size: 1.1rem; margin-bottom: 12px; }
            .inp-group { margin-bottom: 8px; }
            .inp-field { padding: 10px; font-size: 0.85rem; }
            .submit-btn { padding: 10px; font-size: 0.95rem; }
            .cards-container > div[style*="flex"] { flex-direction: column !important; gap: 8px !important; }
            .contact-card-horizontal { min-height: 40px; }
            .card-icon-gold { width: 26px; height: 26px; font-size: 0.75rem; margin-left: 10px; }
            .card-value { font-size: 0.75rem; }
            .map-zone { 
                width: 90%; 
                height: 250px; 
                border-radius: 15px; 
            }
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

    <div class="cinematic-wrapper-v2">
        
        <!-- MAP (Left) -->
        <div class="map-zone">
            <div class="map-gradient"></div>
            <iframe src="<?= $mapEmbedSrc ?>" allowfullscreen="" loading="lazy"></iframe>
        </div>

        <!-- SIDEBAR (Right) -->
        <div class="sidebar-zone">
            
            <div class="brand-header">
                <h1 class="brand-title">المُوَافِي</h1>
            </div>

            <div class="cards-container">
                
                <!-- PHONE & EMAIL ROW -->
                <div style="display: flex; gap: 15px; width: 100%;">
                    
                    <!-- PHONE -->
                    <div class="contact-card-horizontal" style="flex: 1;">
                        <div class="card-icon-gold">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="card-content">
                            <span class="card-value"><?= $companyInfo['phone'] ?></span>
                        </div>
                    </div>

                    <!-- EMAIL -->
                    <div class="contact-card-horizontal" style="flex: 1;">
                        <div class="card-icon-gold">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="card-content">
                            <span class="card-value"><?= $companyInfo['email'] ?></span>
                        </div>
                    </div>

                </div>

                <!-- ADDRESS -->
                <div class="contact-card-horizontal">
                    <div class="card-icon-gold">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div class="card-content">
                        <span class="card-value"><?= $companyInfo['address'] ?></span>
                    </div>
                </div>

            </div>

            <div class="form-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span class="form-title" style="margin-bottom: 0;">تواصل معنا</span>
                    
                    <!-- MINI SOCIALS -->
                    <div style="display: flex; gap: 10px;">
                        <?php if (!empty($companyInfo['whatsapp'])): 
                            // Sanitized number with Egypt Country Code (+2)
                            // Prepend '2' to ensure it works internationally (e.g. 010 -> 2010)
                            $waNum = '2' . preg_replace('/[^0-9]/', '', $companyInfo['whatsapp']);
                        ?>
                            <a href="https://wa.me/<?= $waNum ?>" target="_blank" style="color: #25D366; font-size: 1.5rem; transition: 0.3s;" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($socialLinks['facebook'])): ?>
                            <a href="<?= $socialLinks['facebook'] ?>" target="_blank" style="color: #1877F2; font-size: 1.5rem; transition: 0.3s;" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($success)): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); padding: 10px; color: #34d399; margin-bottom: 15px; border-radius: 5px; text-align: center;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); padding: 10px; color: #f87171; margin-bottom: 15px; border-radius: 5px; text-align: center;">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/contact/send" method="POST">
                    <?= $csrf_field ?? Security::csrfField() ?>
                    
                    <div class="inp-group">
                        <input type="text" name="name" class="inp-field" placeholder="الاسم بالكامل" required>
                    </div>
                    <div class="inp-group">
                        <input type="tel" name="phone" class="inp-field" placeholder="رقم الهاتف" required>
                    </div>
                    <div class="inp-group">
                        <input type="email" name="email" class="inp-field" placeholder="البريد الإلكتروني">
                    </div>
                    <div class="inp-group">
                        <textarea name="message" class="inp-field" rows="4" placeholder="اكتب رسالتك هنا..." required style="resize: none;"><?= htmlspecialchars($productInquiry) ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">إرسال الطلب</button>
                </form>
            </div>
            <!-- Social links removed from bottom as requested -->

        </div>
    </div>
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
</script>
<?php
$extraScripts = ob_get_clean();

require_once VIEWS_PATH . '/layouts/public_layout.php';
?>

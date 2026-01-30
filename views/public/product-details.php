<?php
/**
 * Product Details Page - "The Luxury Lab" Edition
 * Cinematic Dark Theme with Gold Accents
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
}

// Ensure product data exists
if (!isset($product)) {
    header("Location: " . (defined('BASE_URL') ? BASE_URL : '/al-muwafi') . "/products");
    exit;
}

$baseUrl = defined('BASE_URL') ? BASE_URL : '/al-muwafi';
$title = htmlspecialchars($product['name']) . ' | ' . htmlspecialchars($companyName);

// --- Extra CSS ---
ob_start();
?>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Oswald:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        /* =========================================
           CORE IDENTITY (Dark Luxury)
           ========================================= */
        :root {
            --bg-deep: #020617;
            --bg-dark: #0f172a;
            --bg-glass: rgba(30, 41, 59, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --neon-blue: #0ea5e9;
            --gold-1: #D4AF37;
            --gold-2: #F1D87E;
            --gold-3: #BF953F;
            --gold-4: #FCF6BA;
            --gold-5: #aa771c;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; outline: none; }
        body {
            font-family: 'Cairo', sans-serif;
            /* REMOVED: background-color and background-image to use global style.css background */
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* LOADERS & BG */
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

        /* Background Orbs - Same as products.php */
        .bg-orbs { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); animation: orbFloat 20s ease-in-out infinite; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%); bottom: -150px; left: -100px; animation-delay: -7s; }
        @keyframes orbFloat { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }



        .nav-back { 
            color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600; 
            display: flex; align-items: center; gap: 8px; transition: 0.3s;
            position: fixed; top: 100px; left: 5%; z-index: 1001; /* Positioned relative to page */
        }
        .nav-back:hover { color: var(--gold-1); transform: translateX(5px); }

        /* HERO SECTION */
        .hero {
            position: relative; min-height: 100vh; display: flex; align-items: center; z-index: 1;
            padding: 120px 5% 50px;
            background: transparent; /* Allow global background to show through */
        }
        .hero-container {
            width: 100%; max-width: 1400px; margin: 0 auto;
            display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 80px;
            align-items: center;
        }

        /* LEFT: VISUAL (Floating) */
        .visual-stage {
            position: relative; display: flex; justify-content: center; height: 500px;
        }
        .visual-platform {
            position: absolute; bottom: 0; width: 100%; max-width: 400px; height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.05), transparent 70%);
            border-radius: 50%; transform: rotateX(75deg);
            box-shadow: 0 0 50px rgba(14, 165, 233, 0.1);
        }
        .visual-img {
            position: relative; z-index: 5;
            width: 100%; height: 100%; object-fit: contain;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.6));
            animation: floatHero 6s ease-in-out infinite;
        }
        @keyframes floatHero { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }

        /* RIGHT: INFO */
        .info-stage { color: #fff; }
        .category-badge {
            display: inline-block; padding: 6px 16px; 
            background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px; color: var(--gold-1); font-size: 0.8rem; letter-spacing: 1px;
            margin-bottom: 20px; text-transform: uppercase; font-weight: 700;
        }
        .product-title {
            font-size: 3rem; /* Reduced from 4rem for better fit */
            font-weight: 800; line-height: 1.2; margin-bottom: 20px;
            background: linear-gradient(to bottom right, #fff, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .product-desc {
            font-size: 1.1rem; line-height: 1.7; color: var(--text-muted); 
            border-right: 3px solid var(--gold-1); padding-right: 20px; text-align: justify;
            margin-bottom: 40px; max-width: 600px;
        }

        /* HOLOGRAPHIC SPECS ROW (One Line) */
        .specs-grid {
            display: flex; gap: 15px; margin-bottom: 40px;
            overflow-x: auto; 
            padding: 15px 5px; /* Added padding top/bottom to prevent hover clip */
            flex-wrap: nowrap;
        }
        .specs-grid::-webkit-scrollbar { height: 4px; }
        .specs-grid::-webkit-scrollbar-thumb { background: var(--gold-1); border-radius: 4px; }
        
        .spec-card {
            background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px; padding: 15px; transition: 0.3s;
            display: flex; flex-direction: column; gap: 5px;
            min-width: 140px; flex: 1; /* Distribute space evenly */
        }
        .spec-card:hover {
            background: rgba(212, 175, 55, 0.05); border-color: var(--gold-1);
            transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .spec-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; white-space: nowrap; }
        .spec-val { font-size: 1.1rem; font-weight: 700; color: #fff; font-family: 'Oswald', sans-serif; white-space: nowrap; }

        /* THE Z-DEPTH INFINITY SYSTEM (Pure Void) */
        .z-stage {
            width: 100%; height: 90vh;
            display: flex; align-items: center; justify-content: center;
            position: relative;
            /* Force Transparent */
            background: transparent !important; 
            overflow: visible; 
            perspective: 1000px;
            cursor: pointer;
        }
        
        /* The "Tunnel" Container */
        .z-tunnel {
            width: 100%; height: 100%;
            position: relative;
            transform-style: preserve-3d;
        }
        
        .z-card {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            transform-style: preserve-3d;
            will-change: transform, opacity, filter;
        }
        
        .z-img {
            max-width: 95%; max-height: 95%;
            object-fit: contain;
            filter: drop-shadow(0 0 50px rgba(0,0,0,0.8));
            transition: all 0.1s;
        }
        
        /* REMOVED WATERMARK TO CLEAN BACKGROUND */
        .z-hint { display: none; }

        
        /* Hide Progress Bars if they look like borders? Keep them minimal */
        .z-progress {
            position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 5px; z-index: 100;
        }
        .z-bar {
            width: 30px; height: 3px; background: rgba(255,255,255,0.1); overflow: visible;
            border-radius: 4px; border: none;
        }
        .z-fill {
            width: 100%; height: 100%; background: var(--gold-1);
            transform: scaleX(0); transform-origin: left; transition: transform 0.5s;
        }
        .z-bar.active .z-fill { transform: scaleX(1); }

        /* THE GLASS PLATFORM SYSTEM (Items 1 & 2) */
        .platform-stage {
            width: 100%; height: 600px;
            display: flex; flex-direction: column; align-items: center; justify-content: flex-end; /* Align bottom for floor */
            position: relative;
            background: transparent !important; /* Strictly Transparent */
            perspective: 1200px; 
            padding-bottom: 80px;
            cursor: default;
        }
        
        .platform-container {
            position: relative; width: 100%; max-width: 500px; height: 500px;
            display: flex; align-items: flex-end; justify-content: center;
            transform-style: preserve-3d;
            cursor: pointer; /* Interaction hint */
        }
        
        /* 2. Floating Visual */
        .platform-card {
            position: absolute; width: 100%; height: 100%;
            display: flex; align-items: flex-end; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: translateY(20px); /* Start slightly down */
            z-index: 10;
        }
        .platform-card.active {
            opacity: 1; pointer-events: auto;
            transform: translateY(0);
        }
        
        .platform-img {
            max-width: 100%; max-height: 450px;
            object-fit: contain;
            filter: drop-shadow(0 30px 60px rgba(0,0,0,0.6)); /* Strong shadow */
            animation: floatIdle 6s ease-in-out infinite; /* Floating Effect */
        }
        @keyframes floatIdle { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        
        /* 1. Glass Floor */
        .glass-floor {
            position: absolute; bottom: -50px;
            width: 140%; height: 380px; /* Reduced width to prevent text overlap */
            background: radial-gradient(circle at center, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.02) 50%, transparent 85%);
            border-top: 1px solid rgba(255,255,255,0.25);
            border-radius: 50%;
            transform: rotateX(75deg) translateY(80px);
            box-shadow: 
                0 0 100px rgba(14, 165, 233, 0.2),
                inset 0 30px 60px rgba(255,255,255,0.06);
            z-index: 5; pointer-events: none;
        }
        
        /* Nav Dots */
        .pf-nav {
            position: absolute; bottom: 0; left: 0; width: 100%; display: flex; justify-content: center; gap: 10px; z-index: 50;
        }
        .pf-dot {
            width: 40px; height: 3px; background: rgba(255,255,255,0.1); cursor: pointer; transition: 0.3s;
        }
        .pf-dot.active { background: var(--gold-1); width: 60px; box-shadow: 0 0 10px var(--gold-1); }

        /* CTAs */
        .actions { display: flex; gap: 20px; flex-wrap: wrap; }
        .btn-gold {
            background: linear-gradient(90deg, var(--gold-3), var(--gold-1));
            color: #000; padding: 15px 40px; font-weight: 700; border-radius: 50px;
            text-decoration: none; display: inline-flex; align-items: center; gap: 10px;
            transition: 0.3s; box-shadow: 0 0 20px rgba(191, 149, 63, 0.3);
        }
        .btn-gold:hover { transform: scale(1.05); background: #fff; }
        .btn-glass {
            background: transparent; color: #fff; padding: 15px 40px; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.2); border-radius: 50px;
            text-decoration: none; transition: 0.3s;
        }
        .btn-glass:hover { border-color: #fff; background: rgba(255,255,255,0.05); }

        /* SECTIONS: Tabs & Content */
        .content-section { padding: 50px 0; background: transparent; /* REMOVED border to avoid split look */ }
        .tabs-header { display: flex; gap: 30px; margin-bottom: 40px; justify-content: center; }
        .tab-btn {
            background: transparent; border: none; color: var(--text-muted); font-size: 1.2rem;
            padding-bottom: 10px; cursor: pointer; position: relative; font-family: 'Cairo', sans-serif;
            transition: 0.3s;
        }
        .tab-btn.active { color: var(--gold-1); }
        .tab-btn.active::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px;
            background: var(--gold-1);
        }
        
        .tab-content { display: none; animation: fadeIn 0.5s ease; }
        .tab-content.active { display: block; }

        /* SPARE PARTS GRID */
        .parts-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;
        }
        .part-card {
            background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border);
            border-radius: 12px; padding: 20px; text-align: center; transition: 0.3s;
        }
        .part-card:hover { border-color: var(--neon-blue); transform: translateY(-3px); }
        .part-icon { font-size: 2rem; color: var(--text-muted); margin-bottom: 15px; }
        .part-name { color: #fff; font-weight: 600; margin-bottom: 5px; }
        .part-code { color: var(--neon-blue); font-size: 0.8rem; font-family: monospace; }

        /* FAULTS ACCORDION */
        .fault-item {
            background: rgba(255,255,255,0.02); margin-bottom: 10px; border-radius: 8px;
            overflow: hidden; border: 1px solid transparent;
        }
        .fault-header {
            padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between;
            align-items: center; background: rgba(255,255,255,0.03);
        }
        .fault-header:hover { background: rgba(255,255,255,0.05); }
        .fault-body { padding: 20px; display: none; color: var(--text-muted); line-height: 1.6; }
        .fault-item.active { border-color: var(--gold-1); }
        .fault-item.active .fault-body { display: block; }
        .fault-code { color: #ef4444; font-weight: 700; margin-left: 10px; }

        @media (max-width: 1024px) {
            .hero-container { grid-template-columns: 1fr; text-align: center; }
            .product-desc { margin: 0 auto 40px; border-right: none; border-bottom: 3px solid var(--gold-1); padding-bottom: 10px; }
            .visual-stage { height: 350px; order: -1; }
            .product-title { font-size: 2.5rem; }
            .actions { justify-content: center; }
            .specs-grid { justify-content: center; }
        }
        /* DATA EXPANDED GRID STYLE */
        .specs-expanded-container {
            width: 100%;
            background: rgba(255,255,255,0.01);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 30px;
            /* Fixed Height Scroll */
            max-height: 550px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--gold-1) rgba(255,255,255,0.05);
        }
        .specs-expanded-container::-webkit-scrollbar { width: 6px; }
        .specs-expanded-container::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .specs-expanded-container::-webkit-scrollbar-thumb { background: var(--gold-1); border-radius: 10px; }
        
        /* TECH DATA BOXES (Modern & Tight) */
        .specs-grid-layout {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Responsive Boxes */
            gap: 15px;
        }
        
        .spec-row-item {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);
            border-radius: 8px; padding: 12px 15px;
            display: flex; flex-direction: column; gap: 4px;
            transition: 0.2s; border-right: 2px solid transparent;
        }
        .spec-row-item:hover { 
            background: rgba(255,255,255,0.06); 
            border-color: rgba(212, 175, 55, 0.3);
            border-right-color: var(--gold-1);
            transform: translateY(-2px);
        }
        
        .spec-key { 
            color: var(--text-muted); font-size: 0.8rem; font-weight: 500; 
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .spec-val-expanded { 
            color: #fff; font-size: 1rem; font-weight: 700; 
            text-align: right; padding: 0; width: 100%;
            font-family: 'Cairo', sans-serif; /* Back to standard font for readability */
        }
        
        .spec-section-header {
            grid-column: 1 / -1; 
            margin: 25px 0 10px; padding-bottom: 5px;
            color: var(--gold-2); font-size: 1.1rem; font-weight: 700;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            display: flex; align-items: center; gap: 10px;
        }
        .spec-section-header::before {
            content: ''; display: inline-block; width: 8px; height: 8px; background: var(--gold-1); transform: rotate(45deg);
        }
        
        
        /* SPECS MODAL & TRIGGER */
        .btn-specs-trigger {
            width: 100%; padding: 1.5rem; margin-bottom: 2rem;
            background: rgba(15, 23, 42, 0.6); border: 1px dashed var(--gold-1);
            border-radius: 16px; color: var(--gold-2); font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-specs-trigger:hover {
            background: rgba(212, 175, 55, 0.1); border-style: solid; box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            transform: translateY(-3px);
        }
        
        .specs-modal {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: 0.3s; padding: 20px;
        }
        .specs-modal.active { opacity: 1; pointer-events: all; }
        
        .specs-modal-content {
            background: rgba(15, 23, 42, 0.95); border: 1px solid var(--gold-1);
            padding: 3rem; border-radius: 20px; 
            width: 98%; max-width: 1850px; /* Ultra-Wide */
            position: relative; transform: scale(0.9); transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 80px rgba(0,0,0,0.8);
            max-height: 95vh; overflow-y: auto;
            backdrop-filter: blur(25px);
        }
        .specs-modal.active .specs-modal-content { transform: scale(1); }
        
        .close-modal-btn {
            position: absolute; top: 25px; left: 25px;
            background: rgba(255,255,255,0.05); width: 45px; height: 45px; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.1); color: var(--text-muted);
            font-size: 1.4rem; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: center;
            z-index: 10;
        }
        .close-modal-btn:hover { background: var(--danger, #ff4444); color: #fff; border-color: transparent; transform: rotate(90deg); }

        /* Advanced Layout Inside Modal */
        /* Updated Masonry and Card styles */
        .specs-masonry {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Fixed 3 columns for that dashboard look on large screens */
            gap: 25px;
            padding-top: 20px;
            direction: rtl;
        }
        
        @media (max-width: 1400px) { .specs-masonry { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 900px) { .specs-masonry { grid-template-columns: 1fr; } }

        .spec-row-item {
            display: flex; flex-direction: column; 
            justify-content: flex-start; align-items: flex-start; 
            background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px;
            border: 1px solid transparent; transition: 0.2s;
            min-height: 100px;
            direction: rtl; gap: 8px;
        }

        .spec-row-item:hover { background: rgba(255,255,255,0.06); border-color: rgba(212, 175, 55, 0.5); transform: translateY(-3px); }
        
        .spec-key { 
            color: #fff; font-weight: 700; font-size: 0.9rem; /* Slightly smaller to fit */
            display: flex; align-items: center; gap: 8px; 
            text-align: right; width: 100%;
            border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 5px;
            white-space: nowrap; /* Force 1 line */
            overflow: hidden; text-overflow: ellipsis; /* Handle overflow gracefully */
        }
        .spec-val-expanded { color: #fff; font-size: 1.15rem; font-weight:600; text-align:right; line-height: 1.6; padding-right: 32px; }
        
        .spec-section-header { 
            grid-column: 1 / -1; 
            margin-top: 40px; margin-bottom: 20px; 
            color: var(--neon-blue); font-size: 1.6rem; font-weight:800; 
            display: flex; align-items: center; gap: 15px;
        }

        @media (max-width: 768px) {
            .specs-grid-layout { grid-template-columns: 1fr; }
            .specs-list-layout { grid-template-columns: 1fr; } 
        }

        .specs-masonry {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); /* Wide Cards */
            gap: 25px;
            padding-top: 20px;
            direction: rtl; /* Right to Left */
        }
        .spec-category-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 25px;
            /* height: fit-content; REMOVED to allow equal height scaling */
            height: 100%; 
            display: flex; flex-direction: column;
            transition: 0.3s;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .spec-category-card:hover { border-color: var(--gold-1); background: rgba(255, 255, 255, 0.05); transform: translateY(-2px); }
        
        .category-title {
            font-size: 1.2rem; color: var(--gold-1); font-weight: 800;
            margin-bottom: 20px; padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex; align-items: center; gap: 10px;
            width: 100%;
            text-align: right; direction: rtl;
        }
        
        .category-items-grid {
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 12px;
            flex-grow: 1; /* Fill space */
        }
        /* 2 columns inside card for desktop */
        @media (min-width: 992px) {
                .category-items-grid { grid-template-columns: 1fr 1fr; }
        }

        .spec-val-expanded { 
            color: var(--text-muted); font-weight: 500; font-size: 0.95rem; 
            text-align: right; /* Force Right Align */
            direction: rtl; /* Force RTL Reading */
            width: 100%;
            line-height: 1.5;
        }

        /* Icons */
        .spec-key i { margin-left: 0; margin-right: 0; color: var(--gold-3); }

        @media (max-width: 768px) {
            .specs-masonry { grid-template-columns: 1fr; }
            
            /* Hero Section Mobile */
            .hero { padding: 100px 4% 30px; min-height: auto; }
            .hero-container { gap: 40px; }
            .product-title { font-size: 1.8rem; }
            .product-desc { font-size: 1rem; padding-right: 15px; }
            .category-badge { font-size: 0.7rem; padding: 5px 12px; }
            
            /* Specs Cards Mobile */
            .specs-grid { 
                display: grid; 
                grid-template-columns: repeat(2, 1fr); 
                gap: 10px; 
                overflow-x: visible;
                flex-wrap: wrap;
            }
            .spec-card { 
                min-width: unset; 
                width: 100%; 
                padding: 12px 10px; 
                text-align: center;
            }
            .spec-label { font-size: 0.65rem; white-space: normal; }
            .spec-val { font-size: 0.85rem; white-space: normal; word-break: break-word; }
            
            /* Actions Mobile */
            .actions { flex-direction: column; gap: 12px; }
            .btn-gold, .btn-glass { width: 100%; justify-content: center; padding: 14px 20px; font-size: 0.95rem; }
            
            /* Visual Stage Mobile */
            .visual-stage { height: 320px !important; }
            .platform-stage { height: 350px; padding-bottom: 30px; }
            .platform-container { max-width: 95%; height: 300px; }
            .platform-img { max-height: 250px; }
            .glass-floor { 
                width: 130%; 
                height: 280px; 
                bottom: -30px;
                transform: rotateX(75deg) translateY(100px);
            }
            
            /* Nav Back Mobile */
            .nav-back { top: 80px; left: 4%; font-size: 0.8rem; }
            
            /* Tabs Mobile */
            .tabs-header { gap: 15px; flex-wrap: wrap; }
            .tab-btn { font-size: 1rem; }
            
            /* Parts Grid Mobile */
            .parts-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .part-card { padding: 15px; }
            .part-icon { font-size: 1.5rem; }
            .part-name { font-size: 0.9rem; }
            
            /* Modal Mobile */
            .specs-modal-content { padding: 1.5rem; max-width: 98%; }
            .close-modal-btn { top: 15px; left: 15px; width: 40px; height: 40px; font-size: 1.2rem; }
            .btn-specs-trigger { padding: 1rem; font-size: 1rem; }
        }
        
        @media (max-width: 480px) {
            /* Extra Small Screens */
            .hero { padding: 90px 3% 20px; }
            .product-title { font-size: 1.5rem; line-height: 1.3; }
            .product-desc { font-size: 0.9rem; line-height: 1.6; }
            
            /* Specs Single Column */
            .specs-grid { flex-direction: column; }
            .spec-card { width: 100%; }
            
            /* Visual Smaller */
            .visual-stage { height: 220px !important; }
            .platform-stage { height: 320px; }
            .platform-container { height: 280px; }
            .platform-img { max-height: 240px; }
            
            /* Parts Single Column */
            .parts-grid { grid-template-columns: 1fr; }
            
            /* Tabs Stacked */
            .tabs-header { flex-direction: column; gap: 10px; align-items: center; }
            
            /* Content Section */
            .content-section { padding: 30px 0; }
            
            /* Fault Items */
            .fault-header { padding: 12px 15px; font-size: 0.9rem; }
            .fault-body { padding: 15px; font-size: 0.9rem; }
        }
    </style>
<?php
$extraHead = ob_get_clean();

// --- MAIN CONTENT ---
ob_start();
?>
    <!-- LOADER -->
    <div id="page-loader"><div class="loader-spinner"></div></div>

    <!-- Background Orbs - Matching products.php -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <a href="<?= $baseUrl ?>/products" class="nav-back">
        عودة للمنتجات <i class="fas fa-arrow-left"></i> 
    </a>

    <!-- MAIN PRODUCT HERO -->
    <div class="hero">
        <div class="hero-container">
            
            <!-- RIGHT: INFO (RTL) -->
            <div class="info-stage">
                <span class="category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="product-desc">
                    <?= nl2br(htmlspecialchars(strip_tags($product['description']))) ?>
                </p>

                <!-- HOLOGRAPHIC SPECS -->
                <div class="specs-grid">
                    <?php 
                        // 1. Model
                        $model = $product['model'] ?? 'Standard';
                        
                        // Parse specs text to find keywords
                        $rawSpecs = explode("\n", $product['specifications'] ?? '');
                        
                        $speed = null;
                        $resolution = null;
                        $time = null;
                        
                        // Helpers to clean values and extract core numbers + units
                        function extractCoreValue($val, $type) {
                            $val = explode(':', $val);
                            $text = trim(end($val));
                            
                            // Speed: Extract number
                            if($type === 'speed') {
                                if(preg_match('/(\d+)/', $text, $matches)) {
                                    return $matches[1] . ' ورقة/دقيقة';
                                }
                                return $text; // Fallback
                            }
                            
                            // Resolution: Extract like 1200 x 1200
                            if($type === 'resolution') {
                                if(preg_match('/(\d+\s*[xX*]\s*\d+)/', $text, $matches)) {
                                    return $matches[1] . ' dpi';
                                }
                                return '1200 x 1200 dpi'; // Default high quality
                            }
                            
                            return $text;
                        }
                        
                        foreach($rawSpecs as $line) {
                            $line = trim($line);
                            if(empty($line)) continue;
                            
                            // Check keywords
                            if(!$speed && (strpos($line, 'سرعة') !== false || stripos($line, 'Speed') !== false)) {
                                $speed = extractCoreValue($line, 'speed');
                            }
                            elseif(!$resolution && (strpos($line, 'دقة') !== false || stripos($line, 'Resolution') !== false || stripos($line, 'dpi') !== false)) {
                                $resolution = extractCoreValue($line, 'resolution');
                            }
                            elseif(!$time && (strpos($line, 'زمن') !== false || strpos($line, 'وقت') !== false || strpos($line, 'خروج') !== false || stripos($line, 'Time') !== false)) {
                                // Try to find two numbers
                                if(preg_match_all('/(\d+\.?\d*)/', $line, $matches)) {
                                    $nums = $matches[0];
                                    if(count($nums) >= 2) {
                                        $time = [
                                            'c' => $nums[0], 
                                            'b' => $nums[1]
                                        ];
                                    } else {
                                        $time = $nums[0] . ' ثانية';
                                    }
                                } else {
                                    $time = extractCoreValue($line, 'time');
                                }
                            }
                        }
                        
                        // Fallback
                        $remainLines = array_values(array_filter($rawSpecs));
                        if(!$speed && isset($remainLines[0])) $speed = extractCoreValue($remainLines[0], 'speed');
                        // Defaults if empty
                        if(!$speed) $speed = 'N/A';
                        if(!$resolution) $resolution = '1200 x 1200 dpi';
                    ?>

                    <!-- Card 1: Model -->
                    <div class="spec-card">
                        <span class="spec-label">الموديل</span>
                        <span class="spec-val"><?= $model ?></span>
                    </div>

                    <!-- Card 2: Speed -->
                    <div class="spec-card">
                        <span class="spec-label">السرعة</span>
                        <span class="spec-val"><?= $speed ?></span>
                    </div>

                    <!-- Card 3: Resolution -->
                    <div class="spec-card">
                        <span class="spec-label">الدقة</span>
                        <span class="spec-val" dir="ltr"><?= $resolution ?></span>
                    </div>

                    <!-- Card 4: Output Time -->
                    <?php if($time): ?>
                    <div class="spec-card">
                        <span class="spec-label">زمن الإخراج</span>
                        <span class="spec-val">
                            <?php if(is_array($time)): ?>
                                <span style="font-size: 0.9em; display:flex; align-items:center; gap:5px; justify-content:center;">
                                    <span style="color:#0ea5e9;">●</span> <?= $time['c'] ?>s 
                                    <span style="color:#000; margin-right:5px;">●</span> <?= $time['b'] ?>s
                                </span>
                            <?php else: ?>
                                <?= $time ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="actions">
                    <a href="<?= $baseUrl ?>/contact?product=<?= $product['slug'] ?>" class="btn-gold">
                        <i class="fas fa-paper-plane"></i> طلب عرض سعر
                    </a>
                    <a href="#" class="btn-glass" onclick="toggleFullSpecsModal(true); return false;">
                        <i class="fas fa-list-ul"></i> المواصفات الكاملة
                    </a>
                </div>
            </div>

            <!-- LEFT: VISUAL (Z-Depth Infinity) -->
            <div class="visual-stage" style="height: auto; width: 100%; align-self: flex-start; margin-bottom: 50px;">
                <?php 
                    // COMBINE IMAGES: Main Image + Gallery Images
                    $displayImages = [];
                    
                    // 1. Add Main Image (Check 'thumbnail' first as per Admin Controller, then 'image' just in case)
                    if (!empty($product['thumbnail'])) {
                        $displayImages[] = ['image_path' => $product['thumbnail']];
                    } elseif (!empty($product['image'])) {
                        $displayImages[] = ['image_path' => $product['image']];
                    }
                    
                    // 2. Add Gallery Images
                    if (!empty($images)) {
                        foreach($images as $gImg) {
                            $displayImages[] = ['image_path' => $gImg['image_path']];
                        }
                    }
                    
                    // 3. Fallback
                    if (empty($displayImages)) {
                        $displayImages[] = ['image_path' => 'default.png'];
                    }
                    
                    // Deduplicate
                    $uniqueImages = [];
                    $seenPaths = [];
                    foreach($displayImages as $itm) {
                        if(!in_array($itm['image_path'], $seenPaths)){
                            $seenPaths[] = $itm['image_path'];
                            $uniqueImages[] = $itm;
                        }
                    }
                    $displayImages = $uniqueImages;
                ?>
                <div class="platform-stage">
                    <div class="platform-container" onclick="nextPlatformImage()">
                        <!-- 1. The Glass Floor -->
                        <div class="glass-floor"></div>
                        
                        <!-- 2. The Floating Visuals -->
                        <?php foreach($displayImages as $idx => $img): 
                             // DIRECT PATH LOGIC (Bypassing storage_proxy to fix transparency issues)
                             $imgRaw = $img['image_path'];
                             $imgUrl = $baseUrl . '/storage/uploads/' . $imgRaw;
                             
                             if (!empty($imgRaw) && strpos($imgRaw, 'product') === false && strpos($imgRaw, 'default') === false) {
                                  if (strpos($imgRaw, 'products/') !== 0) {
                                      $imgUrl = $baseUrl . '/storage/uploads/products/' . $imgRaw;
                                  }
                             }
                        ?>
                        <div class="platform-card <?= $idx===0?'active':'' ?>" data-idx="<?= $idx ?>">
                             <img src="<?= $imgUrl ?>" class="platform-img">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- TABS SECTION -->
    <?php if (($product['show_spare_parts'] ?? false) || ($product['show_faults'] ?? false)): ?>
    <section class="content-section">
        <div class="hero-container" style="display: block;"> <!-- Re-use container width -->
            
            <div class="tabs-header">
                <?php if ($product['show_spare_parts']): ?>
                <button class="tab-btn active" onclick="switchTab('parts')" id="btn-parts">قطع الغيار</button>
                <?php endif; ?>

                <?php if ($product['show_faults']): ?>
                <button class="tab-btn <?= !$product['show_spare_parts'] ? 'active' : '' ?>" onclick="switchTab('faults')" id="btn-faults">الأعطال الشائعة</button>
                <?php endif; ?>
            </div>
            
            <!-- TAB: SPARE PARTS -->
            <?php if ($product['show_spare_parts']): ?>
            <div id="tab-parts" class="tab-content active">
                <?php if (!empty($spareParts)): ?>
                    <div class="parts-grid">
                    <?php foreach ($spareParts as $part): ?>
                        <div class="part-card">
                            <div class="part-icon"><i class="fas fa-cogs"></i></div>
                            <div class="part-name"><?= htmlspecialchars($part['name']) ?></div>
                            <div class="part-code"><?= htmlspecialchars($part['part_number']) ?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 40px;">
                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                        <p>لا توجد قطع غيار مسجلة لهذا المنتج حالياً.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- TAB: FAULTS -->
            <?php if ($product['show_faults']): ?>
            <div id="tab-faults" class="tab-content <?= !$product['show_spare_parts'] ? 'active' : '' ?>">
                <?php if (!empty($faults)): ?>
                    <div style="max-width: 800px; margin: 0 auto;">
                    <?php foreach ($faults as $fault): ?>
                        <div class="fault-item" onclick="this.classList.toggle('active')">
                            <div class="fault-header">
                                <div>
                                    <?= htmlspecialchars($fault['title']) ?>
                                    <?php if ($fault['error_code']): ?>
                                        <span class="fault-code"><?= htmlspecialchars($fault['error_code']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="fault-body">
                                <strong>المشكلة:</strong> <?= htmlspecialchars($fault['description']) ?><br><br>
                                <strong style="color: var(--gold-1);">الحل المقترح:</strong><br>
                                <?= nl2br(htmlspecialchars($fault['solution'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 40px;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5; color: var(--emerald-500);"></i>
                        <p>لا توجد أعطال شائعة مسجلة لهذا المنتج.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <!-- FULL SPECS MODAL -->
    <div id="fullSpecsModal" class="specs-modal">
        <div class="specs-modal-content">
            <button class="close-modal-btn" onclick="toggleFullSpecsModal(false)">×</button>
            <h3 style="color:var(--gold-1); margin-bottom:1.5rem; text-align:center; font-size:1.5rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:15px;">المواصفات الفنية الكاملة</h3>
            
            <div class="specs-expanded-container">
                <?php if (!empty($product['specifications'])): ?>
                    <?php 
                        $allSpecs = explode("\n", $product['specifications']);
                        
                        // ICON MAPPER FOR ITEMS
                        function getSpecIcon($text) {
                            $t = $text; // mb_stripos handles case insensitivity automatically
                            
                            // Performance & Specs
                            if(mb_stripos($t, 'سرعة')!==false || mb_stripos($t, 'speed')!==false || mb_stripos($t, 'cpm')!==false || mb_stripos($t, 'ppm')!==false) return 'fa-tachometer-alt';
                            if(mb_stripos($t, 'دقة')!==false || mb_stripos($t, 'resolution')!==false || mb_stripos($t, 'dpi')!==false) return 'fa-crosshairs';
                            if(mb_stripos($t, 'ذاكرة')!==false || mb_stripos($t, 'memory')!==false || mb_stripos($t, 'ram')!==false) return 'fa-microchip';
                            if(mb_stripos($t, 'معالج')!==false || mb_stripos($t, 'processor')!==false || mb_stripos($t, 'cpu')!==false) return 'fa-server';
                            if(mb_stripos($t, 'تخزين')!==false || mb_stripos($t, 'storage')!==false || mb_stripos($t, 'hdd')!==false || mb_stripos($t, 'ssd')!==false || mb_stripos($t, 'hard')!==false || mb_stripos($t, 'قرص')!==false) return 'fa-hdd';
                            if(mb_stripos($t, 'دورة')!==false || mb_stripos($t, 'cycle')!==false || mb_stripos($t, 'duty')!==false) return 'fa-sync';
                            
                            // Handling
                            if(mb_stripos($t, 'ورق')!==false || mb_stripos($t, 'paper')!==false || mb_stripos($t, 'cassette')!==false || mb_stripos($t, 'tray')!==false || mb_stripos($t, 'درج')!==false || mb_stripos($t, 'handling')!==false) return 'fa-copy';
                            if(mb_stripos($t, 'وزن')!==false || mb_stripos($t, 'weight')!==false) return 'fa-weight-hanging';
                            if(mb_stripos($t, 'ابعاد')!==false || mb_stripos($t, 'dimension')!==false || mb_stripos($t, 'size')!==false || mb_stripos($t, 'مقاس')!==false) return 'fa-ruler-combined';
                            if(mb_stripos($t, 'وجهين')!==false || mb_stripos($t, 'duplex')!==false) return 'fa-book-open';
                            if(mb_stripos($t, 'سعة')!==false || mb_stripos($t, 'capacity')!==false || mb_stripos($t, 'volume')!==false) return 'fa-box-open';
                            
                            // Hardware & Display
                            if(mb_stripos($t, 'شاشة')!==false || mb_stripos($t, 'panel')!==false || mb_stripos($t, 'display')!==false || mb_stripos($t, 'screen')!==false || mb_stripos($t, 'lcd')!==false) return 'fa-tv';
                            if(mb_stripos($t, 'طاقة')!==false || mb_stripos($t, 'power')!==false || mb_stripos($t, 'voltage')!==false || mb_stripos($t, 'consumption')!==false || mb_stripos($t, 'electric')!==false) return 'fa-bolt';
                            if(mb_stripos($t, 'احبار')!==false || mb_stripos($t, 'toner')!==false || mb_stripos($t, 'cartridge')!==false || mb_stripos($t, 'color')!==false || mb_stripos($t, 'الوان')!==false || mb_stripos($t, 'ink')!==false) return 'fa-palette';
                            
                            // Connectivity
                            if(mb_stripos($t, 'شبكة')!==false || mb_stripos($t, 'network')!==false || mb_stripos($t, 'ethernet')!==false || mb_stripos($t, 'lan')!==false) return 'fa-network-wired';
                            if(mb_stripos($t, 'wifi')!==false || mb_stripos($t, 'wi-fi')!==false || mb_stripos($t, 'wireless')!==false) return 'fa-wifi';
                            if(mb_stripos($t, 'usb')!==false) return 'fa-usb';
                            if(mb_stripos($t, 'bluetooth')!==false) return 'fa-bluetooth-b';
                            if(mb_stripos($t, 'فاكس')!==false || mb_stripos($t, 'fax')!==false) return 'fa-fax';
                            if(mb_stripos($t, 'مسح')!==false || mb_stripos($t, 'scan')!==false) return 'fa-camera';
                            
                            // Misc
                            if(mb_stripos($t, 'نظام')!==false || mb_stripos($t, 'system')!==false || mb_stripos($t, 'os')!==false) return 'fa-desktop';
                            if(mb_stripos($t, 'وقت')!==false || mb_stripos($t, 'time')!==false || mb_stripos($t, 'warm')!==false || mb_stripos($t, 'zaman')!==false || mb_stripos($t, 'زمن')!==false) return 'fa-stopwatch';
                            
                            if(mb_stripos($t, 'copy')!==false || mb_stripos($t, 'نسخ')!==false || mb_stripos($t, 'تصوير')!==false) return 'fa-copy';

                            return 'fa-check-circle';
                        }
                        
                        // NEW ICON MAPPER FOR CATEGORIES (The Card Titles)
                        function getCategoryIcon($title) {
                            $t = $title;
                            if(mb_stripos($t, 'ورق')!==false || mb_stripos($t, 'paper')!==false) return 'fa-copy';
                            if(mb_stripos($t, 'طباعة')!==false || mb_stripos($t, 'print')!==false || mb_stripos($t, 'أداء')!==false) return 'fa-print';
                            if(mb_stripos($t, 'مسح')!==false || mb_stripos($t, 'scan')!==false || mb_stripos($t, 'فاكس')!==false) return 'fa-file-alt';
                            if(mb_stripos($t, 'تقنية')!==false || mb_stripos($t, 'tech')!==false || mb_stripos($t, 'hardware')!==false || mb_stripos($t, 'مواصفات')!==false) return 'fa-microchip';
                            if(mb_stripos($t, 'اتصال')!==false || mb_stripos($t, 'connect')!==false || mb_stripos($t, 'شبكة')!==false) return 'fa-wifi';
                            if(mb_stripos($t, 'نظام')!==false || mb_stripos($t, 'system')!==false || mb_stripos($t, 'برامج')!==false) return 'fa-laptop-code';
                            if(mb_stripos($t, 'عام')!==false || mb_stripos($t, 'general')!==false) return 'fa-info-circle';
                            return 'fa-th-large';
                        }

                        // 1. PARSE INTO GROUPS (Moved Logic Here correctly)
                        $groups = [];
                        $currentGroup = 'المواصفات العامة'; // Default group
                        $groups[$currentGroup] = [];
                        
                        $forceItems = ['ethernet', 'usb', 'wi-fi', 'wifi', 'ieee', 'bluetooth', 'network', 'ram', 'memory', 'hdd', 'speed', 'resolution', 'dpi', 'ppm', 'weight', 'dimension', 'b/g/n', 'protocol', 'ipv4', 'ipv6', 'tcp/ip'];

                        foreach($allSpecs as $line) {
                            $line = trim($line);
                            if(!$line) continue;
                            if($line === 'المواصفات الفنية') continue;

                            // Detection Logic
                            $isHeader = true;
                            $tLine = $line;
                            
                            // Check Force Items
                            foreach($forceItems as $k) {
                                if(stripos($tLine, $k) !== false) {
                                    $isHeader = false;
                                    break;
                                }
                            }
                            
                            if($isHeader) {
                                if (strpos($tLine, ':') !== false) $isHeader = false;
                                elseif (preg_match('/[:\-–—]$/u', $tLine)) $isHeader = false;
                                elseif (strpos($tLine, ' - ') !== false) $isHeader = false;
                            }

                            if($isHeader) {
                                $currentGroup = trim(preg_replace('/^[\d\-\.]+\s+/', '', $line)); // Remove leading numbers
                                if(!isset($groups[$currentGroup])) $groups[$currentGroup] = [];
                            } else {
                                // Parse Item
                                $label = $tLine;
                                $value = '';
                                $cleanLine = preg_replace('/[–—]/u', '-', $tLine);
                                
                                if(strpos($cleanLine, ':') !== false) {
                                    $parts = explode(':', $cleanLine, 2);
                                    $label = trim($parts[0]);
                                    $value = trim($parts[1]);
                                } elseif(strpos($cleanLine, ' - ') !== false) {
                                    $parts = explode(' - ', $cleanLine, 2);
                                    $label = trim($parts[0]);
                                    $value = trim($parts[1]);
                                } elseif(substr($cleanLine, -1) === '-') {
                                    $label = trim(substr($cleanLine, 0, -1));
                                    $value = '<i class="fas fa-check" style="color:var(--neon-blue);"></i>'; 
                                } else {
                                    $label = $cleanLine;
                                }
                                
                                if($value==='' && $label==='') continue;
                                
                                // Remove leading dashes from label
                                $label = ltrim($label, '- ');

                                $groups[$currentGroup][] = [
                                    'label' => $label,
                                    'value' => $value,
                                    'icon' => getSpecIcon($label)
                                ];
                            }
                        }
                    ?>

                    <div class="specs-masonry">
                        <?php foreach($groups as $title => $items): if(empty($items)) continue; ?>
                        <div class="spec-category-card">
                            <div class="category-title">
                                <i class="fas <?= getCategoryIcon($title) ?>"></i> <?= $title ?>
                            </div>
                            <div class="category-items-grid">
                            <?php foreach($items as $item): ?>
                                <div class="spec-row-item">
                                    <div class="spec-key">
                                        <i class="fas <?= $item['icon'] ?>" style="color:var(--gold-3); width:18px; text-align:center;"></i>
                                        <span><?= $item['label'] ?></span>
                                    </div>
                                    <div class="spec-val-expanded"><?= $item['value'] ?></div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>


                <?php else: ?>
                    <p style="text-align:center; color:#999;">لا توجد تفاصيل إضافية.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
$content = ob_get_clean();

// --- Extra Scripts ---
ob_start();
?>
    <script>
        // Loader
        window.addEventListener('load', () => {
            const l = document.getElementById('page-loader');
            if(l) {
                l.style.opacity = '0';
                setTimeout(() => l.style.visibility = 'hidden', 500);
            }
            
            // Intro Anim
            gsap.from('.visual-img', { y: 50, opacity: 0, duration: 1.5, ease: "power3.out" });
            gsap.from('.info-stage', { x: 50, opacity: 0, duration: 1.5, delay: 0.2, ease: "power3.out" });
        });

        // Tab Logic
        function switchTab(id) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            document.getElementById('tab-' + id).classList.add('active');
            event.target.classList.add('active');
        }

        // Gallery Logic
        function switchHeroImage(el, src) {
            const hero = document.getElementById('heroImage');
            
            // Fade Out
            gsap.to(hero, { opacity: 0, scale: 0.95, duration: 0.2, onComplete: () => {
                hero.src = src;
                // Fade In
                gsap.to(hero, { opacity: 1, scale: 1, duration: 0.3 });
            }});
            
            // Update Active Thumb
            document.querySelectorAll('.thumb-img').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }

        /* PLATFORM SWITCH LOGIC */
        let currentPlatformIdx = 0;
        
        function switchPlatform(idx) {
            const cards = document.querySelectorAll('.platform-card');
            // Remove previous active
            cards.forEach(c => c.classList.remove('active'));
            
            // Loop logic
            if(idx >= cards.length) idx = 0;
            if(idx < 0) idx = cards.length - 1;
            
            // Activate
            if(cards[idx]) {
                cards[idx].classList.add('active');
                currentPlatformIdx = idx;
                
                // GSAP pop effect for feedback
                gsap.fromTo(cards[idx].querySelector('img'), { scale: 0.95, opacity: 0.8 }, { scale: 1, opacity: 1, duration: 0.4, ease: "back.out(1.7)" });
            }
        }
        
        function nextPlatformImage() {
            const cards = document.querySelectorAll('.platform-card');
            if(cards.length > 1) {
                switchPlatform(currentPlatformIdx + 1);
            }
        }

        // === AUTO-ROTATE IMAGES ===
        let autoRotateInterval = null;
        const autoRotateDelay = 4000; // 4 seconds between images
        
        function startAutoRotate() {
            const cards = document.querySelectorAll('.platform-card');
            if (cards.length > 1 && !autoRotateInterval) {
                autoRotateInterval = setInterval(() => {
                    nextPlatformImage();
                }, autoRotateDelay);
            }
        }
        
        function stopAutoRotate() {
            if (autoRotateInterval) {
                clearInterval(autoRotateInterval);
                autoRotateInterval = null;
            }
        }
        
        // Start auto-rotate on page load
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRotate();
            
            // Pause on hover
            const platformContainer = document.querySelector('.platform-container');
            if (platformContainer) {
                platformContainer.addEventListener('mouseenter', stopAutoRotate);
                platformContainer.addEventListener('mouseleave', startAutoRotate);
            }
        });
        // === END AUTO-ROTATE ===

        function toggleFullSpecsModal(show) {
            const m = document.getElementById('fullSpecsModal');
            if(show) m.classList.add('active');
            else m.classList.remove('active');
        }
        document.getElementById('fullSpecsModal').addEventListener('click', function(e) {
            if(e.target === this) toggleFullSpecsModal(false);
        });
    </script>
<?php
$extraScripts = ob_get_clean();

// Include Layout
include VIEWS_PATH . '/layouts/public_layout.php';
?>

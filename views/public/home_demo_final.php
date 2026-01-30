<?php
/**
 * Demo: FINAL - Matching Admin Dashboard Identity
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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($companyName) ?> | وكيل ريكو المعتمد</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            /* Admin Dashboard Identity Colors */
            --bg-deep: #020617;
            --bg-dark: #0f172a;
            --bg-glass: rgba(30, 41, 59, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --neon-blue: #0ea5e9;
            --neon-glow: rgba(14, 165, 233, 0.3);
            /* Gold Gradient from Brand */
            --gold-1: #bf953f;
            --gold-2: #fcf6ba;
            --gold-3: #b38728;
            --gold-4: #fbf5b7;
            --gold-5: #aa771c;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-deep);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(14, 165, 233, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 20%);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        ::selection {
            background: var(--neon-blue);
            color: #000;
        }

        /* === ANIMATED BACKGROUND ORBS === */
        .bg-orbs {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            animation: orbFloat 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%);
            top: -200px; right: -100px;
        }

        .orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(191, 149, 63, 0.1), transparent 70%);
            bottom: -150px; left: -100px;
            animation-delay: -7s;
        }

        .orb-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08), transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -14s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* === NAVIGATION (Floating Glass Bar) === */
        .nav {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            width: 95%;
            max-width: 1400px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 0.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 2rem;
            filter: drop-shadow(0 0 10px rgba(14, 165, 233, 0.5));
            color: var(--neon-blue);
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.3rem;
            background: linear-gradient(to right, var(--gold-1), var(--gold-2), var(--gold-3), var(--gold-4), var(--gold-5));
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldShine 3s linear infinite;
        }

        @keyframes goldShine {
            to { background-position: 200% center; }
        }

        .brand-subtitle {
            font-size: 0.7rem;
            color: var(--neon-blue);
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .nav-cta {
            background: linear-gradient(135deg, var(--neon-blue), #3b82f6);
            color: #fff;
            padding: 10px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 0 15px var(--neon-glow);
            transition: all 0.3s;
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 25px var(--neon-glow);
        }

        /* === HERO SECTION === */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 150px 5% 100px;
            position: relative;
            z-index: 2;
        }

        .hero-container {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 3rem;
            align-items: center;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.2);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--neon-blue);
            margin-bottom: 2rem;
        }

        .hero-badge i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .hero-title {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 2rem;
            text-shadow: 0 0 40px rgba(14, 165, 233, 0.3);
            position: relative;
        }

        .hero-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: 0;
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, var(--gold-1), var(--gold-2), var(--neon-blue));
            border-radius: 2px;
            animation: expandLine 2s ease-out forwards;
        }

        @keyframes expandLine {
            from { width: 0; opacity: 0; }
            to { width: 120px; opacity: 1; }
        }

        .hero-title .gold-text {
            background: linear-gradient(to right, var(--gold-1), var(--gold-2), var(--gold-3), var(--gold-1));
            background-size: 300% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: goldShine 3s linear infinite;
            text-shadow: none;
            filter: drop-shadow(0 0 20px rgba(191, 149, 63, 0.4));
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-light);
            line-height: 1.9;
            margin-bottom: 2.5rem;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.05), rgba(191, 149, 63, 0.05));
            padding: 1.5rem;
            border-radius: 12px;
            border-right: 3px solid var(--neon-blue);
            animation: fadeSlideIn 1s ease-out 0.5s both;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-blue), #3b82f6);
            color: #fff;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px var(--neon-glow);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px var(--neon-glow);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold-1), var(--gold-3));
            color: #000;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px rgba(191, 149, 63, 0.3);
            transition: all 0.3s;
        }

        .btn-gold:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(191, 149, 63, 0.4);
        }

        /* === HERO VISUAL (Neon Image) === */
        .hero-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .neon-image-container {
            position: relative;
            width: 100%;
        }

        .neon-image {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 0 30px var(--neon-glow)) drop-shadow(0 0 60px rgba(191, 149, 63, 0.2));
            animation: neonPulse 3s ease-in-out infinite;
        }

        @keyframes neonPulse {
            0%, 100% { filter: drop-shadow(0 0 30px var(--neon-glow)) drop-shadow(0 0 60px rgba(191, 149, 63, 0.2)); }
            50% { filter: drop-shadow(0 0 50px var(--neon-glow)) drop-shadow(0 0 80px rgba(191, 149, 63, 0.3)); }
        }

        /* === PAPER FEEDER INPUT ANIMATION === */
        .paper-feeder {
            position: absolute;
            top: 10%;
            right: 35%;
            width: 120px;
            height: 80px;
            pointer-events: none;
            z-index: 8;
            overflow: visible;
            perspective: 500px;
        }

        .feeder-paper {
            position: absolute;
            right: 0;
            width: 70px;
            height: 80px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform-origin: center center;
            transform: rotateX(60deg);
        }

        /* Header stripe on paper */
        .feeder-paper::before {
            content: '';
            position: absolute;
            top: 6px;
            left: 5px;
            right: 5px;
            height: 4px;
            background: linear-gradient(90deg, #64748b, #94a3b8);
            border-radius: 1px;
        }

        /* Content lines */
        .feeder-paper::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 5px;
            width: 35px;
            height: 50px;
            background: repeating-linear-gradient(
                180deg,
                #d0d0d0 0px,
                #d0d0d0 2px,
                transparent 2px,
                transparent 8px
            );
        }

        .feeder-paper-1 {
            animation: feedPaperHorizontal 2.5s ease-in-out infinite;
        }

        .feeder-paper-2 {
            animation: feedPaperHorizontal 2.5s ease-in-out infinite;
            animation-delay: 0.9s;
        }

        .feeder-paper-3 {
            animation: feedPaperHorizontal 2.5s ease-in-out infinite;
            animation-delay: 1.8s;
        }

        /* Paper slides horizontally from right to left into the feeder */
        @keyframes feedPaperHorizontal {
            0% {
                opacity: 0;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(60px) translateY(0);
                clip-path: inset(0 0 0 0);
            }
            10% {
                opacity: 1;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(40px) translateY(0);
                clip-path: inset(0 0 0 0);
            }
            25% {
                opacity: 1;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(20px) translateY(2px);
                clip-path: inset(0 0 0 15%);
            }
            40% {
                opacity: 1;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-10px) translateY(4px);
                clip-path: inset(0 0 0 40%);
            }
            60% {
                opacity: 1;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-40px) translateY(6px);
                clip-path: inset(0 0 0 65%);
            }
            80% {
                opacity: 0.8;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-70px) translateY(8px);
                clip-path: inset(0 0 0 85%);
            }
            100% {
                opacity: 0;
                transform: rotateX(60deg) rotateZ(-40deg) rotateY(-15deg) translateX(-100px) translateY(10px);
                clip-path: inset(0 0 0 100%);
            }
        }

        /* === PROFESSIONAL PAPER OUTPUT ANIMATION === */
        .paper-output-area {
            position: absolute;
            bottom: 42%;
            left: 20%;
            width: 80px;
            height: 100px;
            pointer-events: none;
            z-index: 10;
        }

        .output-paper {
            position: absolute;
            width: 70px;
            height: 95px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%);
            border-radius: 2px;
            box-shadow: 
                0 4px 15px rgba(0, 0, 0, 0.2),
                0 0 20px rgba(14, 165, 233, 0.3);
            padding: 8px;
            opacity: 0;
        }

        /* Paper header - cyan stripe */
        .output-paper::before {
            content: '';
            position: absolute;
            top: 6px;
            left: 6px;
            right: 6px;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #22d3ee);
            border-radius: 2px;
        }

        /* Paper content lines */
        .output-paper .paper-lines {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .output-paper .paper-line {
            height: 2px;
            background: #d0d7de;
            border-radius: 1px;
        }

        .output-paper .paper-line:nth-child(1) { width: 90%; }
        .output-paper .paper-line:nth-child(2) { width: 75%; }
        .output-paper .paper-line:nth-child(3) { width: 85%; }
        .output-paper .paper-line:nth-child(4) { width: 60%; }
        .output-paper .paper-line:nth-child(5) { width: 80%; }

        /* Paper 1 - Cyan theme (Invoice) */
        .output-paper-1 {
            animation: slidePaperOut 5s ease-out infinite;
        }
        .output-paper-1::before {
            background: linear-gradient(90deg, #0ea5e9, #22d3ee);
        }
        .output-paper-1 .paper-line { background: #bae6fd; }

        /* Paper 2 - Gold theme (Contract) */
        .output-paper-2 {
            animation: slidePaperOut 5s ease-out infinite;
            animation-delay: 1s;
        }
        .output-paper-2::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        .output-paper-2 .paper-line { background: #fde68a; }

        /* Paper 3 - Green theme (Report) */
        .output-paper-3 {
            animation: slidePaperOut 5s ease-out infinite;
            animation-delay: 2s;
        }
        .output-paper-3::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }
        .output-paper-3 .paper-line { background: #a7f3d0; }

        /* Paper 4 - Purple theme (Document) */
        .output-paper-4 {
            animation: slidePaperOut 5s ease-out infinite;
            animation-delay: 3s;
        }
        .output-paper-4::before {
            background: linear-gradient(90deg, #8b5cf6, #a78bfa);
        }
        .output-paper-4 .paper-line { background: #ddd6fe; }

        /* Paper 5 - Pink theme (Certificate) */
        .output-paper-5 {
            animation: slidePaperOut 5s ease-out infinite;
            animation-delay: 4s;
        }
        .output-paper-5::before {
            background: linear-gradient(90deg, #ec4899, #f472b6);
        }
        .output-paper-5 .paper-line { background: #fbcfe8; }

        /* Simple professional slide animation */
        @keyframes slidePaperOut {
            0% {
                opacity: 0;
                transform: translateX(0) translateY(0) rotate(0deg);
            }
            10% {
                opacity: 1;
                transform: translateX(-20px) translateY(5px) rotate(-2deg);
            }
            30% {
                opacity: 1;
                transform: translateX(-50px) translateY(15px) rotate(-5deg);
            }
            50% {
                opacity: 1;
                transform: translateX(-80px) translateY(30px) rotate(-8deg);
            }
            70% {
                opacity: 0.8;
                transform: translateX(-100px) translateY(45px) rotate(-10deg);
            }
            100% {
                opacity: 0;
                transform: translateX(-120px) translateY(60px) rotate(-12deg);
            }
        }

        /* === STATS SECTION === */
        .stats-section {
            padding: 4rem 5%;
            position: relative;
            z-index: 2;
            background: linear-gradient(to bottom, transparent, rgba(15, 23, 42, 0.5));
        }

        .stats-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: rgba(14, 165, 233, 0.3);
            box-shadow: 0 0 25px var(--neon-glow);
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(to bottom, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 1024px) {
            .stats-container { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .stats-container { grid-template-columns: 1fr; }
        }

        /* === SERVICES SECTION === */
        .services-section {
            padding: 8rem 5%;
            position: relative;
            z-index: 2;
        }

        .section-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.2);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--neon-blue);
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 900;
        }

        .section-title .highlight {
            background: linear-gradient(to right, var(--gold-1), var(--gold-2), var(--gold-3));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .service-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 2px;
            background: linear-gradient(90deg, var(--neon-blue), var(--gold-1));
            transition: width 0.4s;
        }

        .service-card:hover {
            background: rgba(30, 41, 59, 0.7);
            border-color: rgba(14, 165, 233, 0.3);
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .service-card:hover::after {
            width: 100%;
        }

        .service-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--neon-blue), #3b82f6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px var(--neon-glow);
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .service-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* === CTA SECTION === */
        .cta-section {
            padding: 8rem 5%;
            position: relative;
            z-index: 2;
        }

        .cta-box {
            max-width: 900px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 58, 138, 0.6));
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 24px;
            padding: 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(14, 165, 233, 0.1);
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            animation: ctaShine 4s infinite;
        }

        @keyframes ctaShine {
            0% { left: -100%; }
            30%, 100% { left: 100%; }
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .cta-desc {
            color: var(--text-muted);
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        /* === FOOTER === */
        .footer {
            padding: 4rem 5%;
            border-top: 1px solid var(--glass-border);
            position: relative;
            z-index: 2;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-copy {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: var(--neon-blue);
        }

        /* === RESPONSIVE === */
        @media (max-width: 1024px) {
            .hero-container { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
            .nav-links { display: none; }
        }

        @media (max-width: 768px) {
            .hero { padding: 120px 5% 60px; }
            .hero-title { font-size: 2rem; }
            .cta-box { padding: 2.5rem; }
            .footer-container { flex-direction: column; gap: 1.5rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Background Orbs -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <a href="<?= BASE_URL ?>" class="logo">
            <i class="fas fa-crown logo-icon"></i>
            <div>
                <div class="brand-name"><?= htmlspecialchars($companyName) ?></div>
                <div class="brand-subtitle">RICOH AUTHORIZED</div>
            </div>
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
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-star"></i>
                    <span>حلول تقنية متميزة منذ <?= $stats['years'] ?? '10' ?> عاماً</span>
                </div>
                <h1 class="hero-title">
                    شريكك في <span class="gold-text">التميز</span> <br>
                    والابتكار التقني
                </h1>
                <p class="hero-desc">
                    نقدم لك أحدث حلول ريكو اليابانية مع خدمة محلية لا مثيل لها. 
                    من الصيانة الشاملة إلى قطع الغيار الأصلية، نحن هنا لضمان نجاحك.
                </p>
                <div class="hero-btns">
                    <a href="<?= BASE_URL ?>/maintenance" class="btn-primary">
                        <i class="fas fa-rocket"></i> ابدأ الآن
                    </a>
                    <a href="<?= BASE_URL ?>/products" class="btn-gold">
                        <i class="fas fa-boxes"></i> تصفح المنتجات
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
                        <!-- Paper 1 - Invoice (Cyan) -->
                        <div class="output-paper output-paper-1">
                            <div class="paper-lines">
                                <div class="paper-line" style="width: 90%"></div>
                                <div class="paper-line" style="width: 70%"></div>
                                <div class="paper-line" style="width: 85%"></div>
                                <div class="paper-line" style="width: 60%"></div>
                                <div class="paper-line" style="width: 75%"></div>
                            </div>
                        </div>
                        <!-- Paper 2 - Contract (Gold) -->
                        <div class="output-paper output-paper-2">
                            <div class="paper-lines">
                                <div class="paper-line" style="width: 100%"></div>
                                <div class="paper-line" style="width: 100%"></div>
                                <div class="paper-line" style="width: 80%"></div>
                                <div class="paper-line" style="width: 100%"></div>
                                <div class="paper-line" style="width: 50%"></div>
                            </div>
                        </div>
                        <!-- Paper 3 - Report (Green) -->
                        <div class="output-paper output-paper-3">
                            <div class="paper-lines">
                                <div class="paper-line" style="width: 60%"></div>
                                <div class="paper-line" style="width: 90%"></div>
                                <div class="paper-line" style="width: 75%"></div>
                                <div class="paper-line" style="width: 95%"></div>
                                <div class="paper-line" style="width: 40%"></div>
                            </div>
                        </div>
                        <!-- Paper 4 - Document (Purple) -->
                        <div class="output-paper output-paper-4">
                            <div class="paper-lines">
                                <div class="paper-line" style="width: 80%"></div>
                                <div class="paper-line" style="width: 65%"></div>
                                <div class="paper-line" style="width: 90%"></div>
                                <div class="paper-line" style="width: 70%"></div>
                                <div class="paper-line" style="width: 85%"></div>
                            </div>
                        </div>
                        <!-- Paper 5 - Certificate (Pink) -->
                        <div class="output-paper output-paper-5">
                            <div class="paper-lines">
                                <div class="paper-line" style="width: 50%"></div>
                                <div class="paper-line" style="width: 85%"></div>
                                <div class="paper-line" style="width: 65%"></div>
                                <div class="paper-line" style="width: 80%"></div>
                                <div class="paper-line" style="width: 55%"></div>
                            </div>
                        </div>
                    </div>
                    <img src="<?= BASE_URL ?>/storage/uploads/hero-neon.png" alt="Ricoh Machine" class="neon-image">
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?>. جميع الحقوق محفوظة.</div>
        </div>
    </footer>

    <!-- Animations -->
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Hero Animations
        gsap.from('.hero-badge', { opacity: 0, y: 30, duration: 1, delay: 0.3 });
        gsap.from('.hero-title', { opacity: 0, y: 40, duration: 1, delay: 0.5 });
        gsap.from('.hero-desc', { opacity: 0, y: 30, duration: 1, delay: 0.7 });
        gsap.from('.hero-btns', { opacity: 0, y: 30, duration: 1, delay: 0.9 });
        gsap.from('.hero-glass-card', { opacity: 0, x: 50, duration: 1.2, delay: 0.5 });

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

        // CTA Box
        gsap.from('.cta-box', {
            opacity: 0,
            scale: 0.95,
            duration: 1,
            scrollTrigger: {
                trigger: '.cta-section',
                start: 'top 70%'
            }
        });
    </script>
</body>
</html>

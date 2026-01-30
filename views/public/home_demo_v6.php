<?php
/**
 * الصفحة الرئيسية - النموذج السادس (The Bespoke Bento / Modern Luxury)
 * نظام المُوَفِّي - تصميم الشبكة الذكية
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المُوَفِّي | القمة في الأداء</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --bg-body: #080c14;
            --surface-1: #111827;
            --surface-2: #1f2937;
            --primary: #3b82f6; /* Royal Blue */
            --accent: #f59e0b; /* Amber */
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border: rgba(255,255,255,0.08);
            --gradient-1: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --gradient-glass: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Readex Pro', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- Global Scrollbar --- */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }

        /* --- Components --- */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--text-main);
            color: var(--bg-body);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255,255,255,0.1);
        }

        .btn-outline {
            border: 1px solid rgba(255,255,255,0.2);
            color: var(--text-main);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            border-color: #fff;
        }

        .section {
            padding: 8rem 5%;
            position: relative;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .heading-xl {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 600;
            line-height: 1.1;
            letter-spacing: -1px;
            margin-bottom: 2rem;
        }

        .heading-lg {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .text-desc {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 600px;
            font-weight: 300;
        }

        /* --- Header --- */
        .nav-wrapper {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 95%;
            max-width: 1600px;
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .nav-logo {
            font-size: 1.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 35px; height: 35px;
            background: #fff;
            border-radius: 8px;
            color: #000;
            display: flex; align-items: center; justify-content: center;
        }

        .nav-menu {
            display: flex; gap: 30px;
        }
        
        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 400;
            transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active { color: #fff; }

        /* --- Hero --- */
        .hero {
            min-height: 100vh;
            padding-top: 15vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        
        .hero-bg-glow {
            position: absolute;
            top: -20%; right: -10%;
            width: 800px; height: 800px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 60%);
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
        }

        /* --- Bento Grid Section --- */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, minmax(280px, auto));
            gap: 20px;
            margin-top: 4rem;
        }

        .bento-item {
            background: var(--surface-1);
            border-radius: 24px;
            padding: 2.5rem;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-image: var(--gradient-glass);
        }

        .bento-item:hover {
            transform: translateY(-5px);
            border-color: rgba(255,255,255,0.15);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .bento-large { grid-column: span 2; grid-row: span 2; }
        .bento-wide { grid-column: span 2; }
        .bento-tall { grid-row: span 2; }

        .bento-icon {
            width: 50px; height: 50px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: #fff;
        }
        
        .bento-large .bento-icon {
            width: 70px; height: 70px;
            font-size: 2rem;
            background: var(--primary);
        }

        .bento-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem; }
        .bento-large .bento-title { font-size: 2.5rem; margin-bottom: 1rem; }
        
        .bento-text { color: var(--text-muted); font-weight: 300; }

        .bento-img {
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 50%;
            object-fit: cover;
            mask-image: linear-gradient(to top, black, transparent);
            -webkit-mask-image: linear-gradient(to top, black, transparent);
            opacity: 0.5;
            transition: 0.5s;
        }
        
        .bento-item:hover .bento-img { transform: scale(1.05); opacity: 0.8; }

        /* --- Brand Strip --- */
        .brand-strip {
            padding: 3rem 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: center;
            gap: 4rem;
            opacity: 0.5;
            flex-wrap: wrap;
        }
        .brand-item { font-size: 1.5rem; font-weight: 700; color: #fff; letter-spacing: 2px; }

        /* --- Footer --- */
        .footer-modern {
            background: var(--surface-1);
            padding: 6rem 5% 3rem;
            margin-top: 4rem;
            border-radius: 40px 40px 0 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .footer-col h4 { color: #fff; margin-bottom: 1.5rem; font-size: 1.2rem; }
        .footer-links a { display: block; color: var(--text-muted); text-decoration: none; margin-bottom: 0.8rem; transition: 0.2s; }
        .footer-links a:hover { color: #fff; padding-right: 5px; }

        /* Responsive */
        @media (max-width: 1024px) {
            .bento-grid { grid-template-columns: 1fr 1fr; }
            .bento-large { grid-column: span 2; }
            .bento-wide { grid-column: span 2; }
        }
        @media (max-width: 768px) {
            .bento-grid { grid-template-columns: 1fr; }
            .bento-large, .bento-wide, .bento-tall { grid-column: span 1; grid-row: span 1; }
            .footer-grid { grid-template-columns: 1fr; }
            .nav-menu { display: none; }
        }

    </style>
</head>
<body>

    <!-- Header -->
    <nav class="nav-wrapper">
        <a href="#" class="nav-logo">
            <div class="logo-icon">M</div>
            <span>المُوَفِّي</span>
        </a>
        <div class="nav-menu">
            <a href="#" class="nav-link active">الرئيسية</a>
            <a href="<?= BASE_URL ?>/products" class="nav-link">منتجات ريكو</a>
            <a href="<?= BASE_URL ?>/services" class="nav-link">الصيانة</a>
            <a href="<?= BASE_URL ?>/about" class="nav-link">عن الشركة</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">
            منطقة العملاء
        </a>
    </nav>

    <!-- Hero -->
    <header class="hero section container">
        <div class="hero-bg-glow"></div>
        
        <div style="max-width: 900px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; color: var(--primary);">
                <i class="fas fa-check-circle"></i>
                <span style="font-weight: 500; letter-spacing: 1px;">وكيل معتمد لشركة ريكو</span>
            </div>
            
            <h1 class="heading-xl">
                نحول بيئة العمل <br>
                إلى <span style="color: var(--primary);">منظومة ذكية.</span>
            </h1>
            
            <p class="text-desc" style="margin-bottom: 3rem;">
                أكثر من 15 عاماً من الخبرة في تقديم حلول الطباعة الرقمية وأنظمة الأرشفة. نقدم لك الموثوقية اليابانية مع خدمة محلية بمواصفات عالمية.
            </p>
            
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?= BASE_URL ?>/maintenance" class="btn btn-primary">
                    <i class="fas fa-wrench" style="margin-left: 10px;"></i> طلب صيانة
                </a>
                <a href="<?= BASE_URL ?>/products" class="btn btn-outline">
                    استكشف المنتجات
                </a>
            </div>
        </div>

        <!-- 3D Abstract Visual (CSS) -->
        <div style="position: absolute; left: 0; bottom: 10%; width: 400px; height: 400px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.05); z-index: -1;">
            <div style="position: absolute; width: 100%; height: 100%; border: 1px solid rgba(255,255,255,0.05); border-radius: 50%; animation: pulse 4s infinite;"></div>
            <div style="position: absolute; width: 70%; height: 70%; top: 15%; left: 15%; background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);"></div>
        </div>
    </header>

    <!-- Brand Strip -->
    <div class="brand-strip">
        <span class="brand-item">RICOH</span>
        <span class="brand-item">AL-MUWAFI</span>
        <span class="brand-item">BUSINESS</span>
        <span class="brand-item">SOLUTIONS</span>
    </div>

    <!-- Bento Grid Services -->
    <section class="section container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 class="heading-lg">خدمات متكاملة</h2>
            <p class="text-desc" style="margin: 0 auto;">صممت لتلبي كافة احتياجات الشركات الحديثة</p>
        </div>

        <div class="bento-grid">
            <!-- Large Focus Item -->
            <div class="bento-item bento-large">
                <img src="https://images.unsplash.com/photo-1588619461332-4458d2a1386e?q=80&w=2574&auto=format&fit=crop" class="bento-img" alt="Maintenance">
                <div>
                    <div class="bento-icon"><i class="fas fa-tools"></i></div>
                    <h3 class="bento-title">الصيانة الشاملة</h3>
                    <p class="bento-text" style="font-size: 1.1rem; max-width: 400px;">
                        عقود صيانة سنوية تضمن لك راحة البال. زيارات دورية، استجابة للطوارئ، وتقارير أداء مفصلة لكل جهاز في منشأتك.
                    </p>
                </div>
                <div style="margin-top: 2rem;">
                    <a href="#" style="color: #fff; text-decoration: none; border-bottom: 1px solid #fff; padding-bottom: 5px;">تفاصيل العقود &larr;</a>
                </div>
            </div>

            <!-- Fast Service -->
            <div class="bento-item">
                <div class="bento-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3 class="bento-title">استجابة سريعة</h3>
                <p class="bento-text">فريقنا يصل إليك في وقت قياسي لضمان عدم توقف العمل.</p>
            </div>

            <!-- Org Parts -->
            <div class="bento-item">
                <div class="bento-icon"><i class="fas fa-check-circle"></i></div>
                <h3 class="bento-title">قطع أصلية</h3>
                <p class="bento-text">ضمان حقيقي على جميع قطع الغيار المستبدلة.</p>
            </div>

            <!-- Network Sol -->
            <div class="bento-item bento-wide">
                <div class="bento-icon"><i class="fas fa-network-wired"></i></div>
                <h3 class="bento-title">حلول الشبكات</h3>
                <p class="bento-text">ربط الطابعات والماسحات الضوئية بنظام الأرشفة السحابي لمؤسستك.</p>
            </div>

            <!-- Stats -->
            <div class="bento-item">
                <h3 class="heading-lg" style="color: var(--primary); margin-bottom: 0;">+1200</h3>
                <p class="bento-text">عميل يثق بنا</p>
                <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 1rem 0;"></div>
                <h3 class="heading-lg" style="color: var(--accent); margin-bottom: 0;">99%</h3>
                <p class="bento-text">نسبة الرضا</p>
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="nav-logo" style="font-size: 2rem; margin-bottom: 1.5rem;">
                        <div class="logo-icon" style="background: var(--primary); color: #fff;">M</div>
                        المُوَفِّي
                    </div>
                    <p class="text-desc" style="font-size: 1rem;">
                        نسعى دائماً لتقديم أفضل حلول الطباعة والأرشفة الرقمية. الجودة والالتزام هما أساس شراكتنا معكم.
                    </p>
                </div>
                
                <div class="footer-col">
                    <h4>روابط سريعة</h4>
                    <div class="footer-links">
                        <a href="<?= BASE_URL ?>/products">المنتجات</a>
                        <a href="<?= BASE_URL ?>/services">الخدمات</a>
                        <a href="<?= BASE_URL ?>/maintenance">طلب صيانة</a>
                        <a href="<?= BASE_URL ?>/privacy">سياسة الخصوصية</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>تواصل معنا</h4>
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-phone"></i> +966 50 000 0000</a>
                        <a href="#"><i class="fas fa-envelope"></i> info@almuwafi.com</a>
                        <a href="#"><i class="fas fa-map-marker-alt"></i> الرياض، المملكة العربية السعودية</a>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; color: var(--text-muted); padding-top: 2rem; border-top: 1px solid var(--border); font-size: 0.9rem;">
                جميع الحقوق محفوظة © 2024 نظام المُوَفِّي.
            </div>
        </div>
    </footer>

    <!-- Animations -->
    <script>
        gsap.registerPlugin(ScrollTrigger);

        // Hero Reveal
        gsap.from(".hero > div", {
            opacity: 0,
            y: 50,
            duration: 1.5,
            ease: "power3.out",
            stagger: 0.2
        });

        // Inbox Grid Reveal
        gsap.utils.toArray(".bento-item").forEach((item, i) => {
            gsap.from(item, {
                opacity: 0,
                y: 50,
                duration: 0.8,
                delay: i * 0.1,
                scrollTrigger: {
                    trigger: ".bento-grid",
                    start: "top 80%"
                }
            });
        });
    </script>
</body>
</html>

<?php
/**
 * الصفحة الرئيسية - النموذج الثاني (Minimalist Corporate)
 * نظام المُوَفِّي لمهمات المكاتب
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المُوَفِّي - شريكك للأعمال المكتبية</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;     /* Royal Blue */
            --primary-dark: #1e40af;
            --accent: #f59e0b;      /* Amber for CTAs */
            --bg-body: #ffffff;
            --bg-light: #f8fafc;
            --text-main: #0f172a;   /* Slate 900 */
            --text-muted: #64748b;  /* Slate 500 */
            --border: #e2e8f0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Cairo', sans-serif;
            margin: 0;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- Typography --- */
        h1, h2, h3 { font-family: 'Cairo', sans-serif; margin: 0; }
        
        .display-text {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--primary);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        /* --- Components --- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 3rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            gap: 10px;
        }

        .btn-primary {
            background: var(--text-main);
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
            background: #000;
        }
        
        .btn-outline {
            border: 2px solid var(--border);
            color: var(--text-main);
            background: transparent;
        }
        
        .btn-outline:hover {
            border-color: var(--text-main);
            background: var(--text-main);
            color: #fff;
        }

        /* --- Navigation --- */
        .navbar {
            padding: 20px 0;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-menu {
            display: flex;
            gap: 30px;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }

        /* --- Hero Section --- */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            background-image: radial-gradient(#64748b 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* --- Services Grid --- */
        .services-section {
            padding: 8rem 0;
            background: #fff;
        }

        .service-card {
            background: #fff;
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.08);
            transform: translateY(-5px);
        }

        .service-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            width: 60px;
            height: 60px;
            background: rgba(37, 99, 235, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .service-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .service-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* --- About Identity --- */
        .about-section {
            padding: 8rem 0;
            background: var(--text-main);
            color: #fff;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 4rem;
        }
        
        .stat-num {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #94a3b8;
        }

        /* --- Footer --- */
        .simple-footer {
            padding: 4rem 0;
            background: #fff;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            .display-text { font-size: 2.5rem; }
            .nav-menu { display: none; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <!-- Professional Nav -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="#" class="logo">
                <span style="color: var(--primary);">●</span> المُوَفِّي
            </a>
            <div class="nav-menu">
                <a href="#" class="nav-link active">الرئيسية</a>
                <a href="#" class="nav-link">عن الشركة</a>
                <a href="#" class="nav-link">خدماتنا</a>
                <a href="#" class="nav-link">تواصل معنا</a>
            </div>
            <a href="<?= BASE_URL ?>/maintenance" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.9rem;">
                منطقة العملاء
            </a>
        </div>
    </nav>

    <!-- Clean Hero -->
    <section class="hero">
        <div class="hero-pattern"></div>
        <div class="container" style="position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 4rem;">
            
            <div>
                <span style="color: var(--primary); font-weight: 700; letter-spacing: 2px; font-size: 0.9rem; display: block; margin-bottom: 1rem;">شريكك الموثوق</span>
                <h1 class="display-text" style="margin-bottom: 1.5rem;">
                    نقدم حلولاً مكتبية <br> تليق بطموحك.
                </h1>
                <p style="color: var(--text-muted); font-size: 1.2rem; margin-bottom: 2.5rem; max-width: 500px;">
                    منذ سنوات ونحن الخيار الأول للشركات في المملكة. نقدم خدمات صيانة احترافية، وأجهزة ريكو أصلية تضمن استمرارية أعمالك.
                </p>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="#" class="btn btn-primary">ابدأ الآن</a>
                    <a href="#" class="btn btn-outline">تعرف علينا</a>
                </div>
            </div>

            <!-- Abstract Visual -->
            <div style="position: relative;">
                <div style="background: #fff; padding: 2rem; border-radius: 20px; box-shadow: 0 40px 80px rgba(0,0,0,0.08); position: relative; z-index: 2;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 2rem;">
                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 700; font-size: 1.1rem; margin: 0;">اعتماد رسمي</h4>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">وكيل معتمد لخدمات ريكو</span>
                        </div>
                    </div>
                    <div style="height: 1px; background: #f1f5f9; margin-bottom: 2rem;"></div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-history" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 700; font-size: 1.1rem; margin: 0;">سرعة التنفيذ</h4>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">متوسط استجابة أقل من 24 ساعة</span>
                        </div>
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div style="position: absolute; width: 200px; height: 200px; background: var(--primary); opacity: 0.1; border-radius: 50%; top: -30px; right: -30px; z-index: 1;"></div>
                <div style="position: absolute; width: 100px; height: 100px; border: 4px solid var(--text-main); opacity: 0.1; border-radius: 50%; bottom: -20px; left: -20px; z-index: 1;"></div>
            </div>

        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">خدماتنا</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-wrench"></i></div>
                    <h3 class="service-title">الصيانة الدورية</h3>
                    <p class="service-desc">نقوم بفحص دوري شامل لجميع الأجهزة لضمان عملها بكفاءة وتجنب الأعطال المفاجئة التي قد تعطل سير العمل.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-print"></i></div>
                    <h3 class="service-title">مبيعات الأجهزة</h3>
                    <p class="service-desc">نوفر أحدث موديلات ماكينات ريكو العالمية، بتشكيلة واسعة تناسب احتياجات الشركات الصغيرة والمتوسطة والكبيرة.</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-network-wired"></i></div>
                    <h3 class="service-title">حلول الشبكات</h3>
                    <p class="service-desc">ربط الطابعات بالشبكة الداخلية، وتفعيل الطباعة السحابية والمسح الضوئي المركزي لتسهيل مشاركة المستندات.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Corporate Identity -->
    <section class="about-section">
        <div class="container">
            <h2 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem;">التميز هو معيارنا الوحيد</h2>
            <p style="font-size: 1.2rem; color: #94a3b8; max-width: 700px; margin: 0 auto;">
                نحن في المُوَفِّي نؤمن بأن نجاح عملائنا هو نجاحنا. لذلك نكرس كل جهودنا لتقديم خدمات ترقى لتوقعاتكم، بدقة متناهية واحترافية لا تضاهى.
            </p>
            
            <div class="stats-grid">
                <div>
                    <span class="stat-num">15+</span>
                    <span class="stat-label">سنة خبرة</span>
                </div>
                <div>
                    <span class="stat-num">5k+</span>
                    <span class="stat-label">عميل</span>
                </div>
                <div>
                    <span class="stat-num">100%</span>
                    <span class="stat-label">ضمان الجودة</span>
                </div>
                <div>
                    <span class="stat-num">24/7</span>
                    <span class="stat-label">دعم مستمر</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="simple-footer">
        <div class="container">
            <a href="#" class="logo" style="justify-content: center; margin-bottom: 1.5rem;">
                <span style="color: var(--primary);">●</span> المُوَفِّي
            </a>
            <div style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <a href="#" style="color: var(--text-main); text-decoration: none;">الرئيسية</a>
                <a href="#" style="color: var(--text-main); text-decoration: none;">خدماتنا</a>
                <a href="#" style="color: var(--text-main); text-decoration: none;">المنتجات</a>
                <a href="#" style="color: var(--text-main); text-decoration: none;">الشروط والأحكام</a>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                جميع الحقوق محفوظة © 2024 نظام المُوَفِّي.
            </p>
        </div>
    </footer>

</body>
</html>

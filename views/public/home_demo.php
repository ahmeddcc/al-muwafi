<?php
/**
 * الصفحة الرئيسية - النسخة التجريبية (Legendary Dark Glass Edition)
 * نظام المُوَفِّي لمهمات المكاتب
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المُوَفِّي - حلول ريكو الذكية</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #020617;
            --bg-card: rgba(30, 41, 59, 0.4);
            --primary: #0ea5e9;
            --secondary: #6366f1;
            --accent: #f59e0b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.08);
            --glow: 0 0 20px rgba(14, 165, 233, 0.3);
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(14, 165, 233, 0.08) 0%, transparent 25%);
            color: var(--text-main);
            font-family: 'Cairo', sans-serif;
            margin: 0;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- Animations --- */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse-glow {
            0% { box-shadow: 0 0 15px rgba(14, 165, 233, 0.2); }
            50% { box-shadow: 0 0 30px rgba(14, 165, 233, 0.5); }
            100% { box-shadow: 0 0 15px rgba(14, 165, 233, 0.2); }
        }

        /* --- Header --- */
        .glass-nav {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 1400px;
            padding: 1rem 2rem;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            color: var(--primary);
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-glow {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            box-shadow: var(--glow);
            transition: all 0.3s;
        }

        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px rgba(14, 165, 233, 0.6);
        }
        
        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
        }

        /* --- Hero Section --- */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 5% 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            flex: 1;
            z-index: 2;
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .text-gradient {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .hero-visual {
            flex: 1;
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
        }

        .printer-3d {
            width: 500px;
            height: auto;
            filter: drop-shadow(0 0 50px rgba(14, 165, 233, 0.2));
            animation: float 6s ease-in-out infinite;
        }

        /* --- Features/Services --- */
        .section {
            padding: 6rem 5%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .glass-card:hover {
            transform: translateY(-10px);
            border-color: rgba(14, 165, 233, 0.3);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .glass-card:hover::before {
            opacity: 1;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: rgba(14, 165, 233, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        
        .glass-card:hover .card-icon {
            background: var(--primary);
            color: #fff;
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #e2e8f0;
        }

        .card-text {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* --- Stats Counter --- */
        .stats-section {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(2, 6, 23, 0.9));
            margin: 4rem 0;
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.5rem;
            display: block;
            text-shadow: 0 0 20px rgba(14, 165, 233, 0.5);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* --- Footer --- */
        .footer {
            background: #020617;
            padding: 4rem 5% 2rem;
            border-top: 1px solid var(--glass-border);
        }
        
        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--glass-border);
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        /* --- Mobile --- */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column-reverse;
                text-align: center;
                padding-top: 6rem;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .printer-3d {
                width: 80%;
                margin-bottom: 2rem;
            }
            
            .nav-links {
                display: none; /* Simplification for demo */
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <nav class="glass-nav">
        <a href="#" class="logo">
            <i class="fas fa-print"></i>
            المُوَفِّي
        </a>
        <div class="nav-links">
            <a href="#" class="nav-link active">الرئيسية</a>
            <a href="#" class="nav-link">خدماتنا</a>
            <a href="#" class="nav-link">منتجاتنا</a>
            <a href="#" class="nav-link">من نحن</a>
        </div>
        <a href="<?= BASE_URL ?>/maintenance" class="btn-glow">
            <i class="fas fa-tools"></i> طلب صيانة
        </a>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">
                مستقبل الطباعة الرقمية <br>
                <span class="text-gradient">يبدأ من هنا</span>
            </h1>
            <p class="hero-desc">
                نقدم حلولاً متكاملة لمكاتب المستقبل. صيانة احترافية، منتجات أصلية، ودعم فني لا يتوقف. ارتقِ بأداء مكتبك مع المُوَفِّي.
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="#" class="btn-glow">استكشف خدماتنا</a>
                <a href="#" class="btn-outline">تواصل معنا</a>
            </div>
            
            <div style="margin-top: 3rem; display: flex; gap: 2rem; color: var(--text-muted);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle" style="color: var(--primary);"></i>
                    <span>معتمد من ريكو</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle" style="color: var(--primary);"></i>
                    <span>ضمان شامل</span>
                </div>
            </div>
        </div>
        
        <div class="hero-visual">
            <!-- 3D Printer Placeholder (Using FontAwesome for demo, implies 3D Image) -->
            <div class="printer-3d" style="font-size: 15rem; color: rgba(255,255,255,0.05); display: flex; justify-content: center; align-items: center; background: radial-gradient(circle, rgba(14,165,233,0.1) 0%, transparent 70%); border-radius: 50%; width: 400px; height: 400px;">
                <i class="fas fa-print" style="color: var(--primary); filter: drop-shadow(0 0 30px var(--primary));"></i>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section section">
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-item">
                <span class="stat-number">500+</span>
                <span class="stat-label">عميل سعيد</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">120+</span>
                <span class="stat-label">عقد صيانة</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">24/7</span>
                <span class="stat-label">دعم فني</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">98%</span>
                <span class="stat-label">نسبة الرضا</span>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="section">
        <div class="section-header">
            <span class="section-subtitle">خدماتنا</span>
            <h2 class="section-title">حلول مبتكرة لمكتبك</h2>
        </div>
        
        <div class="grid">
            <div class="glass-card">
                <div class="card-icon"><i class="fas fa-cog"></i></div>
                <h3 class="card-title">صيانة دورية ذكية</h3>
                <p class="card-text">نظام صيانة وقائي يعتمد على تحليل البيانات لضمان عمل أجهزتك بكفاءة قصوى دون توقف مفاجئ.</p>
            </div>
            
            <div class="glass-card">
                <div class="card-icon"><i class="fas fa-box-open"></i></div>
                <h3 class="card-title">قطع غيار أصلية</h3>
                <p class="card-text">نوفر قطع غيار ريكو الأصلية مع ضمان الجودة، لضمان عمر أطول للآلات وأداء متميز.</p>
            </div>
            
            <div class="glass-card">
                <div class="card-icon"><i class="fas fa-headset"></i></div>
                <h3 class="card-title">دعم فني فوري</h3>
                <p class="card-text">فريق دعم فني خبير جاهز للرد على استفساراتكم وحل المشكلات عن بعد أو في الموقع.</p>
            </div>
        </div>
    </section>

    <!-- Why Us -->
    <section class="section" style="position: relative;">
        <!-- Background decoration -->
        <div style="position: absolute; right: 0; bottom: 0; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, transparent 70%); border-radius: 50%; z-index: -1;"></div>
        
        <div class="grid" style="align-items: center;">
            <div>
                <span class="section-subtitle">لماذا المُوَفِّي؟</span>
                <h2 class="section-title">نحن لا نبيع طابعات فقط، <br> <span class="text-gradient">نحن نبني شراكات</span></h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">
                    في عالم الأعمال المتسارع، لا يمكنك تحمل توقف العمل. نحن نفهم ذلك، ولهذا قمنا بتصميم خدماتنا لتكون استباقية وسريعة وموثوقة.
                </p>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="background: rgba(14,165,233,0.1); padding: 10px; border-radius: 10px; color: var(--primary);">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 5px 0; color: #e2e8f0;">سرعة استجابة قياسية</h4>
                            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">نصل إليك في غضون ساعات قليلة من طلب الخدمة.</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="background: rgba(99,102,241,0.1); padding: 10px; border-radius: 10px; color: var(--secondary);">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 5px 0; color: #e2e8f0;">خبراء معتمدون</h4>
                            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">فريقنا حاصل على شهادات خبرة مباشرة من شركة ريكو.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="position: relative;">
                <div class="glass-card" style="padding: 3rem; text-align: center; border: 1px solid var(--primary); box-shadow: 0 0 50px rgba(14,165,233,0.1);">
                    <i class="fas fa-quote-right" style="font-size: 2rem; color: var(--primary); opacity: 0.5; margin-bottom: 1rem;"></i>
                    <p style="font-size: 1.2rem; font-style: italic; color: #e2e8f0; margin-bottom: 1.5rem;">
                        "التعامل مع المُوَفِّي أحدث تغييراً جذرياً في كفاءة عملنا. الدعم الفني ممتاز وسرعة الاستجابة لا تُضاهى."
                    </p>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: #334155; border-radius: 50%;"></div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700;">أحمد علي</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">مدير شركة الفرسان</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 2rem;">
            <div style="max-width: 300px;">
                <a href="#" class="logo" style="margin-bottom: 1rem;">
                    <i class="fas fa-print"></i> المُوَفِّي
                </a>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    شريكك الموثوق في حلول الطباعة الرقمية والخدمات المكتبية في المملكة.
                </p>
            </div>
            
            <div>
                <h4 style="color: #fff; margin-bottom: 1rem;">روابط مهمة</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="#" style="color: var(--text-muted); text-decoration: none;">الرئيسية</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none;">الخدمات</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none;">المنتجات</a>
                </div>
            </div>
            
            <div>
                <h4 style="color: #fff; margin-bottom: 1rem;">تواصل معنا</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; color: var(--text-muted);">
                    <span><i class="fas fa-phone"></i> +966 50 000 0000</span>
                    <span><i class="fas fa-envelope"></i> info@almuwafi.com</span>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2024 نظام المُوَفِّي. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

</body>
</html>

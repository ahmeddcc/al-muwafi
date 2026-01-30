<?php
/**
 * صفحة تسجيل الدخول - التصميم الجديد
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Security;
use App\Services\Settings;

$companyInfo = Settings::getCompanyInfo();

// جلب صورة صفحة تسجيل الدخول من الإعدادات
$loginImage = !empty($companyInfo['login_image']) 
    ? BASE_URL . '/storage/uploads/' . $companyInfo['login_image'] 
    : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - <?= htmlspecialchars($companyInfo['name'] ?? 'المُوَفِّي') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <?php if (!empty($companyInfo['favicon'])): ?>
    <link rel="icon" href="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['favicon'] ?>">
    <?php endif; ?>
    
    <style>
        :root {
            --bg-color: #050505;
            --accent-color: #00f3ff;
            --secondary-accent: #7000ff;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --text-color: #ffffff;
            --input-bg: rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif; }

        body {
            height: 100vh;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 20% 50%, #1a1a2e 0%, #000 70%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            width: 90%;
            max-width: 1400px;
            height: 85vh;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        /* === القسم الأيمن: النموذج === */
        .form-section {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .form-section::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 2px; /* زيادة السمك قليلاً */
            height: 70%;
            background: linear-gradient(to bottom, transparent, var(--accent-color), transparent);
            box-shadow: 0 0 20px var(--accent-color), 0 0 40px var(--accent-color); /* توهج أقوى */
            opacity: 0.8;
            animation: neonPulse 3s ease-in-out infinite alternate;
        }

        @keyframes neonPulse {
            0% {
                box-shadow: 0 0 20px var(--accent-color), 0 0 40px var(--accent-color);
                opacity: 0.8;
            }
            100% {
                box-shadow: 0 0 30px var(--accent-color), 0 0 60px var(--accent-color), 0 0 100px var(--accent-color);
                opacity: 1;
            }
        }

        .header { 
            margin-bottom: 40px; 
            text-align: center; /* توسيط النص */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header .logo-img { 
            max-height: 80px; /* زيادة حجم اللوجو قليلاً */
            margin-bottom: 25px; 
            filter: drop-shadow(0 0 15px var(--accent-color)); 
            display: block;
        }
        .header h1 { 
            font-size: 36px; /* تكبير العنوان */
            font-weight: 900; 
            color: var(--text-color); 
            margin-bottom: 10px; 
            text-shadow: 0 0 20px rgba(255,255,255,0.1);
        }
        .header p { color: #888; font-size: 15px; }

        .form-container {
            width: 100%;
            max-width: 320px; /* تصغير العرض ليكون أكثر احترافية */
            margin: 0 auto;
        }

        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { 
            display: block; 
            margin-bottom: 10px; 
            color: #ccc; 
            font-size: 14px; 
            text-align: center;
        }
        .input-group input {
            width: 100%; 
            padding: 12px 20px; /* تصغير الحشوة قليلاً */
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 50px;
            color: #fff;
            font-size: 14px; 
            outline: none; 
            transition: 0.3s ease;
            text-align: center;
        }
        .input-group input:focus { 
            border-color: var(--accent-color); 
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.15); 
            background: rgba(0, 0, 0, 0.4);
            transform: scale(1.02);
        }

        /* === مفتاح التبديل (تذكرني) === */
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
            cursor: pointer;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
        }
        
        .switch input { opacity: 0; width: 0; height: 0; }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--accent-color);
            box-shadow: 0 0 10px var(--accent-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(20px);
        }

        .remember-text {
            color: #ccc;
            font-size: 13px;
        }

        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(90deg, var(--accent-color), var(--secondary-accent));
            border: none; border-radius: 50px;
            color: #fff; font-size: 16px;
            font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 5px;
            text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 5px 20px rgba(0, 243, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg) translate(-100%, -100%);
            animation: shineBtn 3s infinite;
        }
        @keyframes shineBtn {
            0% { transform: rotate(45deg) translate(-100%, -100%); }
            20% { transform: rotate(45deg) translate(100%, 100%); }
            100% { transform: rotate(45deg) translate(100%, 100%); }
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0, 243, 255, 0.5); }

        .alert {
            padding: 15px; border-radius: 8px; margin-bottom: 20px;
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5; font-size: 14px;
            text-align: center;
        }

        .back-link { text-align: center; margin-top: 25px; }
        .back-link a { color: #888; text-decoration: none; font-size: 13px; transition: 0.3s; }
        .back-link a:hover { color: var(--accent-color); text-shadow: 0 0 10px var(--accent-color); }

        /* === القسم الأيسر: الصورة === */
        .image-section {
            flex: 1.3;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            /* إزالة أي خلفية قد تؤثر على الألوان */
            background: transparent;
            overflow: hidden;
        }

        .machine-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .machine-img {
            max-width: 90%; 
            max-height: 85%; 
            object-fit: contain;
            /* الظل الأسود الطبيعي بدلاً من الملون لإزالة الضباب */
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));
            animation: floatMachine 6s ease-in-out infinite;
            position: relative;
            z-index: 2;
            transition: filter 0.4s ease; /* تنعيم تأثير التغيير */
            cursor: pointer;
        }

        .machine-img:hover {
            /* توهج نيون عند المرور */
            filter: drop-shadow(0 0 25px var(--accent-color)) brightness(1.1);
        }

        .no-image-placeholder {
            color: rgba(255,255,255,0.1);
            font-size: 150px;
            animation: floatMachine 6s ease-in-out infinite;
        }

        @keyframes floatMachine {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* === حركة الأوراق المتطايرة === */
        .flying-papers {
            position: absolute;
            top: 40%; /* رفع مكان الخروج لأعلى قليلاً (كان 50%) */
            left: 42%; /* تعديل طفيف للموقع الأفقي */
            width: 0;
            height: 0;
            pointer-events: none;
            z-index: 1; /* خلف الماكينة */
        }

        .paper {
            position: absolute;
            width: 50px;
            height: 70px; /* أبعاد ورقة A4 مصغرة */
            background: #fff;
            border-radius: 2px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            opacity: 0;
            transform-origin: center center;
            animation: flyPaperReal var(--duration) cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
            animation-delay: var(--delay);
        }

        /* تفاصيل الورقة */
        .paper::before {
            content: ''; position: absolute; top: 10%; left: 10%; width: 80%; height: 2px; background: #e0e0e0; box-shadow: 0 5px 0 #e0e0e0, 0 10px 0 #e0e0e0;
        }

        @keyframes flyPaperReal {
            0% {
                opacity: 0;
                transform: translate(0, 0) scale(0.5) rotateX(45deg); /* تخرج صغيرة ومن زاوية */
                z-index: 1;
            }
            15% {
                opacity: 1;
                transform: translate(-40px, 10px) scale(0.8) rotateX(0deg); /* تظهر وتكبر قليلاً */
                z-index: 3; /* تصبح أمام الماكينة */
            }
            100% {
                opacity: 0;
                /* تطير بعيداً باتجاهات مختلفة */
                transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(1.2);
                z-index: 3;
            }
        }

        /* === مسارات واقعية للأوراق === */
        /* تخرج لليسار والأسفل (كأنها تقع في صينية المخرجات أو تطير منها) */
        .paper.p1 { --tx: -180px; --ty: 150px; --rot: -25deg; --delay: 0s; --duration: 3s; }
        .paper.p2 { --tx: -220px; --ty: 80px;  --rot: -45deg; --delay: 0.8s; --duration: 3.5s; }
        .paper.p3 { --tx: -150px; --ty: 200px; --rot: -10deg; --delay: 1.5s; --duration: 4s; }
        .paper.p4 { --tx: -250px; --ty: 120px; --rot: -60deg; --delay: 2.2s; --duration: 3.2s; }
        .paper.p5 { --tx: -200px; --ty: 180px; --rot: -30deg; --delay: 3s; --duration: 3.8s; }
        .paper.p6 { --tx: -280px; --ty: 100px; --rot: -50deg; --delay: 3.8s; --duration: 4.2s; }
        /* === التجاوب مع الموبايل === */
        @media (max-width: 900px) {
            .main-container { flex-direction: column-reverse; height: auto; min-height: 100vh; width: 100%; border-radius: 0; border: none; }
            .image-section { height: 350px; flex: none; }
            .form-section { flex: none; padding: 40px 30px; }

            .form-section::before {
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                width: 70%;
                height: 1px;
                background: linear-gradient(to right, transparent, var(--accent-color), transparent);
            }
            
            .header h1 { font-size: 26px; }
            .paper { width: 30px; height: 38px; font-size: 12px; }
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="form-section">
            <div class="header">
                <?php if (!empty($companyInfo['logo'])): ?>
                <img src="<?= BASE_URL ?>/storage/uploads/<?= $companyInfo['logo'] ?>" alt="Logo" class="logo-img">
                <?php endif; ?>
                <h1>تسجيل الدخول</h1>
                <p>مرحباً بك في لوحة تحكم <?= htmlspecialchars($companyInfo['name'] ?? 'المُوَفِّي') ?></p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/admin/auth/authenticate" method="POST">
                <?= Security::csrfField() ?>
                
                <div class="input-group">
                    <label>اسم المستخدم أو البريد الإلكتروني</label>
                    <input type="text" name="username" placeholder="أدخل اسم المستخدم" required autofocus>
                </div>

                <div class="input-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="remember-me">
                    <label class="switch">
                        <input type="checkbox" name="remember">
                        <span class="slider"></span>
                    </label>
                    <span class="remember-text">تذكرني</span>
                </div>

                <button type="submit" class="btn-submit">تسجيل الدخول</button>
            </form>

            <div class="back-link">
                <a href="<?= BASE_URL ?>">← العودة للموقع</a>
            </div>
        </div>

        <div class="image-section">
            <!-- الأوراق المتطايرة -->
            <div class="flying-papers">
                <div class="paper p1"><span class="icon">📄</span></div>
                <div class="paper p2"><span class="icon">📊</span></div>
                <div class="paper p3"><span class="icon">📝</span></div>
                <div class="paper p4"><span class="icon">💼</span></div>
                <div class="paper p5"><span class="icon">📋</span></div>
                <div class="paper p6"><span class="icon">📈</span></div>
                <div class="paper p7"><span class="icon">🖨️</span></div>
                <div class="paper p8"><span class="icon">✨</span></div>
            </div>

            <div class="machine-container">
                <?php if (!empty($loginImage)): ?>
                <img src="<?= $loginImage ?>" alt="Login Image" class="machine-img">
                <?php else: ?>
                <div class="no-image-placeholder">🖨️</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</body>
</html>

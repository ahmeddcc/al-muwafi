<?php
/**
 * صفحة قريباً - Coming Soon
 * نظام المُوَفِّي لمهمات المكاتب
 */

use App\Services\Settings;

$companyName = Settings::get('company_name', 'المُوَفِّي لخدمات ريكو');
$companyPhone = Settings::get('company_phone', '');
$companyEmail = Settings::get('company_email', '');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قريباً - <?= htmlspecialchars($companyName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a3e 50%, #0d0d1a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        /* خلفية متحركة */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .bg-animation span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(0, 212, 255, 0.1);
            animation: float 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }
        
        .bg-animation span:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .bg-animation span:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .bg-animation span:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .bg-animation span:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .bg-animation span:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
        .bg-animation span:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        .bg-animation span:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .bg-animation span:nth-child(8) { left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
        .bg-animation span:nth-child(9) { left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
        .bg-animation span:nth-child(10) { left: 85%; width: 150px; height: 150px; animation-delay: 0s; animation-duration: 11s; }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 50%;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }
        
        .container {
            text-align: center;
            z-index: 1;
            padding: 2rem;
            max-width: 700px;
        }
        
        /* أيقونة متحركة */
        .logo-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            position: relative;
        }
        
        .logo-icon .printer {
            font-size: 4rem;
            animation: pulse 2s ease-in-out infinite;
            display: block;
            background: linear-gradient(135deg, #00d4ff, #7c3aed, #00d4ff);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s ease-in-out infinite, gradient 3s ease infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .gear {
            position: absolute;
            font-size: 1.5rem;
            animation: rotate 4s linear infinite;
        }
        
        .gear-1 { top: 0; right: 10px; color: #00d4ff; }
        .gear-2 { bottom: 10px; left: 10px; color: #7c3aed; animation-direction: reverse; }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .brand {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00d4ff, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }
        
        .tagline {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }
        
        .coming-soon {
            font-size: 3.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1rem;
            text-shadow: 0 0 40px rgba(0, 212, 255, 0.3);
        }
        
        .message {
            color: #a0aec0;
            font-size: 1.3rem;
            line-height: 2;
            margin-bottom: 3rem;
        }
        
        .message strong {
            color: #00d4ff;
        }
        
        /* شريط التحميل */
        .progress-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 5px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 8px;
            background: linear-gradient(90deg, #00d4ff, #7c3aed, #00d4ff);
            background-size: 200% 100%;
            border-radius: 50px;
            width: 75%;
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .progress-text {
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        /* معلومات التواصل */
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .contact-item:hover {
            color: #00d4ff;
        }
        
        .contact-item .icon {
            font-size: 1.2rem;
        }
        
        /* تأثيرات خاصة */
        .glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .glow-1 { top: -100px; right: -100px; }
        .glow-2 { bottom: -100px; left: -100px; background: radial-gradient(circle, rgba(124, 58, 237, 0.15) 0%, transparent 70%); }
        
        @media (max-width: 768px) {
            .coming-soon { font-size: 2.5rem; }
            .message { font-size: 1.1rem; }
            .brand { font-size: 2rem; }
            .contact-info { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
    <!-- خلفية متحركة -->
    <div class="bg-animation">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>
    
    <!-- تأثيرات التوهج -->
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>
    
    <div class="container">
        <!-- الأيقونة المتحركة -->
        <div class="logo-icon">
            <span class="printer">🖨️</span>
            <span class="gear gear-1">⚙️</span>
            <span class="gear gear-2">⚙️</span>
        </div>
        
        <!-- اسم الموقع -->
        <h1 class="brand"><?= htmlspecialchars($companyName) ?></h1>
        <p class="tagline">شريككم الموثوق في عالم آلات التصوير والطابعات</p>
        
        <!-- رسالة قريباً -->
        <h2 class="coming-soon">🚀 قريباً</h2>
        
        <p class="message">
            <strong>عملاؤنا الكرام</strong><br>
            نعمل بجد لإطلاق موقعنا الجديد بحلة عصرية ومميزة<br>
            انتظرونا قريباً... نجهز لكم تجربة استثنائية!
        </p>
        
        <!-- شريط التقدم -->
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        <p class="progress-text">جارٍ الإنشاء... 75%</p>
        
        <!-- معلومات التواصل -->
        <?php if ($companyPhone || $companyEmail): ?>
        <div class="contact-info">
            <?php if ($companyPhone): ?>
            <a href="tel:<?= $companyPhone ?>" class="contact-item">
                <span class="icon">📞</span>
                <span dir="ltr"><?= htmlspecialchars($companyPhone) ?></span>
            </a>
            <?php endif; ?>
            <?php if ($companyEmail): ?>
            <a href="mailto:<?= $companyEmail ?>" class="contact-item">
                <span class="icon">✉️</span>
                <span><?= htmlspecialchars($companyEmail) ?></span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

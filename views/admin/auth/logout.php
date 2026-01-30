<?php
/**
 * صفحة تسجيل الخروج
 * نظام المُوَفِّي لخدمات ريكو
 */

$pageTitle = 'تسجيل الخروج';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | نظام المُوَفِّي</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --neon-blue: #0ea5e9;
            --neon-purple: #8b5cf6;
            --text-main: #f8fafc;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            background-color: #020617;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            z-index: -1;
            animation: float 10s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: var(--neon-blue);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            background: var(--neon-purple);
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 50px); }
        }

        .logout-container {
            text-align: center;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3.5rem 3rem;
            border-radius: 30px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
            max-width: 480px;
            width: 90%;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: appear 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes appear {
            to { transform: translateY(0); opacity: 1; }
        }

        .icon-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(139, 92, 246, 0.1));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 2.5rem;
            color: var(--neon-blue);
            box-shadow: 0 0 30px rgba(14, 165, 233, 0.15);
            position: relative;
            z-index: 2;
        }

        .icon-ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(14, 165, 233, 0.3);
            animation: ripple 2s infinite linear;
            z-index: 1;
        }

        @keyframes ripple {
            0% { width: 100%; height: 100%; opacity: 1; }
            100% { width: 180%; height: 180%; opacity: 0; }
        }

        h1 {
            font-size: 2rem;
            margin: 0 0 0.8rem 0;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: #94a3b8;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
            font-weight: 500;
        }

        .progress-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            height: 6px;
            width: 100%;
            margin-bottom: 2.5rem;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--neon-blue), var(--neon-purple));
            width: 100%;
            border-radius: 8px;
            animation: countdown 6s linear forwards;
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.5);
        }

        @keyframes countdown {
            from { width: 100%; }
            to { width: 0%; }
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding: 14px 32px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            box-sizing: border-box;
            font-size: 1.05rem;
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .security-status {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #10b981;
            opacity: 0.8;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10b981;
        }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="logout-container">
        <div class="icon-wrapper">
            <div class="icon-ripple"></div>
            <div class="icon-circle">
                <i class="fa-solid fa-lock"></i>
            </div>
        </div>
        
        <h1>تم تأمين الجلسة</h1>
        <p>تم تسجيل خروجك بأمان من النظام.<br>سنقوم بإعادة توجيهك قريباً...</p>
        
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        
        <a href="<?= BASE_URL ?>/admin/auth/login" class="btn-login" id="loginBtn">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            العودة لصفحة الدخول
        </a>
        
        <div class="security-status">
            <span class="status-dot"></span>
            <span>الاتصال مشفر وآمن</span>
        </div>
    </div>

    <script>
        // Redirect after 6 seconds
        setTimeout(function() {
            window.location.href = "<?= BASE_URL ?>/admin/auth/login";
        }, 6000);
        
        // Manual click handling
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "<?= BASE_URL ?>/admin/auth/login";
        });
    </script>
</body>
</html>

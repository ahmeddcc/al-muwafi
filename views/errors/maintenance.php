<?php
/**
 * صفحة وضع الصيانة
 * نظام المُوَفِّي لمهمات المكاتب
 */

$title = 'الموقع تحت الصيانة';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - المُوَفِّي لمهمات المكاتب</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            direction: rtl;
        }
        .maintenance-container {
            text-align: center;
            padding: 2rem;
        }
        .icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: rotate 3s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .title {
            font-size: 2rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #00d4ff, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .message {
            color: #a0a0a0;
            font-size: 1.1rem;
            max-width: 500px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon">⚙️</div>
        <h1 class="title">الموقع تحت الصيانة</h1>
        <p class="message">
            نعمل حالياً على تحسين الموقع وإضافة ميزات جديدة.
            <br>
            سنعود قريباً. نشكر صبركم وتفهمكم.
        </p>
    </div>
</body>
</html>

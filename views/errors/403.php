<?php
/**
 * صفحة الخطأ 403
 * نظام المُوَفِّي لمهمات المكاتب
 */

$title = 'غير مصرح';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - المُوَفِّي لخدمات ريكو</title>
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
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ffa726, #ff7043);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 1.5rem;
            margin: 1rem 0;
            color: #e0e0e0;
        }
        .error-message {
            color: #a0a0a0;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, #ffa726, #ff7043);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 167, 38, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">غير مصرح بالوصول</h1>
        <p class="error-message">عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة.</p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/admin" class="btn">العودة للوحة التحكم</a>
    </div>
</body>
</html>

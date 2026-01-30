<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_PATH', dirname(__DIR__));
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/app/Services/Database.php';
require_once APP_PATH . '/app/Services/Settings.php';
require_once APP_PATH . '/app/Services/ErrorLogger.php';

use App\Services\Settings;

if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
   die('Access Denied');
}

header('Content-Type: text/plain; charset=utf-8');

$ownerToken = Settings::get('telegram_owner_bot_token');

if (!$ownerToken) {
    die("Token غير موجود في الإعدادات");
}

// رابط الـ Webhook - يجب أن يكون رابط عام (HTTPS)
// غيّر هذا الرابط إلى رابطك الخارجي (ngrok أو domain)
$webhookUrl = '';

if (isset($_GET['url'])) {
    $webhookUrl = $_GET['url'];
}

if (empty($webhookUrl)) {
    echo "=== تعيين Webhook للتلجرام ===\n\n";
    echo "استخدم هذا الملف مع معامل url:\n";
    echo "مثال: check_telegram.php?action=set&url=https://yourdomain.com/al-muwafi/public/telegram_webhook.php\n\n";
    echo "أو إذا كنت تستخدم ngrok:\n";
    echo "1. شغّل ngrok: ngrok http 80\n";
    echo "2. انسخ الرابط HTTPS (مثل https://abc123.ngrok.io)\n";
    echo "3. افتح: set_webhook.php?url=https://abc123.ngrok.io/al-muwafi/public/telegram_webhook.php\n";
    exit;
}

echo "=== تسجيل Webhook ===\n\n";
echo "URL: $webhookUrl\n\n";

$apiUrl = "https://api.telegram.org/bot{$ownerToken}/setWebhook";
$postData = ['url' => $webhookUrl];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "خطأ: $error\n";
} else {
    $data = json_decode($response, true);
    if ($data && $data['ok']) {
        echo "✅ تم تسجيل الـ Webhook بنجاح!\n";
        echo "Description: " . ($data['description'] ?? '') . "\n";
        
        // حفظ الرابط في الإعدادات
        Settings::set('telegram_owner_webhook_url', $webhookUrl);
        echo "\n✅ تم حفظ الرابط في إعدادات النظام.\n";
    } else {
        echo "❌ فشل: " . ($data['description'] ?? 'خطأ غير معروف') . "\n";
    }
}

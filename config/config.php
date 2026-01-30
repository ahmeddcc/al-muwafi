<?php
/**
 * ملف الإعدادات الرئيسي
 * نظام المُوَفِّي لمهمات المكاتب
 */

// منع الوصول المباشر
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

// إعدادات التطبيق
define('APP_NAME', 'المُوَفِّي لمهمات المكاتب');
define('APP_VERSION', '1.0.0');
define('APP_LANG', 'ar');
define('APP_DIR', 'rtl');
define('APP_CHARSET', 'UTF-8');

// إعدادات المسارات
define('BASE_URL', 'http://localhost/al-muwafi');
define('PUBLIC_PATH', APP_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('STORAGE_PATH', APP_PATH . '/storage');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('CACHE_PATH', STORAGE_PATH . '/cache');

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'al_muwafi_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// إعدادات الجلسات
define('SESSION_NAME', 'muwafi_session');
define('SESSION_LIFETIME', 7200); // ساعتين

// إعدادات الأمان
define('CSRF_TOKEN_NAME', 'csrf_token');
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

// إعدادات الملفات
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 ميجابايت
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// إعدادات Telegram
define('TELEGRAM_BOT_TOKEN', '');
define('TELEGRAM_OWNER_CHAT_ID', '');
define('TELEGRAM_ERROR_CHAT_ID', '');

// إعدادات الذكاء الاصطناعي
define('AI_API_KEY', '');
define('AI_API_URL', '');

// إعدادات الحماية (يمكن تفعيلها/تعطيلها من لوحة التحكم)
$securitySettings = [
    'disable_right_click' => false,
    'disable_inspect' => false,
    'disable_f12' => false,
    'disable_copy' => false,
    'enable_rate_limiting' => true,
    'rate_limit_requests' => 100,
    'rate_limit_window' => 60, // ثانية
];

// إعدادات التطوير
define('DEBUG_MODE', true);
define('SHOW_ERRORS', true);

// تحميل الإعدادات من قاعدة البيانات (عند الاتصال)
function loadDatabaseSettings(): array {
    // سيتم تحميلها لاحقاً من قاعدة البيانات
    return [];
}

<?php
/**
 * خدمة الأمان
 * نظام المُوَفِّي لمهمات المكاتب
 */

namespace App\Services;

class Security {
    private static array $settings = [];
    
    /**
     * تهيئة إعدادات الأمان
     */
    public static function init(): void {
        // تحميل الإعدادات من قاعدة البيانات
        self::loadSettings();
        
        // إعداد الجلسة
        self::setupSession();
        
        // إعداد رؤوس الأمان
        self::setSecurityHeaders();
    }
    
    /**
     * تحميل إعدادات الأمان
     */
    private static function loadSettings(): void {
        try {
            $db = Database::getInstance();
            $settings = $db->fetchAll(
                "SELECT setting_key, setting_value FROM settings WHERE setting_group = 'security'"
            );
            
            foreach ($settings as $setting) {
                self::$settings[$setting['setting_key']] = $setting['setting_value'];
            }
        } catch (\Exception $e) {
            // استخدام القيم الافتراضية
            self::$settings = [
                'security_disable_right_click' => '0',
                'security_disable_inspect' => '0',
                'security_disable_f12' => '0',
                'security_disable_copy' => '0',
                'security_rate_limiting' => '1',
                'security_rate_limit_requests' => '100',
                'security_rate_limit_window' => '60',
            ];
        }
    }
    
    /**
     * إعداد الجلسة الآمنة
     */
    private static function setupSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionName = defined('SESSION_NAME') ? SESSION_NAME : 'muwafi_session';
            $sessionLifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 7200;
            
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
            ini_set('session.use_strict_mode', '1');
            
            session_name($sessionName);
            session_start();
            
            // تجديد معرف الجلسة بشكل دوري
            if (!isset($_SESSION['_last_regeneration'])) {
                $_SESSION['_last_regeneration'] = time();
            } elseif (time() - (int)$_SESSION['_last_regeneration'] > 300) {
                session_regenerate_id(true);
                $_SESSION['_last_regeneration'] = time();
            }
        }
    }
    
    /**
     * إعداد رؤوس الأمان
     */
    private static function setSecurityHeaders(): void {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header("Content-Security-Policy: frame-ancestors 'none'");
        }
    }
    
    /**
     * توليد رمز CSRF
     */
    public static function generateCsrfToken(): string {
        $tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
        
        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION[$tokenName];
    }
    
    /**
     * التحقق من رمز CSRF
     */
    public static function verifyCsrfToken(?string $token): bool {
        $tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
        
        if (!$token || !isset($_SESSION[$tokenName])) {
            return false;
        }
        
        return hash_equals($_SESSION[$tokenName], $token);
    }
    
    /**
     * الحصول على حقل CSRF المخفي
     */
    public static function csrfField(): string {
        $tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
        $token = self::generateCsrfToken();
        return sprintf('<input type="hidden" name="%s" value="%s">', $tokenName, $token);
    }
    
    /**
     * تنظيف المدخلات من XSS
     */
    public static function sanitize(mixed $input): mixed {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        
        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return $input;
    }
    
    /**
     * تنظيف للعرض
     */
    public static function escape(mixed $value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * التحقق من حد الطلبات
     */
    public static function checkRateLimit(): bool {
        // التحقق من تفعيل الحماية
        if (!isset(self::$settings['security_rate_limiting']) || self::$settings['security_rate_limiting'] !== '1') {
            return true;
        }
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $maxRequests = (int) (self::$settings['security_rate_limit_requests'] ?? 100);
        $window = (int) (self::$settings['security_rate_limit_window'] ?? 60);
        
        $key = 'rate_limit_' . hash('sha256', $ip);
        $cachePath = (defined('CACHE_PATH') ? CACHE_PATH : dirname(__DIR__, 2) . '/storage/cache');
        $cacheFile = $cachePath . '/' . $key . '.json';
        
        if (!is_dir($cachePath)) {
            @mkdir($cachePath, 0755, true);
        }
        
        // قيم افتراضية آمنة
        $data = ['count' => 0, 'start' => time()];
        
        if (file_exists($cacheFile)) {
            $raw = @file_get_contents($cacheFile);
            if ($raw !== false) {
                $decoded = @json_decode($raw, true);
                // التحقق الصارم من صحة البيانات
                if (is_array($decoded) && isset($decoded['count']) && isset($decoded['start'])) {
                    $data = [
                        'count' => (int) $decoded['count'],
                        'start' => (int) $decoded['start']
                    ];
                }
            }
        }
        
        // إعادة تعيين إذا انتهت النافذة الزمنية (مع تحويل صريح للأنواع)
        $currentTime = time();
        $startTime = (int) $data['start'];
        
        if (($currentTime - $startTime) > $window) {
            $data = ['count' => 0, 'start' => $currentTime];
        }
        
        $data['count'] = (int) $data['count'] + 1;
        @file_put_contents($cacheFile, json_encode($data), LOCK_EX);
        
        return $data['count'] <= $maxRequests;
    }
    
    /**
     * الحصول على كود JavaScript للحماية
     */
    public static function getFrontendProtectionScript(): string {
        $scripts = [];
        
        if (self::$settings['security_disable_right_click'] === '1') {
            $scripts[] = "document.addEventListener('contextmenu', e => e.preventDefault());";
        }
        
        if (self::$settings['security_disable_f12'] === '1' || 
            self::$settings['security_disable_inspect'] === '1') {
            $scripts[] = "document.addEventListener('keydown', function(e) {
                if (e.key === 'F12' || 
                    (e.ctrlKey && e.shiftKey && ['I','J','C'].includes(e.key.toUpperCase())) ||
                    (e.ctrlKey && e.key.toUpperCase() === 'U')) {
                    e.preventDefault();
                }
            });";
        }
        
        if (self::$settings['security_disable_copy'] === '1') {
            $scripts[] = "document.addEventListener('copy', e => e.preventDefault());
                         document.addEventListener('cut', e => e.preventDefault());";
        }
        
        if (empty($scripts)) {
            return '';
        }
        
        return '<script>(function(){' . implode('', $scripts) . '})();</script>';
    }
    
    /**
     * تشفير كلمة المرور
     */
    public static function hashPassword(string $password): string {
        $algo = defined('HASH_ALGO') ? HASH_ALGO : PASSWORD_BCRYPT;
        $cost = defined('HASH_COST') ? HASH_COST : 12;
        
        return password_hash($password, $algo, ['cost' => $cost]);
    }
    
    /**
     * التحقق من كلمة المرور
     */
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * توليد رمز عشوائي
     */
    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length / 2));
    }
}

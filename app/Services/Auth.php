<?php
/**
 * خدمة المصادقة
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Services;

class Auth {
    private static ?array $currentUser = null;
    private static ?RBAC $rbac = null;
    
    /**
     * تسجيل الدخول
     */
    public static function login(string $username, string $password): array {
        $db = Database::getInstance();
        
        // البحث عن المستخدم
        $user = $db->fetchOne(
            "SELECT u.*, r.name as role_name, r.name_ar as role_name_ar 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE (u.username = :username OR u.email = :email) AND u.is_active = 1",
            ['username' => $username, 'email' => $username]
        );
        
        if (!$user) {
            self::logActivity(null, 'login_failed', 'محاولة دخول فاشلة - مستخدم غير موجود: ' . $username);
            return ['success' => false, 'message' => 'بيانات الدخول غير صحيحة'];
        }
        
        // التحقق من كلمة المرور
        if (!Security::verifyPassword($password, $user['password'])) {
            self::logActivity($user['id'], 'login_failed', 'محاولة دخول فاشلة - كلمة مرور خاطئة');
            return ['success' => false, 'message' => 'بيانات الدخول غير صحيحة'];
        }
        
        // تحديث آخر دخول
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
        
        // حفظ بيانات الجلسة
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'role_name_ar' => $user['role_name_ar'],
            'avatar' => $user['avatar'],
        ];
        $_SESSION['login_time'] = time();
        
        // تجديد معرف الجلسة
        session_regenerate_id(true);
        
        self::logActivity($user['id'], 'login_success', 'تسجيل دخول ناجح');
        
        return ['success' => true, 'user' => $_SESSION['user_data']];
    }
    
    /**
     * تسجيل الخروج
     */
    public static function logout(): void {
        if (self::check()) {
            self::logActivity($_SESSION['user_id'] ?? null, 'logout', 'تسجيل خروج');
        }
        
        // مسح بيانات الجلسة
        $_SESSION = [];
        
        // حذف كوكي الجلسة
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    /**
     * التحقق من تسجيل الدخول
     */
    public static function check(): bool {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_data']);
    }
    
    /**
     * الحصول على المستخدم الحالي
     */
    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }
        
        if (self::$currentUser === null) {
            self::$currentUser = $_SESSION['user_data'];
        }
        
        return self::$currentUser;
    }
    
    /**
     * الحصول على معرف المستخدم الحالي
     */
    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * التحقق من صلاحية معينة
     */
    public static function can(string $permission): bool {
        if (!self::check()) {
            return false;
        }
        
        // المستخدم admin أو Super Admin لديه كل الصلاحيات
        $user = self::user();
        if ($user && self::isSuperAdminUser($user)) {
            return true;
        }
        
        $rbac = self::getRBAC();
        return $rbac->hasPermission($_SESSION['user_data']['role_id'], $permission);
    }
    
    /**
     * التحقق من أي صلاحية من مجموعة
     */
    public static function canAny(array $permissions): bool {
        foreach ($permissions as $permission) {
            if (self::can($permission)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * التحقق من جميع الصلاحيات
     */
    public static function canAll(array $permissions): bool {
        foreach ($permissions as $permission) {
            if (!self::can($permission)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * التحقق من كون المستخدم مدير نظام
     */
    public static function isSuperAdmin(): bool {
        return self::check() && ($_SESSION['user_data']['role_name'] ?? '') === 'super_admin';
    }
    
    /**
     * طلب المصادقة
     */
    public static function requireAuth(): void {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/admin';
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login');
            exit;
        }
    }
    
    /**
     * طلب صلاحية معينة
     */
    public static function requirePermission(string $permission): void {
        self::requireAuth();
        
        // المستخدم admin أو Super Admin لديه كل الصلاحيات
        $user = self::user();
        if ($user && self::isSuperAdminUser($user)) {
            return;
        }
        
        if (!self::can($permission)) {
            http_response_code(403);
            include VIEWS_PATH . '/errors/403.php';
            exit;
        }
    }
    
    /**
     * التحقق من كون المستخدم Super Admin
     * فقط role_id = 1 (مدير النظام) يتجاوز الصلاحيات
     * جميع الأدوار الأخرى بما فيها المدير (admin) تتبع الصلاحيات المخصصة لها
     */
    private static function isSuperAdminUser(array $user): bool {
        // فقط role_id = 1 يتجاوز الصلاحيات
        return (($user['role_id'] ?? 0) == 1);
    }
    
    /**
     * الحصول على خدمة RBAC
     */
    private static function getRBAC(): RBAC {
        if (self::$rbac === null) {
            self::$rbac = new RBAC();
        }
        return self::$rbac;
    }
    
    /**
     * تسجيل نشاط المستخدم
     */
    public static function logActivity(?int $userId, string $action, string $description = ''): void {
        try {
            $db = Database::getInstance();
            $db->insert('user_activity_logs', [
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Exception $e) {
            // تجاهل أخطاء التسجيل
        }
    }
    
    /**
     * تحديث بيانات المستخدم في الجلسة
     */
    public static function refreshUser(): void {
        if (!self::check()) {
            return;
        }
        
        $db = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT u.*, r.name as role_name, r.name_ar as role_name_ar 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.id = :id AND u.is_active = 1",
            ['id' => $_SESSION['user_id']]
        );
        
        if (!$user) {
            self::logout();
            return;
        }
        
        $_SESSION['user_data'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'role_name_ar' => $user['role_name_ar'],
            'avatar' => $user['avatar'],
        ];
        
        self::$currentUser = null;
    }
}

<?php
/**
 * نقطة الدخول الرئيسية
 * نظام المُوَفِّي لمهمات المكاتب
 */

// تعريف المسار الجذري
define('APP_PATH', dirname(__DIR__));

// تحميل الإعدادات
require_once APP_PATH . '/config/config.php';

// تحميل autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Services\Security;
use App\Services\Auth;
use App\Services\Settings;
use App\Services\ErrorLogger;

// معالجة الأخطاء
set_error_handler(function ($severity, $message, $file, $line) {
    ErrorLogger::logError('php_error', $message, $file, $line, 
        in_array($severity, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR]) ? 'critical' : 'medium'
    );
    
    if (DEBUG_MODE && SHOW_ERRORS) {
        return false; // عرض الخطأ
    }
    return true; // إخفاء الخطأ
});

set_exception_handler(function ($exception) {
    ErrorLogger::logError(
        'exception',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        'critical'
    );
    
    if (DEBUG_MODE && SHOW_ERRORS) {
        echo '<pre>' . $exception->getMessage() . '</pre>';
    } else {
        http_response_code(500);
        include VIEWS_PATH . '/errors/500.php';
    }
});

// تهيئة الأمان
Security::init();

// التحقق من حد الطلبات
if (!Security::checkRateLimit()) {
    http_response_code(429);
    die('طلبات كثيرة جداً. الرجاء المحاولة لاحقاً.');
}

// التحقق من وضع الصيانة
if (Settings::isMaintenanceMode() && !Auth::isSuperAdmin()) {
    include VIEWS_PATH . '/errors/maintenance.php';
    exit;
}

// الحصول على المسار
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));
$path = parse_url(str_replace($basePath, '', $requestUri), PHP_URL_PATH);
$path = trim($path, '/');

// تقسيم المسار
$segments = $path ? explode('/', $path) : [];

// التوجيه
try {
    if (!isset($segments) || !is_array($segments)) {
        $segments = [];
    }
    $router = new Router($segments);
    $router->dispatch();
} catch (\Throwable $e) {
    // التقاط أي خطأ قاتل في التوجيه
    ErrorLogger::logError(
        'routing_error',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        'critical'
    );
    http_response_code(500);
    include VIEWS_PATH . '/errors/500.php';
}

/**
 * فئة التوجيه
 */
class Router {
    private array $segments;
    private string $controller = 'Home';
    private string $action = 'index';
    private array $params = [];
    private bool $isAdmin = false;
    
    public function __construct(array $segments) {
        $this->segments = $segments;
        $this->parseRoute();
    }
    
    private function parseRoute(): void {
        if (empty($this->segments)) {
            return;
        }
        
        // التحقق من لوحة التحكم
        if ($this->segments[0] === 'admin') {
            $this->isAdmin = true;
            array_shift($this->segments);
            
            if (empty($this->segments)) {
                $this->controller = 'Dashboard';
                return;
            }
            
            // مسارات خاصة للوحة التحكم
            $adminRoutes = [
                'login' => ['Auth', 'login'],
                'logout' => ['Auth', 'logout'],
            ];
            
            $firstSegment = $this->segments[0] ?? '';
            if (isset($adminRoutes[$firstSegment])) {
                $this->controller = $adminRoutes[$firstSegment][0];
                $this->action = $adminRoutes[$firstSegment][1];
                return;
            }
        }
        
        // تحديد الـ Controller
        if (!empty($this->segments[0])) {
            $this->controller = $this->formatControllerName($this->segments[0]);
            array_shift($this->segments);
        }
        
        // تحديد الـ Action
        if (!empty($this->segments[0])) {
            $this->action = $this->formatActionName($this->segments[0]);
            array_shift($this->segments);
        }
        
        // باقي الـ segments كـ params
        $this->params = $this->segments;
    }
    
    public function dispatch(): void {
        $namespace = $this->isAdmin ? 'App\\Controllers\\Admin\\' : 'App\\Controllers\\';
        $controllerClass = $namespace . $this->controller . 'Controller';
        
        // التحقق من وجود الـ Controller
        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }
        
        $controller = new $controllerClass();
        
        // التحقق من وجود الـ Action
        if (!method_exists($controller, $this->action)) {
            $this->notFound();
            return;
        }
        
        // طلب المصادقة للوحة التحكم
        if ($this->isAdmin && $this->controller !== 'Auth') {
            Auth::requireAuth();
        }
        
        // تنفيذ الـ Action
        call_user_func_array([$controller, $this->action], $this->params);
    }
    
    private function formatControllerName(string $name): string {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }
    
    private function formatActionName(string $name): string {
        $formatted = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
        return lcfirst($formatted);
    }
    
    private function notFound(): void {
        http_response_code(404);
        include VIEWS_PATH . '/errors/404.php';
    }
}

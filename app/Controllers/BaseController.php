<?php
/**
 * الفئة الأساسية للتحكم
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

use App\Services\Database;
use App\Services\Auth;
use App\Services\Security;
use App\Services\Settings;
use App\Services\NotificationService;

abstract class BaseController {
    protected ?Database $db = null;
    protected array $data = [];
    
    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->data['settings'] = Settings::getCompanyInfo();
            $this->data['social'] = Settings::getSocialLinks();
            $this->data['user'] = Auth::user();
            $this->data['notifications'] = NotificationService::getCounts();
        } catch (\Exception $e) {
            $this->data['settings'] = ['name' => 'المُوَفِّي لخدمات ريكو'];
            $this->data['social'] = [];
            $this->data['user'] = null;
        }
        $this->data['csrf_field'] = Security::csrfField();
    }
    
    /**
     * عرض صفحة
     */
    protected function view(string $view, array $data = []): void {
        $data = array_merge($this->data, $data);
        extract($data);
        
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        
        include $viewPath;
    }
    
    /**
     * إرجاع JSON
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * إعادة التوجيه
     */
    protected function redirect(string $url, array $flash = []): void {
        foreach ($flash as $key => $value) {
            $_SESSION['flash'][$key] = $value;
        }
        
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . $url;
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * الحصول على بيانات POST
     */
    protected function input(string $key, mixed $default = null): mixed {
        return Security::sanitize($_POST[$key] ?? $default);
    }
    
    /**
     * الحصول على جميع بيانات POST
     */
    protected function allInput(): array {
        return Security::sanitize($_POST);
    }
    
    /**
     * الحصول على بيانات GET
     */
    protected function query(string $key, mixed $default = null): mixed {
        return Security::sanitize($_GET[$key] ?? $default);
    }
    
    /**
     * التحقق من CSRF
     */
    protected function validateCsrf(): bool {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return Security::verifyCsrfToken($token);
    }
    
    /**
     * الحصول على رسالة Flash
     */
    protected function getFlash(string $key): ?string {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
    /**
     * التحقق من الطلب Ajax
     */
    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * التحقق من طريقة الطلب
     */
    protected function isMethod(string $method): bool {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }
    
    /**
     * طلب صلاحية
     */
    protected function requirePermission(string $permission): void {
        Auth::requirePermission($permission);
    }
}

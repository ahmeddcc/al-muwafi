<?php
/**
 * وحدة تحكم المصادقة (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth;

class AuthController extends BaseController {
    
    /**
     * صفحة تسجيل الدخول
     */
    public function login(): void {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }
        
        $this->view('admin.auth.login', [
            'title' => 'تسجيل الدخول',
            'error' => $this->getFlash('error'),
        ]);
    }
    
    /**
     * معالجة تسجيل الدخول
     */
    public function authenticate(): void {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/auth/login', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $username = $this->input('username');
        $password = $this->input('password');
        
        if (empty($username) || empty($password)) {
            $this->redirect('/admin/auth/login', ['error' => 'الرجاء إدخال اسم المستخدم وكلمة المرور']);
            return;
        }
        
        $result = Auth::login($username, $password);
        
        if (!$result['success']) {
            $this->redirect('/admin/auth/login', ['error' => $result['message']]);
            return;
        }
        
        $redirectTo = $_SESSION['redirect_after_login'] ?? '/admin';
        unset($_SESSION['redirect_after_login']);
        
        // تعيين علامة لإظهار نافذة الترحيب
        $_SESSION['show_welcome_modal'] = true;
        
        $this->redirect($redirectTo);
    }
    
    /**
     * تسجيل الخروج
     */
    public function logout(): void {
        Auth::logout();
        // عرض صفحة تسجيل الخروج بدلاً من إعادة التوجيه المباشر
        $this->view('admin.auth.logout', [
            'title' => 'تم تسجيل الخروج'
        ]);
    }
}

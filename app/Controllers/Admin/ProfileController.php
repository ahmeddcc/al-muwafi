<?php
/**
 * وحدة تحكم الملف الشخصي
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth;
use App\Services\Security;
use App\Services\ImageProcessor;

class ProfileController extends BaseController {
    
    private ImageProcessor $imageProcessor;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
    }
    
    /**
     * عرض الملف الشخصي
     */
    public function index(): void {
        $user = Auth::user();
        if (!$user) {
            $this->redirect('/admin/auth/login');
            return;
        }
        
        $this->view('admin.profile.index', [
            'title' => 'الملف الشخصي',
            'user' => $user
        ]);
    }
    
    /**
     * تحديث الملف الشخصي
     */
    public function update(): void {
        if (!$this->isMethod('POST')) {
            $this->redirect('/admin/profile');
            return;
        }
        
        if (!$this->validateCsrf()) {
            $this->redirect('/admin/profile', ['error' => 'رمز التحقق غير صالح']);
            return;
        }
        
        $user = Auth::user();
        $userId = $user['id'] ?? 0;
        
        // البيانات المدخلة
        $data = [
            'full_name' => $this->input('full_name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'username' => $this->input('username'),
        ];
        
        // التحقق من صحة البيانات
        $errors = $this->validateProfileData($data, $userId);
        
        if (!empty($errors)) {
            $this->redirect('/admin/profile', ['error' => implode('<br>', $errors)]);
            return;
        }
        
        try {
            // تحديث الصورة الشخصية
            if (!empty($_FILES['avatar']['name'])) {
                try {
                    $avatarPath = $this->imageProcessor->upload($_FILES['avatar'], 'avatars');
                    $data['avatar'] = $avatarPath;
                    
                    // حذف الصورة القديمة
                    if (!empty($user['avatar'])) {
                        $this->imageProcessor->delete($user['avatar']);
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            // تحديث كلمة المرور إذا تم إدخالها
            $password = $this->input('password');
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $this->redirect('/admin/profile', ['error' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل']);
                    return;
                }
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            // تنفيذ التحديث
            $updateFields = [];
            $params = [];
            
            foreach ($data as $key => $value) {
                $updateFields[] = "$key = :$key";
                $params[$key] = $value;
            }
            
            $params['id'] = $userId;
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $this->db->query($sql, $params);
            
            // تحديث بيانات الجلسة عن طريق Auth Helper
            Auth::refreshUser();
            
            $this->redirect('/admin/profile', ['success' => 'تم تحديث الملف الشخصي بنجاح']);
            
        } catch (\Exception $e) {
            $this->redirect('/admin/profile', ['error' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
        }
    }
    
    /**
     * التحقق من البيانات
     */
    private function validateProfileData(array $data, int $userId): array {
        $errors = [];
        
        if (empty($data['full_name'])) {
            $errors[] = 'الاسم الكامل مطلوب';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        if (empty($data['username'])) {
            $errors[] = 'اسم المستخدم مطلوب';
        }
        
        // التحقق من التكرار
        $exists = $this->db->fetchOne("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?", 
            [$data['email'], $data['username'], $userId]);
            
        if ($exists) {
            $errors[] = 'البريد الإلكتروني أو اسم المستخدم مسجل مسبقاً لمستخدم آخر';
        }
        
        return $errors;
    }
}

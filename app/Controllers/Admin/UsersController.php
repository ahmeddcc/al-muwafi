<?php
/**
 * وحدة تحكم المستخدمين (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth;
use App\Services\Security;
use App\Services\ImageProcessor;
use App\Services\RBAC;

class UsersController extends BaseController {
    private ImageProcessor $imageProcessor;
    private RBAC $rbac;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
        $this->rbac = new RBAC();
    }
    
    /**
     * قائمة المستخدمين
     */
    public function index(): void {
        $this->requirePermission('users.view');
        
        $page = (int) $this->query('page', 1);
        $search = trim($this->query('search', ''));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $where = '1=1';
        $params = [];
        
        // إخفاء المستخدمين المخفيين عن غير مدير النظام
        $currentUser = Auth::user();
        $isSuperAdmin = ($currentUser['role_id'] ?? 0) == 1;
        
        if (!$isSuperAdmin) {
            // المستخدم ليس super_admin - إخفاء المستخدمين المخفيين
            $where .= " AND (u.is_hidden = 0 OR u.is_hidden IS NULL)";
        }
        
        if ($search) {
            $where .= " AND (u.full_name LIKE :search OR u.username LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        // جلب العدد الكلي
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users u WHERE $where", $params);
        $totalPages = ceil($total / $limit);
        
        // جلب البيانات مع الترحيل
        $users = $this->db->fetchAll(
            "SELECT u.*, r.name_ar as role_name_ar 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE $where
             ORDER BY u.created_at DESC 
             LIMIT $limit OFFSET $offset",
            $params
        );
        
        $this->view('admin.users.index', [
            'title' => 'إدارة المستخدمين',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'total' => $total,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }
    
    /**
     * إنشاء مستخدم جديد
     */
    public function create(): void {
        $this->requirePermission('users.create');
        
        $roles = $this->rbac->getAllRoles();
        
        $this->view('admin.users.form', [
            'title' => 'إضافة مستخدم جديد',
            'user' => null,
            'roles' => $roles,
        ]);
    }
    
    /**
     * حفظ مستخدم جديد
     */
    public function store(): void {
        $this->requirePermission('users.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/users', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->prepareData();
        $errors = $this->validateData($data);
        
        if (!empty($errors)) {
            $this->redirect('/admin/users/create', ['error' => implode('<br>', $errors)]);
            return;
        }
        
        // تشفير كلمة المرور
        $data['password'] = Security::hashPassword($data['password']);
        
        // معالجة الصورة
        if (!empty($_FILES['avatar']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['avatar'], 'avatars');
            if ($result['success']) {
                $data['avatar'] = $result['path'];
            }
        }
        
        unset($data['password_confirm']);
        
        $this->db->insert('users', $data);
        
        $this->redirect('/admin/users', ['success' => 'تم إضافة المستخدم بنجاح']);
    }
    
    /**
     * تعديل مستخدم
     */
    public function edit(int $id): void {
        $this->requirePermission('users.edit');
        
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user) {
            $this->redirect('/admin/users', ['error' => 'المستخدم غير موجود']);
            return;
        }
        
        $roles = $this->rbac->getAllRoles();
        
        $this->view('admin.users.form', [
            'title' => 'تعديل المستخدم',
            'user' => $user,
            'roles' => $roles,
        ]);
    }
    
    /**
     * تحديث مستخدم
     */
    public function update(int $id): void {
        $this->requirePermission('users.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/users', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user) {
            $this->redirect('/admin/users', ['error' => 'المستخدم غير موجود']);
            return;
        }
        
        $data = $this->prepareData(false);
        $errors = $this->validateData($data, $id);
        
        if (!empty($errors)) {
            $this->redirect('/admin/users/edit/' . $id, ['error' => implode('<br>', $errors)]);
            return;
        }
        
        // تحديث كلمة المرور إذا تم إدخالها
        if (!empty($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        } else {
            unset($data['password']);
        }
        
        unset($data['password_confirm']);
        
        // معالجة الصورة
        if (!empty($_FILES['avatar']['name'])) {
            if ($user['avatar']) {
                $this->imageProcessor->delete($user['avatar']);
            }
            
            $result = $this->imageProcessor->upload($_FILES['avatar'], 'avatars');
            if ($result['success']) {
                $data['avatar'] = $result['path'];
            }
        }
        
        $this->db->update('users', $data, 'id = :id', ['id' => $id]);
        
        $this->redirect('/admin/users', ['success' => 'تم تحديث المستخدم بنجاح']);
    }
    
    /**
     * حذف مستخدم
     */
    public function delete(int $id): void {
        $this->requirePermission('users.delete');
        
        if ($id === Auth::id()) {
            $this->json(['success' => false, 'error' => 'لا يمكنك حذف حسابك'], 400);
            return;
        }
        
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user) {
            $this->json(['success' => false, 'error' => 'المستخدم غير موجود'], 404);
            return;
        }
        
        if ($user['avatar']) {
            $this->imageProcessor->delete($user['avatar']);
        }
        
        $this->db->delete('users', 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true, 'message' => 'تم حذف المستخدم بنجاح']);
    }

    /**
     * حذف متعدد للمستخدمين
     */
    public function bulkDelete(): void {
        $this->requirePermission('users.delete');
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'Invalid request'], 400);
            return;
        }
        
        $ids = $_POST['ids'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            $this->json(['success' => false, 'error' => 'No items selected'], 400);
            return;
        }
        
        $currentUserId = Auth::id();
        $deletedCount = 0;
        
        foreach ($ids as $id) {
            $id = (int) $id;
            
            // تخطي حذف النفس
            if ($id === $currentUserId) continue;
            
            $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
            if ($user) {
                if ($user['avatar']) {
                    $this->imageProcessor->delete($user['avatar']);
                }
                $this->db->delete('users', 'id = :id', ['id' => $id]);
                $deletedCount++;
            }
        }
        
        $this->json([
            'success' => true, 
            'message' => "تم حذف $deletedCount مستخدم بنجاح",
            'deletedCount' => $deletedCount
        ]);
    }
    
    /**
     * تبديل حالة المستخدم
     */
    public function toggleStatus(int $id): void {
        $this->requirePermission('users.toggle_status');
        
        if ($id === Auth::id()) {
            $this->json(['success' => false, 'error' => 'لا يمكنك تعطيل حسابك'], 400);
            return;
        }
        
        $user = $this->db->fetchOne("SELECT is_active FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user) {
            $this->json(['success' => false, 'error' => 'المستخدم غير موجود'], 404);
            return;
        }
        
        $newStatus = $user['is_active'] ? 0 : 1;
        $this->db->update('users', ['is_active' => $newStatus], 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true, 'is_active' => $newStatus]);
    }
    
    /**
     * سجل نشاط المستخدم
     */
    public function activity(int $id): void {
        $this->requirePermission('users.view');
        
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        
        if (!$user) {
            $this->redirect('/admin/users', ['error' => 'المستخدم غير موجود']);
            return;
        }
        
        $activities = $this->db->fetchAll(
            "SELECT * FROM user_activity_logs 
             WHERE user_id = :id 
             ORDER BY created_at DESC LIMIT 100",
            ['id' => $id]
        );
        
        $this->view('admin.users.activity', [
            'title' => 'سجل نشاط ' . $user['full_name'],
            'user' => $user,
            'activities' => $activities,
        ]);
    }
    
    private function prepareData(bool $requirePassword = true): array {
        // التحقق مما إذا كان المستخدم الحالي super_admin
        $currentUser = Auth::user();
        $isSuperAdmin = ($currentUser['role_id'] ?? 0) == 1;
        
        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'full_name' => $this->input('full_name'),
            'phone' => $this->input('phone'),
            'role_id' => (int) $this->input('role_id'),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password_confirm'),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];
        
        // فقط super_admin يمكنه تعيين is_hidden
        if ($isSuperAdmin) {
            $data['is_hidden'] = $this->input('is_hidden') ? 1 : 0;
        }
        
        return $data;
    }
    
    private function validateData(array $data, ?int $excludeId = null): array {
        $errors = [];
        
        if (empty($data['username'])) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif ($this->db->exists('users', 'username = :u' . ($excludeId ? ' AND id != :id' : ''), 
            $excludeId ? ['u' => $data['username'], 'id' => $excludeId] : ['u' => $data['username']])) {
            $errors[] = 'اسم المستخدم موجود مسبقاً';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        } elseif ($this->db->exists('users', 'email = :e' . ($excludeId ? ' AND id != :id' : ''),
            $excludeId ? ['e' => $data['email'], 'id' => $excludeId] : ['e' => $data['email']])) {
            $errors[] = 'البريد الإلكتروني موجود مسبقاً';
        }
        
        if (empty($data['full_name'])) {
            $errors[] = 'الاسم الكامل مطلوب';
        }
        
        if (!$excludeId && empty($data['password'])) {
            $errors[] = 'كلمة المرور مطلوبة';
        }
        
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
            }
            if ($data['password'] !== $data['password_confirm']) {
                $errors[] = 'كلمة المرور غير متطابقة';
            }
        }
        
        return $errors;
    }
}

<?php
/**
 * وحدة تحكم الأدوار والصلاحيات (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RolesController extends BaseController {
    
    /**
     * عرض قائمة الأدوار
     */
    public function index(): void {
        $this->requirePermission('roles.view');
        
        // جلب الأدوار مع عدد المستخدمين وعدد الصلاحيات
        $roles = $this->db->fetchAll(
            "SELECT r.*, 
                    COUNT(DISTINCT u.id) as users_count,
                    (SELECT COUNT(*) FROM role_permissions WHERE role_id = r.id) as permissions_count
             FROM roles r 
             LEFT JOIN users u ON r.id = u.role_id 
             GROUP BY r.id 
             ORDER BY r.id"
        );
        
        // جلب جميع الصلاحيات
        $permissions = $this->db->fetchAll("SELECT * FROM permissions ORDER BY group_name, name");
        
        // تجميع الصلاحيات حسب الموديول
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $group = $perm['group_name'] ?? 'general';
            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }
            $groupedPermissions[$group][] = $perm;
        }
        
        $this->view('admin.roles.index', [
            'title' => 'الأدوار والصلاحيات',
            'roles' => $roles,
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions,
        ]);
    }
    
    /**
     * نموذج إضافة دور
     */
    public function create(): void {
        $this->requirePermission('roles.create');
        
        $permissions = $this->db->fetchAll("SELECT * FROM permissions ORDER BY group_name, name");
        
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $group = $perm['group_name'] ?? 'general';
            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }
            $groupedPermissions[$group][] = $perm;
        }
        
        $this->view('admin.roles.form', [
            'title' => 'إضافة دور جديد',
            'role' => null,
            'rolePermissions' => [],
            'groupedPermissions' => $groupedPermissions,
        ]);
    }
    
    /**
     * حفظ دور جديد
     */
    public function store(): void {
        $this->requirePermission('roles.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/roles', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->allInput();
        
        if (empty($data['name'])) {
            $this->redirect('/admin/roles/create', ['error' => 'اسم الدور مطلوب']);
            return;
        }
        
        // التحقق من عدم تكرار الاسم
        $exists = $this->db->exists('roles', 'name = :name', ['name' => $data['name']]);
        if ($exists) {
            $this->redirect('/admin/roles/create', ['error' => 'اسم الدور موجود مسبقاً']);
            return;
        }
        
        // إنشاء الدور
        $this->db->insert('roles', [
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? $data['name'],
            'description' => $data['description'] ?? '',
        ]);
        
        $roleId = $this->db->lastInsertId();
        
        // إضافة الصلاحيات
        $rawPermissions = $_POST['permissions'] ?? [];
        if (!empty($rawPermissions) && is_array($rawPermissions)) {
            $rbac = new \App\Services\RBAC();
            $permissions = array_map('intval', $rawPermissions);
            $rbac->updateRolePermissions($roleId, $permissions);
        }
        
        $this->redirect('/admin/roles', ['success' => 'تم إضافة الدور بنجاح']);
    }
    
    /**
     * نموذج تعديل دور
     */
    public function edit(int $id): void {
        $this->requirePermission('roles.edit');
        
        $role = $this->db->fetchOne("SELECT * FROM roles WHERE id = :id", ['id' => $id]);
        
        if (!$role) {
            $this->redirect('/admin/roles', ['error' => 'الدور غير موجود']);
            return;
        }
        
        // جلب صلاحيات الدور الحالية
        $rolePermissions = $this->db->fetchAll(
            "SELECT permission_id FROM role_permissions WHERE role_id = :role_id",
            ['role_id' => $id]
        );
        $rolePermissionIds = array_column($rolePermissions, 'permission_id');
        
        // جلب جميع الصلاحيات
        $permissions = $this->db->fetchAll("SELECT * FROM permissions ORDER BY group_name, name");
        
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $group = $perm['group_name'] ?? 'general';
            if (!isset($groupedPermissions[$group])) {
                $groupedPermissions[$group] = [];
            }
            $groupedPermissions[$group][] = $perm;
        }
        
        $this->view('admin.roles.form', [
            'title' => 'تعديل الدور',
            'role' => $role,
            'rolePermissions' => $rolePermissionIds,
            'groupedPermissions' => $groupedPermissions,
        ]);
    }
    
    /**
     * تحديث دور
     */
    public function update(int $id): void {
        $this->requirePermission('roles.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/roles', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $role = $this->db->fetchOne("SELECT * FROM roles WHERE id = :id", ['id' => $id]);
        
        if (!$role) {
            $this->redirect('/admin/roles', ['error' => 'الدور غير موجود']);
            return;
        }
        
        $data = $this->allInput();
        
        // للأدوار المحمية (admin, super_admin): منع تغيير الاسم فقط، مع السماح بتعديل الصلاحيات
        $isProtectedRole = in_array($role['name'], ['admin', 'super_admin']);
        
        if (!$isProtectedRole) {
            // للأدوار العادية: السماح بتغيير كل شيء
            if (empty($data['name'])) {
                $this->redirect("/admin/roles/edit/$id", ['error' => 'اسم الدور مطلوب']);
                return;
            }
            
            // التحقق من عدم تكرار الاسم
            $exists = $this->db->exists('roles', 'name = :name AND id != :id', ['name' => $data['name'], 'id' => $id]);
            if ($exists) {
                $this->redirect("/admin/roles/edit/$id", ['error' => 'اسم الدور موجود مسبقاً']);
                return;
            }
            
            // تحديث بيانات الدور
            $this->db->update('roles', [
                'name' => $data['name'],
                'name_ar' => $data['name_ar'] ?? $data['name'],
                'description' => $data['description'] ?? '',
            ], 'id = :id', ['id' => $id]);
        } else {
            // للأدوار المحمية: تحديث الوصف فقط (الاسم يبقى ثابتاً)
            $this->db->update('roles', [
                'description' => $data['description'] ?? '',
            ], 'id = :id', ['id' => $id]);
        }
        
        // تحديث الصلاحيات باستخدام RBAC Service لضمان سلامة البيانات
        $rbac = new \App\Services\RBAC();
        // استخدام القيم مباشرة من POST لتجنب مشاكل التعقيم مع المصفوفات
        $rawPermissions = $_POST['permissions'] ?? [];
        $permissions = is_array($rawPermissions) ? array_map('intval', $rawPermissions) : [];
        
        $rbac->updateRolePermissions($id, $permissions);
        
        $this->redirect('/admin/roles', ['success' => 'تم تحديث الدور بنجاح']);
    }
    
    /**
     * حذف دور
     */
    public function delete(int $id): void {
        $this->requirePermission('roles.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        $role = $this->db->fetchOne("SELECT * FROM roles WHERE id = :id", ['id' => $id]);
        
        if (!$role) {
            $this->json(['success' => false, 'message' => 'الدور غير موجود'], 404);
            return;
        }
        
        // منع حذف دور المدير
        if ($role['name'] === 'admin') {
            $this->json(['success' => false, 'message' => 'لا يمكن حذف دور المدير'], 403);
            return;
        }
        
        // التحقق من عدم وجود مستخدمين بهذا الدور
        $usersCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role_id = :role_id",
            ['role_id' => $id]
        );
        
        if ($usersCount > 0) {
            $this->json(['success' => false, 'message' => "لا يمكن حذف الدور لوجود $usersCount مستخدم مرتبط به"], 400);
            return;
        }
        
        // حذف الصلاحيات أولاً
        $this->db->delete('role_permissions', 'role_id = :role_id', ['role_id' => $id]);
        
        // حذف الدور
        $this->db->delete('roles', 'id = :id', ['id' => $id]);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'تم حذف الدور']);
        } else {
            $this->redirect('/admin/roles', ['success' => 'تم حذف الدور']);
        }
    }
}

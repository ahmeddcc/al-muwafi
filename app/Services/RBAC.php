<?php
/**
 * خدمة الصلاحيات (RBAC)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Services;

class RBAC {
    private array $rolePermissions = [];
    private bool $loaded = false;
    
    /**
     * تحميل صلاحيات الأدوار
     */
    private function loadPermissions(): void {
        if ($this->loaded) {
            return;
        }
        
        try {
            $db = Database::getInstance();
            
            $results = $db->fetchAll(
                "SELECT rp.role_id, p.name as permission_name 
                 FROM role_permissions rp 
                 JOIN permissions p ON rp.permission_id = p.id"
            );
            
            foreach ($results as $row) {
                $this->rolePermissions[$row['role_id']][] = $row['permission_name'];
            }
            
            $this->loaded = true;
        } catch (\Exception $e) {
            ErrorLogger::logError('rbac_load', $e->getMessage(), __FILE__, __LINE__);
        }
    }
    
    /**
     * التحقق من صلاحية لدور معين
     */
    public function hasPermission(int $roleId, string $permission): bool {
        $this->loadPermissions();
        
        if (!isset($this->rolePermissions[$roleId])) {
            return false;
        }
        
        return in_array($permission, $this->rolePermissions[$roleId], true);
    }
    
    /**
     * الحصول على جميع صلاحيات دور
     */
    public function getRolePermissions(int $roleId): array {
        $this->loadPermissions();
        return $this->rolePermissions[$roleId] ?? [];
    }
    
    /**
     * الحصول على جميع الأدوار
     */
    public function getAllRoles(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM roles ORDER BY id");
    }
    
    /**
     * الحصول على جميع الصلاحيات مجمعة
     */
    public function getAllPermissionsGrouped(): array {
        $db = Database::getInstance();
        $permissions = $db->fetchAll("SELECT * FROM permissions ORDER BY group_name, id");
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['group_name']][] = $permission;
        }
        
        return $grouped;
    }
    
    /**
     * تحديث صلاحيات دور
     */
    public function updateRolePermissions(int $roleId, array $permissionIds): bool {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // حذف الصلاحيات القديمة
            $db->delete('role_permissions', 'role_id = :role_id', ['role_id' => $roleId]);
            
            // إضافة الصلاحيات الجديدة
            foreach ($permissionIds as $permissionId) {
                $db->insert('role_permissions', [
                    'role_id' => $roleId,
                    'permission_id' => (int) $permissionId
                ]);
            }
            
            $db->commit();
            
            // إعادة تحميل الصلاحيات
            $this->loaded = false;
            $this->rolePermissions = [];
            
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            ErrorLogger::logError('rbac_update', $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    /**
     * إنشاء دور جديد
     */
    public function createRole(array $data): ?int {
        $db = Database::getInstance();
        
        try {
            return $db->insert('roles', [
                'name' => $data['name'],
                'name_ar' => $data['name_ar'],
                'description' => $data['description'] ?? null,
                'is_system' => 0
            ]);
        } catch (\Exception $e) {
            ErrorLogger::logError('rbac_create_role', $e->getMessage(), __FILE__, __LINE__);
            return null;
        }
    }
    
    /**
     * تحديث دور
     */
    public function updateRole(int $id, array $data): bool {
        $db = Database::getInstance();
        
        try {
            $db->update('roles', [
                'name' => $data['name'],
                'name_ar' => $data['name_ar'],
                'description' => $data['description'] ?? null,
            ], 'id = :id AND is_system = 0', ['id' => $id]);
            
            return true;
        } catch (\Exception $e) {
            ErrorLogger::logError('rbac_update_role', $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    /**
     * حذف دور
     */
    public function deleteRole(int $id): bool {
        $db = Database::getInstance();
        
        // التحقق من أنه ليس دور نظام
        $role = $db->fetchOne("SELECT is_system FROM roles WHERE id = :id", ['id' => $id]);
        
        if (!$role || $role['is_system']) {
            return false;
        }
        
        // التحقق من عدم وجود مستخدمين بهذا الدور
        $usersCount = $db->count('users', 'role_id = :role_id', ['role_id' => $id]);
        
        if ($usersCount > 0) {
            return false;
        }
        
        try {
            $db->delete('roles', 'id = :id AND is_system = 0', ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            ErrorLogger::logError('rbac_delete_role', $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    /**
     * الحصول على أسماء مجموعات الصلاحيات بالعربي
     */
    public function getGroupNameAr(string $groupName): string {
        $groups = [
            'dashboard' => 'لوحة التحكم',
            'tickets' => 'التذاكر',
            'categories' => 'الأقسام',
            'products' => 'المنتجات',
            'spare_parts' => 'قطع الغيار',
            'services' => 'الخدمات',
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'pages' => 'الصفحات',
            'settings' => 'الإعدادات',
            'logs' => 'السجلات',
            'messages' => 'الرسائل',
        ];
        
        return $groups[$groupName] ?? $groupName;
    }
}

<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LogsController extends BaseController {
    
    /**
     * عرض سجلات النظام
     */
    public function index() {
        $this->requirePermission('logs.view');
        
        $page = (int) $this->query('page', 1);
        $search = trim($this->query('search', ''));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = '1=1';
        $params = [];
        
        if ($search) {
            $where .= " AND (l.description LIKE :search OR u.full_name LIKE :search OR l.action LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        // جلب العدد الكلي
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) 
             FROM user_activity_logs l 
             LEFT JOIN users u ON l.user_id = u.id 
             WHERE $where", 
            $params
        );
        $totalPages = ceil($total / $limit);
        
        // جلب البيانات مع الترحيل
        $logs = $this->db->fetchAll(
            "SELECT l.*, u.full_name, u.username, u.avatar 
             FROM user_activity_logs l 
             LEFT JOIN users u ON l.user_id = u.id 
             WHERE $where 
             ORDER BY l.created_at DESC 
             LIMIT $limit OFFSET $offset",
            $params
        );
        
        $this->view('admin.logs.index', [
            'title' => 'سجلات النظام',
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'total' => $total
        ]);
    }
    
    /**
     * مسح السجلات القديمة
     */
    public function clear() {
        // التحقق من الصلاحية أو أن المستخدم Super Admin
        $user = \App\Services\Auth::user();
        $isSuperAdmin = $user && (
            $user['role_id'] == 1 || 
            $user['username'] === 'admin' || 
            ($user['role_name'] ?? '') === 'super_admin'
        );
        
        if (!$isSuperAdmin) {
            $this->requirePermission('logs.delete');
        }
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/logs');
            return;
        }
        
        // حذف جميع السجلات
        $deleted = $this->db->query("DELETE FROM user_activity_logs");
        
        $this->redirect('/admin/logs', ['success' => 'تم مسح جميع السجلات بنجاح']);
    }
}

<?php
/**
 * وحدة تحكم الرسائل (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MessagesController extends BaseController {
    
    public function index(): void {
        $this->requirePermission('messages.view');
        
        $page = (int) $this->query('page', 1);
        $search = trim($this->query('search', ''));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $where = '1=1';
        $params = [];
        
        if ($search) {
            $where .= " AND (name LIKE :search OR email LIKE :search OR subject LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        // جلب العدد الكلي
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE $where", $params);
        $totalPages = ceil($total / $limit);
        
        // جلب البيانات
        $messages = $this->db->fetchAll(
            "SELECT * FROM contact_messages WHERE $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset", 
            $params
        );
        
        $this->view('admin.messages.index', [
            'title' => 'إدارة الرسائل',
            'messages' => $messages,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'total' => $total,
        ]);
    }

    public function bulkDelete(): void {
        $this->requirePermission('messages.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        $ids = $_POST['ids'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            $this->json(['success' => false, 'message' => 'لم يتم تحديد أي رسائل']);
            return;
        }
        
        // تنظيف المعرفات
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids);
        
        if (empty($ids)) {
            $this->json(['success' => false, 'message' => 'معرفات غير صالحة']);
            return;
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM contact_messages WHERE id IN ($placeholders)";
        
        $this->db->query($sql, array_values($ids));
        
        $this->json(['success' => true, 'message' => 'تم حذف الرسائل المحددة']);
    }
    
    public function show(int $id): void {
        $this->requirePermission('messages.view');
        
        $message = $this->db->fetchOne("SELECT * FROM contact_messages WHERE id = :id", ['id' => $id]);
        
        if (!$message) {
            $this->redirect('/admin/messages', ['error' => 'الرسالة غير موجودة']);
            return;
        }
        
        // تحديث حالة القراءة
        if (!$message['is_read']) {
            $this->db->update('contact_messages', ['is_read' => 1], 'id = :id', ['id' => $id]);
        }
        
        $this->view('admin.messages.view', [
            'title' => 'عرض الرسالة',
            'message' => $message,
        ]);
    }
    
    public function delete(int $id): void {
        $this->requirePermission('messages.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        $this->db->delete('contact_messages', 'id = :id', ['id' => $id]);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'تم حذف الرسالة']);
        } else {
            $this->redirect('/admin/messages', ['success' => 'تم حذف الرسالة']);
        }
    }
    
    public function markAsRead(int $id): void {
        $this->requirePermission('messages.view');
        
        $this->db->update('contact_messages', ['is_read' => 1], 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true]);
    }
}

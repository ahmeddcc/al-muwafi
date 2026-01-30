<?php
/**
 * نموذج التذكرة (Model)
 * نظام المُوَفِّي لمهمات المكاتب
 */

namespace App\Models;

use App\Services\Database;

class Ticket {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * جلب جميع التذاكر مع التصفية
     */
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array {
        $where = "1=1";
        $params = [];
        
        if (!empty($filters['status']) && in_array($filters['status'], ['new', 'received', 'in_progress', 'fixed', 'closed'])) {
            $where .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $where .= " AND (t.ticket_number LIKE :search OR t.customer_name LIKE :search2 OR t.customer_phone LIKE :search3)";
            $params['search'] = "%{$filters['search']}%";
            $params['search2'] = "%{$filters['search']}%";
            $params['search3'] = "%{$filters['search']}%";
        }
        
        return $this->db->fetchAll(
            "SELECT t.*, u.full_name as assigned_name,
             (SELECT COUNT(*) FROM maintenance_tickets t2 WHERE t2.customer_phone = t.customer_phone) as repetition_count
             FROM maintenance_tickets t 
             LEFT JOIN users u ON t.assigned_to = u.id 
             WHERE {$where} 
             ORDER BY t.created_at DESC 
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }
    
    /**
     * حساب عدد التذاكر
     */
    public function count(array $filters = []): int {
        $where = "1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $where .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $where .= " AND (ticket_number LIKE :search OR customer_name LIKE :search2 OR customer_phone LIKE :search3)";
            $params['search'] = "%{$filters['search']}%";
            $params['search2'] = "%{$filters['search']}%";
            $params['search3'] = "%{$filters['search']}%";
        }
        
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM maintenance_tickets t WHERE {$where}",
            $params
        );
    }
    
    /**
     * البحث عن تذكرة بواسطة المعرف
     */
    public function find(int $id): ?array {
        return $this->db->fetchOne(
            "SELECT t.*, u.full_name as assigned_name 
             FROM maintenance_tickets t 
             LEFT JOIN users u ON t.assigned_to = u.id 
             WHERE t.id = :id",
            ['id' => $id]
        );
    }
    
    /**
     * تحديث التذكرة
     */
    public function update(int $id, array $data): bool {
        return $this->db->update('maintenance_tickets', $data, 'id = :id', ['id' => $id]) > 0;
    }
    
    /**
     * جلب سجل الأحداث
     */
    public function getTimeline(int $ticketId): array {
        return $this->db->fetchAll(
            "SELECT tl.*, u.full_name as user_name 
             FROM ticket_timeline tl 
             LEFT JOIN users u ON tl.user_id = u.id 
             WHERE tl.ticket_id = :id 
             ORDER BY tl.created_at DESC",
            ['id' => $ticketId]
        );
    }
    
    /**
     * إضافة حدث للسجل
     */
    public function addTimeline(array $data): int {
        return $this->db->insert('ticket_timeline', $data);
    }
    
    /**
     * جلب المرفقات
     */
    public function getAttachments(int $ticketId): array {
        return $this->db->fetchAll(
            "SELECT ta.*, u.full_name as uploader_name 
             FROM ticket_attachments ta 
             LEFT JOIN users u ON ta.uploaded_by = u.id 
             WHERE ta.ticket_id = :id 
             ORDER BY ta.created_at DESC",
            ['id' => $ticketId]
        );
    }
    
    /**
     * جلب الفنيين المتاحين
     */
    public function getTechnicians(): array {
        return $this->db->fetchAll(
            "SELECT u.id, u.full_name FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE r.name IN ('technician', 'admin', 'super_admin') AND u.is_active = 1"
        );
    }

    /**
     * حذف تذكرة
     */
    public function delete(int $id): bool {
        // يمكن إضافة منطق لحذف الملفات المرتبطة هنا إذا لزم الأمر
        return $this->db->delete('maintenance_tickets', 'id = :id', ['id' => $id]) > 0;
    }

    /**
     * حذف مجموعة تذاكر
     */
    public function deleteMultiple(array $ids): int {
        if (empty($ids)) return 0;
        
        $deleted = 0;
        foreach ($ids as $id) {
            if ($this->db->delete('maintenance_tickets', 'id = :id', ['id' => (int)$id]) > 0) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
}

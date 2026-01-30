<?php

namespace App\Services;

class NotificationService {
    
    /**
     * الحصول على عدد الإشعارات غير المقروءة
     * يجمع بين التذاكر الجديدة والرسائل غير المقروءة
     */
    /**
     * الحصول على إحصائيات الإشعارات
     * يرجع مصفوفة تحتوي على تفاصيل الأعداد
     */
    public static function getCounts(): array {
        $db = Database::getInstance();
        $counts = [
            'total' => 0,
            'tickets' => 0,
            'messages' => 0
        ];
        
        try {
            // عدد التذاكر الجديدة
            $stmt = $db->query("SELECT COUNT(*) as count FROM maintenance_tickets WHERE status = 'new'");
            $counts['tickets'] = (int) $stmt->fetch()['count'];
            
            // عدد الرسائل غير المقروءة
            $stmt = $db->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
            $counts['messages'] = (int) $stmt->fetch()['count'];
            
            $counts['total'] = $counts['tickets'] + $counts['messages'];
            
        } catch (\Exception $e) {
            // في حال وجود خطأ في قاعدة البيانات n
            return $counts;
        }
        
        return $counts;
    }

    /**
     * الحصول على العدد الكلي فقط (لللنسخ القديمة)
     */
    public static function getUnreadCount(): int {
        return self::getCounts()['total'];
    }
}

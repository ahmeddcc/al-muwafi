<?php
/**
 * وحدة تحكم لوحة التحكم الرئيسية
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth;

class DashboardController extends BaseController {
    
    /**
     * لوحة التحكم الرئيسية
     */
    public function index(): void {
        $this->requirePermission('dashboard.view');
        
        // إحصائيات التذاكر
        $ticketStats = [
            'total' => $this->db->count('maintenance_tickets'),
            'new' => $this->db->count('maintenance_tickets', "status = 'new'"),
            'in_progress' => $this->db->count('maintenance_tickets', "status IN ('received', 'in_progress')"),
            'closed' => $this->db->count('maintenance_tickets', "status = 'closed'"),
            'today' => $this->db->count('maintenance_tickets', 'DATE(created_at) = CURDATE()'),
        ];
        
        // إحصائيات المحتوى
        $contentStats = [
            'products' => $this->db->count('products'),
            'categories' => $this->db->count('categories'),
            'spare_parts' => $this->db->count('spare_parts'),
            'services' => $this->db->count('services'),
        ];
        
        // آخر التذاكر
        $recentTickets = $this->db->fetchAll(
            "SELECT t.*, u.full_name as assigned_name 
             FROM maintenance_tickets t 
             LEFT JOIN users u ON t.assigned_to = u.id 
             ORDER BY t.created_at DESC LIMIT 5"
        );
        
        // الأعطال المتكررة
        $repeatedFaults = $this->db->fetchAll(
            "SELECT machine_model, fault_description, COUNT(*) as count 
             FROM maintenance_tickets 
             WHERE fault_type = 'repeated' 
             GROUP BY machine_model, fault_description 
             ORDER BY count DESC LIMIT 5"
        );
        
        // الرسائل غير المقروءة
        $unreadMessages = $this->db->count('contact_messages', 'is_read = 0');
        
        // آخر الإشعارات
        $notifications = $this->db->fetchAll(
            "SELECT * FROM system_notifications 
             WHERE user_id = :user_id AND is_read = 0 
             ORDER BY created_at DESC LIMIT 5",
            ['user_id' => Auth::id()]
        );

        // إحصائيات الرسم البياني (آخر 7 أيام)
        $chartData = [
            'dates' => [],
            'new' => [],
            'closed' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartData['dates'][] = date('D', strtotime($date)); // اسم اليوم
            
            // التذاكر الجديدة في هذا اليوم
            $chartData['new'][] = $this->db->count(
                'maintenance_tickets', 
                "DATE(created_at) = '$date' AND status = 'new'"
            );
            
            // التذاكر المنجزة في هذا اليوم
            $chartData['closed'][] = $this->db->count(
                'maintenance_tickets', 
                "DATE(updated_at) = '$date' AND status IN ('fixed', 'closed', 'delivered')"
            );
        }
        
        $this->view('admin.dashboard', [
            'title' => 'لوحة التحكم',
            'ticketStats' => $ticketStats,
            'contentStats' => $contentStats,
            'recentTickets' => $recentTickets,
            'repeatedFaults' => $repeatedFaults,
            'unreadMessages' => $unreadMessages,
            'notifications' => $notifications,
            'chartData' => $chartData
        ]);
    }
}

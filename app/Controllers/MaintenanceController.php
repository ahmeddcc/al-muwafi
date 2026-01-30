<?php
/**
 * وحدة تحكم التذاكر (الموقع العام)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

use App\Services\ImageProcessor;
use App\Services\TelegramService;

class MaintenanceController extends BaseController {
    private ImageProcessor $imageProcessor;
    private TelegramService $telegram;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
        $this->telegram = new TelegramService();
    }
    
    /**
     * صفحة طلب الصيانة
     */
    public function index(): void {
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'maintenance' AND is_active = 1");
        
        $this->view('public.maintenance', [
            'title' => $page['meta_title'] ?? 'طلب صيانة',
            'meta_description' => $page['meta_description'] ?? '',
        ]);
    }
    
    /**
     * إنشاء تذكرة جديدة
     */
    public function submit(): void {
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/maintenance', ['error' => 'طلب غير صالح']);
            return;
        }
        
        // جمع البيانات
        $data = [
            'customer_name' => $this->input('customer_name'),
            'customer_phone' => $this->input('customer_phone'),
            'customer_address' => $this->input('customer_address'),
            'machine_type' => $this->input('machine_type'),
            'machine_model' => $this->input('machine_model'),
            'fault_description' => $this->input('fault_description'),
            'error_code' => $this->input('error_code'),
            'fault_type' => $this->input('fault_type', 'new'),
            'repeat_count' => (int) $this->input('repeat_count', 0),
            'location_latitude' => $this->input('latitude') ?: null,
            'location_longitude' => $this->input('longitude') ?: null,
        ];
        
        // التحقق من البيانات
        $errors = $this->validateTicketData($data);
        if (!empty($errors)) {
            $this->redirect('/maintenance', ['error' => implode('<br>', $errors)]);
            return;
        }
        
        // توليد رقم التذكرة
        $data['ticket_number'] = $this->generateTicketNumber();
        $data['status'] = 'new';
        
        // معالجة صورة الموديل
        if (!empty($_FILES['model_image']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['model_image'], 'tickets');
            if ($result['success']) {
                $data['machine_model_image'] = $result['path'];
            }
        }
        
        // معالجة صورة الشاشة
        if (!empty($_FILES['screen_image']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['screen_image'], 'tickets');
            if ($result['success']) {
                $data['screen_image'] = $result['path'];
            }
        }
        
        try {
            $this->db->beginTransaction();
            
            // إدراج التذكرة
            $ticketId = $this->db->insert('maintenance_tickets', $data);
            
            // إضافة سجل الإنشاء
            $this->db->insert('ticket_timeline', [
                'ticket_id' => $ticketId,
                'action' => 'created',
                'new_value' => 'تم إنشاء التذكرة',
            ]);
            
            $this->db->commit();
            
            // إرسال إشعار Telegram
            $data['id'] = $ticketId;
            $this->telegram->notifyNewTicket($data);
            
            // إعادة التوجيه لصفحة النجاح
            $_SESSION['ticket_created'] = $data['ticket_number'];
            $this->redirect('/maintenance/success');
            
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->redirect('/maintenance', ['error' => 'حدث خطأ أثناء إنشاء التذكرة']);
        }
    }
    
    /**
     * صفحة نجاح إنشاء التذكرة
     */
    public function success(): void {
        $ticketNumber = $_SESSION['ticket_created'] ?? null;
        unset($_SESSION['ticket_created']);
        
        if (!$ticketNumber) {
            $this->redirect('/maintenance');
            return;
        }
        
        $ticket = $this->db->fetchOne(
            "SELECT * FROM maintenance_tickets WHERE ticket_number = :number",
            ['number' => $ticketNumber]
        );
        
        $this->view('public.maintenance-success', [
            'title' => 'تم إنشاء التذكرة بنجاح',
            'ticket' => $ticket,
        ]);
    }
    
    /**
     * البحث عن تذكرة
     */
    public function search(): void {
        $ticketNumber = $this->query('ticket_number') ?? $this->input('ticket_number');
        
        if (!$ticketNumber) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'الرجاء إدخال رقم التذكرة']);
            }
            $this->redirect('/maintenance');
            return;
        }
        
        $ticket = $this->db->fetchOne(
            "SELECT * FROM maintenance_tickets WHERE ticket_number = :number",
            ['number' => $ticketNumber]
        );
        
        if (!$ticket) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'لم يتم العثور على التذكرة']);
            }
            // Redirect back to maintenance with error to show in popup
            $this->redirect('/maintenance', ['search_error' => 'not_found', 'ticket' => $ticketNumber]);
            return;
        }
        
        // جلب سجل التذكرة
        $timeline = $this->db->fetchAll(
            "SELECT t.*, u.full_name as user_name 
             FROM ticket_timeline t 
             LEFT JOIN users u ON t.user_id = u.id 
             WHERE t.ticket_id = :id 
             ORDER BY t.created_at DESC",
            ['id' => $ticket['id']]
        );
        
        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'ticket' => $ticket,
                'timeline' => $timeline,
            ]);
        }
        
        $this->view('public.maintenance-status', [
            'title' => 'حالة التذكرة #' . $ticket['ticket_number'],
            'ticket' => $ticket,
            'timeline' => $timeline,
        ]);
    }
    
    /**
     * التحقق من بيانات التذكرة
     */
    private function validateTicketData(array $data): array {
        $errors = [];
        
        if (empty($data['customer_name'])) {
            $errors[] = 'اسم العميل مطلوب';
        }
        
        if (empty($data['customer_phone'])) {
            $errors[] = 'رقم الهاتف مطلوب';
        } elseif (!preg_match('/^[\d\s\-\+]{10,}$/', $data['customer_phone'])) {
            $errors[] = 'رقم الهاتف غير صالح';
        }
        
        if (empty($data['customer_address'])) {
            $errors[] = 'العنوان مطلوب';
        }
        
        if (empty($data['machine_type']) || !in_array($data['machine_type'], ['copier', 'printer'])) {
            $errors[] = 'نوع الجهاز مطلوب';
        }
        
        if (empty($data['fault_description'])) {
            $errors[] = 'وصف العطل مطلوب';
        }
        
        return $errors;
    }
    
    /**
     * توليد رقم تذكرة فريد
     */
    private function generateTicketNumber(): string {
        $prefix = 'TK';
        $date = date('ymd');
        $maxAttempts = 10;
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // عد التذاكر اليوم + محاولة إضافية
            $count = $this->db->count(
                'maintenance_tickets',
                'DATE(created_at) = CURDATE()'
            );
            
            // إضافة رقم عشوائي صغير للتفرد
            $sequence = $count + 1 + $attempt;
            $ticketNumber = $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            // التحقق من عدم وجود الرقم
            $exists = $this->db->exists(
                'maintenance_tickets',
                'ticket_number = :num',
                ['num' => $ticketNumber]
            );
            
            if (!$exists) {
                return $ticketNumber;
            }
        }
        
        // في حالة فشل كل المحاولات، إضافة طابع زمني عشوائي
        return $prefix . $date . substr(uniqid(), -6);
    }
}

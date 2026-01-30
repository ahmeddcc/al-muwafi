<?php
/**
 * وحدة تحكم التذاكر (لوحة التحكم)
 * نظام المُوَفِّي لمهمات المكاتب
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Auth;
use App\Services\TelegramService;
use App\Services\ImageProcessor;
use App\Models\Ticket;

class TicketsController extends BaseController {
    private TelegramService $telegram;
    private Ticket $ticketModel;
    
    public function __construct() {
        parent::__construct();
        $this->telegram = new TelegramService();
        $this->ticketModel = new Ticket();
    }
    
    /**
     * قائمة التذاكر
     */
    public function index(): void {
        $this->requirePermission('tickets.view');
        
        $status = $this->query('status');
        $search = $this->query('search');
        $page = max(1, (int) $this->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $filters = [
            'status' => $status,
            'search' => $search
        ];
        
        $total = $this->ticketModel->count($filters);
        $tickets = $this->ticketModel->getAll($filters, $perPage, $offset);
        $technicians = $this->ticketModel->getTechnicians();
        
        $this->view('admin.tickets.index', [
            'title' => 'إدارة التذاكر',
            'tickets' => $tickets,
            'technicians' => $technicians,
            'currentStatus' => $status,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total,
        ]);
    }
    
    /**
     * عرض تفاصيل تذكرة
     */
    public function show(int $id): void {
        $this->requirePermission('tickets.view');
        
        $ticket = $this->ticketModel->find($id);
        
        if (!$ticket) {
            $this->redirect('/admin/tickets', ['error' => 'التذكرة غير موجودة']);
            return;
        }
        
        $timeline = $this->ticketModel->getTimeline($id);
        $attachments = $this->ticketModel->getAttachments($id);
        $technicians = $this->ticketModel->getTechnicians();
        
        $this->view('admin.tickets.show', [
            'title' => 'تذكرة #' . $ticket['ticket_number'],
            'ticket' => $ticket,
            'timeline' => $timeline,
            'attachments' => $attachments,
            'technicians' => $technicians,
        ]);
    }
    
    /**
     * تحديث حالة التذكرة
     */
    public function updateStatus(int $id): void {
        $this->requirePermission('tickets.change_status');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $newStatus = $this->input('status');
        $notes = $this->input('notes');
        
        $validStatuses = ['new', 'received', 'in_progress', 'fixed', 'closed'];
        if (!in_array($newStatus, $validStatuses)) {
            $this->json(['success' => false, 'error' => 'حالة غير صالحة'], 400);
            return;
        }
        
        $ticket = $this->ticketModel->find($id);
        
        if (!$ticket) {
            $this->json(['success' => false, 'error' => 'التذكرة غير موجودة'], 404);
            return;
        }
        
        $oldStatus = $ticket['status'];
        
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'closed') {
            $updateData['closed_at'] = date('Y-m-d H:i:s');
        }
        
        $this->ticketModel->update($id, $updateData);
        
        // إضافة للسجل
        $this->ticketModel->addTimeline([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'notes' => $notes,
        ]);
        
        // إشعار Telegram
        $ticket['status'] = $newStatus;
        $this->telegram->notifyTicketUpdate($ticket, 'status_changed', $notes);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'تم تحديث الحالة بنجاح']);
        } else {
            $this->redirect('/admin/tickets/' . $id, ['success' => 'تم تحديث الحالة']);
        }
    }
    
    /**
     * تعيين فني
     */
    public function assign(int $id): void {
        $this->requirePermission('tickets.assign');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $technicianId = (int) $this->input('technician_id');
        
        $ticket = $this->ticketModel->find($id);
        
        if (!$ticket) {
            $this->json(['success' => false, 'error' => 'التذكرة غير موجودة'], 404);
            return;
        }
        
        $technician = $this->db->fetchOne("SELECT full_name FROM users WHERE id = :id", ['id' => $technicianId]);
        
        $this->ticketModel->update($id, ['assigned_to' => $technicianId]);
        
        $this->ticketModel->addTimeline([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'action' => 'assigned',
            'new_value' => $technician['full_name'] ?? 'غير معروف',
        ]);
        
        $this->json(['success' => true, 'message' => 'تم تعيين الفني بنجاح']);
    }
    
    /**
     * إضافة ملاحظة
     */
    public function addNote(int $id): void {
        $this->requirePermission('tickets.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $notes = $this->input('notes');
        
        if (empty($notes)) {
            $this->json(['success' => false, 'error' => 'الملاحظة مطلوبة'], 400);
            return;
        }
        
        $this->ticketModel->addTimeline([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'action' => 'note_added',
            'notes' => $notes,
        ]);
        
        $this->json(['success' => true, 'message' => 'تم إضافة الملاحظة']);
    }
    
    /**
     * رفع تقرير الإصلاح
     */
    public function uploadReport(int $id): void {
        $this->requirePermission('tickets.edit');
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        if (empty($_FILES['report']['name'])) {
            $this->json(['success' => false, 'error' => 'الرجاء اختيار ملف'], 400);
            return;
        }
        
        // التحقق من نوع الملف (PDF)
        $extension = strtolower(pathinfo($_FILES['report']['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['report']['tmp_name']);
        finfo_close($finfo);

        if ($extension !== 'pdf' || $mimeType !== 'application/pdf') {
            $this->json(['success' => false, 'error' => 'يجب أن يكون الملف PDF صالح'], 400);
            return;
        }
        
        $uploadDir = UPLOADS_PATH . '/reports/' . date('Y/m');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = 'report_' . $id . '_' . time() . '.pdf';
        $filePath = $uploadDir . '/' . $fileName;
        
        if (!move_uploaded_file($_FILES['report']['tmp_name'], $filePath)) {
            $this->json(['success' => false, 'error' => 'فشل في رفع الملف'], 500);
            return;
        }
        
        $relativePath = 'reports/' . date('Y/m') . '/' . $fileName;
        
        $this->ticketModel->update($id, ['repair_report' => $relativePath]);
        
        $this->ticketModel->addTimeline([
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'action' => 'report_uploaded',
            'new_value' => $fileName,
        ]);
        
    }

    /**
     * حذف تذكرة
     */
    public function delete(int $id): void {
        $this->requirePermission('tickets.delete');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        if ($this->ticketModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'تم حذف التذكرة بنجاح']);
        } else {
            $this->json(['success' => false, 'error' => 'فشل في حذف التذكرة'], 500);
        }
    }

    /**
     * حذف جماعي
     */
    public function bulkDelete(): void {
        $this->requirePermission('tickets.delete');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $ids = $this->input('ids');
        
        if (empty($ids) || !is_array($ids)) {
            $this->json(['success' => false, 'error' => 'لم يتم تحديد أي تذاكر'], 400);
            return;
        }
        
        // Sanitize IDs
        $ids = array_map('intval', $ids);
        
        $count = $this->ticketModel->deleteMultiple($ids);
        
        if ($count > 0) {
            $this->json(['success' => true, 'message' => "تم حذف $count تذكرة بنجاح"]);
        } else {
            $this->json(['success' => false, 'error' => 'فشل في حذف التذاكر المحددة'], 500);
        }
    }
}

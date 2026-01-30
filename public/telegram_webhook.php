<?php
/**
 * Webhook لبوت Telegram للمالك
 * نظام المُوَفِّي لمهمات المكاتب
 */

// تحميل الإعدادات
define('APP_PATH', dirname(__DIR__));
require_once APP_PATH . '/config/config.php';

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Services\Database;
use App\Services\TelegramService;
use App\Services\Settings;

// تسجيل أخطاء للتشخيص
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/webhook_errors.log');

// تسجيل الطلب الخام
$rawInput = file_get_contents('php://input');
file_put_contents(__DIR__ . '/../storage/logs/webhook_debug.log', date('[Y-m-d H:i:s] ') . "Received: " . $rawInput . "\n", FILE_APPEND);

$update = json_decode($rawInput, true);

if (!$update) {
    file_put_contents(__DIR__ . '/../storage/logs/webhook_debug.log', date('[Y-m-d H:i:s] ') . "Error: Empty or invalid JSON input\n", FILE_APPEND);
    exit;
}

$telegram = new TelegramService();
$db = Database::getInstance();

// التحقق من Chat ID المصرح
$allowedChatId = Settings::get('telegram_owner_chat_id');

// معالجة Callback Query (الأزرار)
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $chatId = $callback['message']['chat']['id'];
    $messageId = $callback['message']['message_id'];
    $data = $callback['data'];
    
    // التحقق من الصلاحية
    if ($chatId != $allowedChatId) {
        $telegram->answerCallbackQuery($callback['id'], '⛔ غير مصرح لك', true);
        exit;
    }
    
    // تحليل البيانات
    $parts = explode(':', $data);
    $action = $parts[0] ?? null;
    $ticketId = $parts[1] ?? null;
    
    // بعض الإجراءات لا تتطلب ticketId (مثل القائمة الرئيسية)
    if (!$ticketId && !in_array($action, ['main_menu', 'tickets_by_status'])) {
        $telegram->answerCallbackQuery($callback['id'], 'خطأ في البيانات', true);
        exit;
    }
    
    $ticket = $db->fetchOne("SELECT * FROM maintenance_tickets WHERE id = :id", ['id' => $ticketId]);
    
    if (!$ticket) {
        $telegram->answerCallbackQuery($callback['id'], 'التذكرة غير موجودة', true);
        exit;
    }
    
    switch ($action) {
        case 'owner_view':
        case 'view_ticket':
            $statusLabels = [
                'new' => '🆕 جديدة',
                'received' => '📥 مستلمة',
                'in_progress' => '🔧 قيد العمل',
                'fixed' => '✅ تم الإصلاح',
                'closed' => '🔒 مغلقة',
            ];
            
            $message = "📋 *تفاصيل التذكرة*\n\n";
            $message .= "🔢 *الرقم:* `{$ticket['ticket_number']}`\n";
            $message .= "👤 *العميل:* {$ticket['customer_name']}\n";
            $message .= "📞 *الهاتف:* {$ticket['customer_phone']}\n";
            $message .= "📍 *العنوان:* {$ticket['customer_address']}\n";
            $message .= "🖨️ *الجهاز:* " . ($ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة') . "\n";
            $message .= "📝 *الموديل:* {$ticket['machine_model']}\n";
            $message .= "⚠️ *العطل:* {$ticket['fault_description']}\n";
            
            if ($ticket['error_code']) {
                $message .= "🔢 *كود الخطأ:* `{$ticket['error_code']}`\n";
            }
            
            $message .= "📊 *الحالة:* {$statusLabels[$ticket['status']]}\n";
            $message .= "📅 *التاريخ:* {$ticket['created_at']}";
            
            $buttons = [];
            
            if ($ticket['status'] !== 'closed') {
                $buttons[] = [
                    ['text' => '📥 استلام', 'callback_data' => 'receive_ticket:' . $ticketId],
                    ['text' => '🔧 قيد العمل', 'callback_data' => 'progress_ticket:' . $ticketId],
                ];
                $buttons[] = [
                    ['text' => '✅ تم الإصلاح', 'callback_data' => 'fix_ticket:' . $ticketId],
                    ['text' => '🔒 إغلاق', 'callback_data' => 'close_ticket:' . $ticketId],
                ];
            } else {
                $buttons[] = [
                    ['text' => '🔓 إعادة فتح', 'callback_data' => 'reopen_ticket:' . $ticketId],
                ];
            }
            
            if ($ticket['repair_report']) {
                $buttons[] = [
                    ['text' => '📄 تحميل التقرير', 'callback_data' => 'download_report:' . $ticketId],
                ];
            }
            
            // تنسيق رقم الواتساب
            $phone = preg_replace('/[^0-9]/', '', $ticket['customer_phone']);
            $defaultCountryCode = Settings::get('company_country_code', '20');
            
            // إذا بدأ بصفر، نستبدله بكود الدولة من الإعدادات
            if (substr($phone, 0, 1) === '0') {
                $phone = $defaultCountryCode . substr($phone, 1);
            } elseif (strlen($phone) <= 10 && substr($phone, 0, strlen($defaultCountryCode)) !== $defaultCountryCode) {
                // إذا كان الرقم قصيراً ولا يبدأ بكود الدولة، نضيفه احتياطاً
                $phone = $defaultCountryCode . $phone;
            }
            
            $buttons[] = [
                ['text' => '💬 واتساب', 'url' => 'https://wa.me/' . $phone],
                ['text' => '🔙 رجوع', 'callback_data' => 'main_menu:0']
            ];
            
            try {
                $telegram->editMessageSimple($chatId, $messageId, $message, $buttons);
            } catch (\Exception $e) { /* ignore */ }
            $telegram->answerCallbackQuery($callback['id']);
            break;
            
        case 'owner_receive':
        case 'receive_ticket':
            updateTicketStatus($db, $telegram, $ticketId, 'received', $chatId, $messageId, $callback['id']);
            break;
            
        case 'owner_assign':
            // عرض قائمة الفنيين للاختيار
            $message = "👨‍🔧 *تعيين فني للتذكرة*\n\nاختر الفني:";
            $technicians = $db->fetchAll("SELECT id, full_name FROM users WHERE role_id IN (SELECT id FROM roles WHERE name IN ('technician', 'admin')) AND is_active = 1");
            $buttons = [];
            foreach ($technicians as $tech) {
                $buttons[] = [['text' => $tech['full_name'], 'callback_data' => 'assign_to:' . $ticketId . ':' . $tech['id']]];
            }
            $buttons[] = [['text' => '🔙 رجوع', 'callback_data' => 'owner_view:' . $ticketId]];
            
            try {
                $telegram->editMessage('owner', $chatId, $messageId, $message, $buttons);
            } catch (\Exception $e) { /* ignore */ }
            $telegram->answerCallback('owner', $callback['id']);
            break;
            
        case 'assign_to':
            // تعيين الفني
            $parts = explode(':', $data);
            $techId = $parts[2] ?? null;
            if ($techId) {
                $db->update('maintenance_tickets', ['assigned_to' => $techId, 'status' => 'assigned'], 'id = :id', ['id' => $ticketId]);
                $technician = $db->fetchOne("SELECT full_name FROM users WHERE id = :id", ['id' => $techId]);
                $telegram->answerCallback('owner', $callback['id'], '✅ تم تعيين ' . $technician['full_name']);
                
                // تحديث الرسالة
                $ticket = $db->fetchOne("SELECT ticket_number FROM maintenance_tickets WHERE id = :id", ['id' => $ticketId]);
                $message = "✅ تم تعيين الفني *{$technician['full_name']}* للتذكرة #{$ticket['ticket_number']}";
                $telegram->editMessage('owner', $chatId, $messageId, $message, [[['text' => '👁️ عرض التفاصيل', 'callback_data' => 'owner_view:' . $ticketId]]]);
            }
            break;
            
        case 'progress_ticket':
            updateTicketStatus($db, $telegram, $ticketId, 'in_progress', $chatId, $messageId, $callback['id']);
            break;
            
        case 'fix_ticket':
            updateTicketStatus($db, $telegram, $ticketId, 'fixed', $chatId, $messageId, $callback['id']);
            break;
            
        case 'close_ticket':
            updateTicketStatus($db, $telegram, $ticketId, 'closed', $chatId, $messageId, $callback['id']);
            break;
            
        case 'reopen_ticket':
            updateTicketStatus($db, $telegram, $ticketId, 'received', $chatId, $messageId, $callback['id']);
            break;
            
        case 'download_report':
            if ($ticket['repair_report']) {
                $reportPath = UPLOADS_PATH . '/' . $ticket['repair_report'];
                if (file_exists($reportPath)) {
                    $telegram->sendDocument($chatId, $reportPath, "📄 تقرير التذكرة #{$ticket['ticket_number']}");
                }
            }
            $telegram->answerCallbackQuery($callback['id']);
            break;
            
        case 'tickets_by_status':
            showTicketsByStatus($db, $telegram, $chatId, $messageId, $ticketId);
            $telegram->answerCallbackQuery($callback['id']);
            break;
            
        case 'main_menu':
            showMainMenu($telegram, $chatId, $messageId);
            $telegram->answerCallbackQuery($callback['id']);
            break;
    }
    
    exit;
}

// دوال مساعدة
// دوال مساعدة
function updateTicketStatus($db, $telegram, $ticketId, $newStatus, $chatId, $messageId, $callbackId) {
    $statusLabels = [
        'received' => 'مستلمة',
        'in_progress' => 'قيد العمل',
        'fixed' => 'تم الإصلاح',
        'closed' => 'مغلقة',
    ];
    
    $updateData = ['status' => $newStatus];
    if ($newStatus === 'closed') {
        $updateData['closed_at'] = date('Y-m-d H:i:s');
    }
    
    $db->update('maintenance_tickets', $updateData, 'id = :id', ['id' => $ticketId]);
    
    $db->insert('ticket_timeline', [
        'ticket_id' => $ticketId,
        'action' => 'status_changed',
        'old_value' => '',
        'new_value' => $newStatus,
        'notes' => 'تم التحديث عبر Telegram',
    ]);
    
    $ticket = $db->fetchOne("SELECT ticket_number FROM maintenance_tickets WHERE id = :id", ['id' => $ticketId]);
    
    $message = "✅ تم تحديث حالة التذكرة #{$ticket['ticket_number']} إلى: *{$statusLabels[$newStatus]}*";
    
    $buttons = [[['text' => '👁️ عرض التفاصيل', 'callback_data' => 'owner_view:' . $ticketId]]];
    
    try {
        $telegram->editMessageSimple($chatId, $messageId, $message, $buttons);
    } catch (\Exception $e) { /* ignore */ }
    
    $telegram->answerCallbackQuery($callbackId, 'تم التحديث ✅');
}

function showTicketsByStatus($db, $telegram, $chatId, $messageId, $status) {
    $statusLabels = [
        'new' => 'الجديدة',
        'received' => 'المستلمة',
        'in_progress' => 'قيد العمل',
        'fixed' => 'المنتهية',
        'closed' => 'المغلقة',
    ];
    
    $tickets = $db->fetchAll(
        "SELECT id, ticket_number, customer_name FROM maintenance_tickets 
         WHERE status = :status ORDER BY created_at DESC LIMIT 10",
        ['status' => $status]
    );
    
    $message = "📋 *التذاكر {$statusLabels[$status]}*\n\n";
    
    $buttons = [];
    
    if (empty($tickets)) {
        $message .= "لا توجد تذاكر";
    } else {
        foreach ($tickets as $ticket) {
            $buttons[] = [[
                'text' => "#{$ticket['ticket_number']} - {$ticket['customer_name']}",
                'callback_data' => 'view_ticket:' . $ticket['id']
            ]];
        }
    }
    
    $buttons[] = [['text' => '🔙 رجوع', 'callback_data' => 'main_menu:0']];
    
    try {
        $telegram->editMessageSimple($chatId, $messageId, $message, $buttons);
    } catch (\Exception $e) { /* ignore */ }
}

function showMainMenu($telegram, $chatId, $messageId = null) {
    $message = "🎛️ *القائمة الرئيسية*\n\nاختر من القائمة:";
    
    $buttons = [
        [
            ['text' => '🆕 التذاكر الجديدة', 'callback_data' => 'tickets_by_status:new'],
            ['text' => '📥 المستلمة', 'callback_data' => 'tickets_by_status:received'],
        ],
        [
            ['text' => '🔧 قيد العمل', 'callback_data' => 'tickets_by_status:in_progress'],
            ['text' => '✅ المنتهية', 'callback_data' => 'tickets_by_status:fixed'],
        ],
        [
            ['text' => '🔒 المغلقة', 'callback_data' => 'tickets_by_status:closed'],
        ],
    ];
    
    try {
        if ($messageId) {
            $telegram->editMessageSimple($chatId, $messageId, $message, $buttons);
        } else {
            $telegram->sendOwnerMessageWithButtons($chatId, $message, $buttons);
        }
    } catch (\Exception $e) { /* ignore */ }
}

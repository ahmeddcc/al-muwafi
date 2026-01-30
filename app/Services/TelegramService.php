<?php
/**
 * خدمة Telegram - نسخة محدثة
 * نظام المُوَفِّي لمهمات المكاتب
 * يدعم بوتين: بوت المالك وبوت الدعم الفني
 */

namespace App\Services;

class TelegramService {
    // بوت المالك
    private string $ownerBotToken = '';
    private string $ownerChatId = '';
    
    // بوت الدعم الفني
    private string $supportBotToken = '';
    private string $supportChatId = '';
    
    private string $errorChatId = '';
    private string $apiUrl = 'https://api.telegram.org/bot';
    private bool $ownerEnabled = false;
    private bool $supportEnabled = false;
    
    public function __construct() {
        $this->loadSettings();
    }
    
    /**
     * تحميل الإعدادات
     */
    private function loadSettings(): void {
        try {
            $db = Database::getInstance();
            $settings = $db->fetchAll(
                "SELECT setting_key, setting_value FROM settings WHERE setting_group = 'telegram'"
            );
            
            foreach ($settings as $setting) {
                match($setting['setting_key']) {
                    // بوت المالك
                    'telegram_owner_bot_token' => $this->ownerBotToken = $setting['setting_value'] ?? '',
                    'telegram_owner_chat_id' => $this->ownerChatId = $setting['setting_value'] ?? '',
                    'telegram_owner_enabled' => $this->ownerEnabled = $setting['setting_value'] === '1',
                    
                    // بوت الدعم الفني
                    'telegram_support_bot_token' => $this->supportBotToken = $setting['setting_value'] ?? '',
                    'telegram_support_chat_id' => $this->supportChatId = $setting['setting_value'] ?? '',
                    'telegram_support_enabled' => $this->supportEnabled = $setting['setting_value'] === '1',
                    
                    // للتوافق مع النسخة القديمة
                    'telegram_bot_token' => $this->ownerBotToken = $this->ownerBotToken ?: ($setting['setting_value'] ?? ''),
                    'telegram_error_chat_id' => $this->errorChatId = $setting['setting_value'] ?? '',
                    'telegram_notifications_enabled' => $this->ownerEnabled = $this->ownerEnabled ?: ($setting['setting_value'] === '1'),
                    
                    default => null
                };
            }
        } catch (\Exception $e) {
            // استخدام القيم من config.php
            $this->ownerBotToken = defined('TELEGRAM_BOT_TOKEN') ? TELEGRAM_BOT_TOKEN : '';
            $this->ownerChatId = defined('TELEGRAM_OWNER_CHAT_ID') ? TELEGRAM_OWNER_CHAT_ID : '';
            $this->errorChatId = defined('TELEGRAM_ERROR_CHAT_ID') ? TELEGRAM_ERROR_CHAT_ID : '';
        }
    }
    
    /**
     * التحقق من تكوين بوت المالك
     */
    public function isOwnerBotConfigured(): bool {
        return $this->ownerEnabled && !empty($this->ownerBotToken) && !empty($this->ownerChatId);
    }
    
    /**
     * التحقق من تكوين بوت الدعم
     */
    public function isSupportBotConfigured(): bool {
        return $this->supportEnabled && !empty($this->supportBotToken) && !empty($this->supportChatId);
    }
    
    /**
     * للتوافق مع الكود القديم
     */
    public function isConfigured(): bool {
        return $this->isOwnerBotConfigured();
    }
    
    // ========================================
    // دوال بوت المالك
    // ========================================
    
    /**
     * إرسال رسالة للمالك
     */
    public function sendToOwner(string $message, array $buttons = []): array {
        if (!$this->isOwnerBotConfigured()) {
            return ['ok' => false, 'error' => 'بوت المالك غير مُكوّن'];
        }
        
        if (!empty($buttons)) {
            return $this->sendMessageWithButtons($this->ownerBotToken, $this->ownerChatId, $message, $buttons);
        }
        
        return $this->sendMessage($this->ownerBotToken, $this->ownerChatId, $message);
    }
    
    /**
     * إشعار تذكرة جديدة للمالك
     */
    public function notifyOwnerNewTicket(array $ticket): array {
        $message = "🎫 *تذكرة صيانة جديدة*\n\n";
        $message .= "📋 *رقم التذكرة:* `{$ticket['ticket_number']}`\n";
        $message .= "👤 *العميل:* {$ticket['customer_name']}\n";
        $message .= "📞 *الهاتف:* `{$ticket['customer_phone']}`\n";
        $message .= "🖨️ *الجهاز:* " . ($ticket['machine_type'] === 'copier' ? 'آلة تصوير' : 'طابعة') . "\n";
        $message .= "📝 *الموديل:* {$ticket['machine_model']}\n";
        $message .= "⚠️ *العطل:* {$ticket['fault_description']}\n";
        
        if (!empty($ticket['error_code'])) {
            $message .= "🔢 *كود الخطأ:* `{$ticket['error_code']}`\n";
        }
        
        $message .= "📅 *التاريخ:* " . date('Y-m-d H:i');
        
        // تنسيق رقم الواتساب
        // تنسيق رقم الواتساب
        $phone = preg_replace('/[^0-9]/', '', $ticket['customer_phone']);
        $defaultCountryCode = Settings::get('company_country_code', '20');
        
        // إذا بدأ بصفر، نستبدله بكود الدولة من الإعدادات
        if (substr($phone, 0, 1) === '0') {
            $phone = $defaultCountryCode . substr($phone, 1);
        } elseif (strlen($phone) <= 10 && substr($phone, 0, strlen($defaultCountryCode)) !== $defaultCountryCode) {
            // إذا كان الرقم قصيراً ولا يبدأ بكود الدولة، نضيفه احتياطاً (لأرقام مثل 10xxxxxxx في مصر)
            $phone = $defaultCountryCode . $phone;
        }
        
        $buttons = [
            [
                ['text' => '👁️ عرض', 'callback_data' => 'owner_view:' . $ticket['id']],
                ['text' => '✅ استلام', 'callback_data' => 'owner_receive:' . $ticket['id']],
            ],
            [
                ['text' => '👨‍🔧 تعيين فني', 'callback_data' => 'owner_assign:' . $ticket['id']],
                ['text' => '💬 واتساب', 'url' => 'https://wa.me/' . $phone],
            ]
        ];
        
        return $this->sendToOwner($message, $buttons);
    }
    
    /**
     * إشعار تحديث تذكرة للمالك
     */
    public function notifyOwnerTicketUpdate(array $ticket, string $action, ?string $notes = null): array {
        $statusLabels = [
            'new' => '🆕 جديدة',
            'received' => '📥 مستلمة',
            'assigned' => '👨‍🔧 معينة لفني',
            'in_progress' => '🔄 قيد العمل',
            'fixed' => '✅ تم الإصلاح',
            'closed' => '🏁 مغلقة',
        ];
        
        $message = "🔄 *تحديث تذكرة*\n\n";
        $message .= "📋 *رقم التذكرة:* `{$ticket['ticket_number']}`\n";
        $message .= "👤 *العميل:* {$ticket['customer_name']}\n";
        $message .= "📊 *الحالة:* " . ($statusLabels[$ticket['status']] ?? $ticket['status']) . "\n";
        
        if ($notes) {
            $message .= "📝 *ملاحظات:* {$notes}\n";
        }
        
        $message .= "🕐 *التحديث:* " . date('Y-m-d H:i');
        
        return $this->sendToOwner($message);
    }
    
    /**
     * إرسال تقرير يومي للمالك
     */
    public function sendOwnerDailyReport(array $stats): array {
        $message = "📊 *التقرير اليومي*\n";
        $message .= "📅 " . date('Y-m-d') . "\n\n";
        $message .= "🎫 تذاكر اليوم: *{$stats['today_tickets']}*\n";
        $message .= "✅ محلولة: *{$stats['solved']}*\n";
        $message .= "⏳ معلقة: *{$stats['pending']}*\n";
        $message .= "🔄 قيد العمل: *{$stats['in_progress']}*\n";
        
        return $this->sendToOwner($message);
    }
    
    /**
     * إرسال تنبيه خطأ للمالك
     */
    public function sendOwnerError(string $error): array {
        $chatId = !empty($this->errorChatId) ? $this->errorChatId : $this->ownerChatId;
        
        $appName = defined('APP_NAME') ? APP_NAME : 'النظام';
        $message = "⚠️ *تنبيه خطأ في {$appName}*\n\n";
        $message .= "```\n{$error}\n```\n";
        $message .= "🕐 " . date('Y-m-d H:i:s');
        
        return $this->sendMessage($this->ownerBotToken, $chatId, $message);
    }
    
    // ========================================
    // دوال بوت الدعم الفني
    // ========================================
    
    /**
     * إرسال رسالة للدعم الفني
     */
    public function sendToSupport(string $message, array $buttons = []): array {
        if (!$this->isSupportBotConfigured()) {
            return ['ok' => false, 'error' => 'بوت الدعم غير مُكوّن'];
        }
        
        if (!empty($buttons)) {
            return $this->sendMessageWithButtons($this->supportBotToken, $this->supportChatId, $message, $buttons);
        }
        
        return $this->sendMessage($this->supportBotToken, $this->supportChatId, $message);
    }
    
    /**
     * إشعار تذكرة معينة للفني
     */
    public function notifySupportAssignedTicket(array $ticket, string $technicianName = ''): array {
        $message = "🔧 *تذكرة معينة لك*\n\n";
        $message .= "📋 *رقم التذكرة:* `{$ticket['ticket_number']}`\n";
        $message .= "👤 *العميل:* {$ticket['customer_name']}\n";
        $message .= "📞 *الهاتف:* `{$ticket['customer_phone']}`\n";
        $message .= "📍 *العنوان:* {$ticket['customer_address']}\n";
        $message .= "🖨️ *الجهاز:* {$ticket['machine_model']}\n";
        $message .= "⚠️ *العطل:* {$ticket['fault_description']}\n";
        
        if (!empty($ticket['error_code'])) {
            $message .= "🔢 *كود الخطأ:* `{$ticket['error_code']}`\n";
        }
        
        $buttons = [
            [
                ['text' => '📞 اتصال بالعميل', 'url' => 'tel:' . $ticket['customer_phone']],
                ['text' => '💬 واتساب', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $ticket['customer_phone'])],
            ],
            [
                ['text' => '🔄 بدء العمل', 'callback_data' => 'support_start:' . $ticket['id']],
                ['text' => '✅ تم الإصلاح', 'callback_data' => 'support_fixed:' . $ticket['id']],
            ],
            [
                ['text' => '📝 إضافة ملاحظة', 'callback_data' => 'support_note:' . $ticket['id']],
            ]
        ];
        
        return $this->sendToSupport($message, $buttons);
    }
    
    /**
     * إشعار تذكرة عاجلة للدعم
     */
    public function notifySupportUrgentTicket(array $ticket): array {
        $message = "🚨 *تذكرة عاجلة!*\n\n";
        $message .= "📋 *رقم التذكرة:* `{$ticket['ticket_number']}`\n";
        $message .= "👤 *العميل:* {$ticket['customer_name']}\n";
        $message .= "📞 *الهاتف:* `{$ticket['customer_phone']}`\n";
        $message .= "⚠️ *العطل:* {$ticket['fault_description']}\n";
        $message .= "⏰ *مطلوب الرد فوراً!*";
        
        return $this->sendToSupport($message);
    }
    
    // ========================================
    // دوال مشتركة
    // ========================================
    
    /**
     * إرسال رسالة عادية
     */
    private function sendMessage(string $botToken, string $chatId, string $message, array $options = []): array {
        if (empty($chatId) || empty($botToken)) {
            return ['ok' => false, 'error' => 'التكوين غير مكتمل'];
        }
        
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ], $options);
        
        return $this->request($botToken, 'sendMessage', $params);
    }
    
    /**
     * إرسال رسالة مع أزرار
     */
    private function sendMessageWithButtons(string $botToken, string $chatId, string $message, array $buttons): array {
        $keyboard = ['inline_keyboard' => $buttons];
        
        return $this->sendMessage($botToken, $chatId, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }
    
    /**
     * تحديث رسالة
     */
    public function editMessage(string $botType, string $chatId, int $messageId, string $newText, array $buttons = []): array {
        $botToken = $botType === 'owner' ? $this->ownerBotToken : $this->supportBotToken;
        
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $newText,
            'parse_mode' => 'Markdown',
        ];
        
        if (!empty($buttons)) {
            $params['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
        }
        
        return $this->request($botToken, 'editMessageText', $params);
    }
    
    /**
     * الرد على callback query
     */
    public function answerCallback(string $botType, string $callbackQueryId, string $text = '', bool $showAlert = false): array {
        $botToken = $botType === 'owner' ? $this->ownerBotToken : $this->supportBotToken;
        
        return $this->request($botToken, 'answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }
    
    /**
     * إرسال طلب للـ API
     */
    private function request(string $botToken, string $method, array $params, bool $multipart = false): array {
        $url = $this->apiUrl . $botToken . '/' . $method;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($multipart) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            ErrorLogger::logError('telegram_request', $error, __FILE__, __LINE__);
            return ['ok' => false, 'error' => $error];
        }
        
        $result = json_decode($response, true);
        
        if (!$result || !isset($result['ok'])) {
            return ['ok' => false, 'error' => 'استجابة غير صالحة'];
        }
        
        return $result;
    }
    
    /**
     * الحصول على معلومات بوت المالك
     */
    public function getOwnerBotInfo(): array {
        if (empty($this->ownerBotToken)) {
            return ['ok' => false, 'error' => 'التوكن غير محدد'];
        }
        return $this->request($this->ownerBotToken, 'getMe', []);
    }
    
    /**
     * الحصول على معلومات بوت الدعم
     */
    public function getSupportBotInfo(): array {
        if (empty($this->supportBotToken)) {
            return ['ok' => false, 'error' => 'التوكن غير محدد'];
        }
        return $this->request($this->supportBotToken, 'getMe', []);
    }
    
    /**
     * تعيين Webhook لبوت المالك
     */
    public function setOwnerWebhook(string $url): array {
        return $this->request($this->ownerBotToken, 'setWebhook', ['url' => $url]);
    }
    
    /**
     * تعيين Webhook لبوت الدعم
     */
    public function setSupportWebhook(string $url): array {
        return $this->request($this->supportBotToken, 'setWebhook', ['url' => $url]);
    }
    
    // ========================================
    // للتوافق مع الكود القديم
    // ========================================
    
    public function sendOwnerNotification(string $message, array $buttons = []): array {
        return $this->sendToOwner($message, $buttons);
    }
    
    public function sendErrorNotification(string $message): array {
        return $this->sendOwnerError($message);
    }
    
    public function notifyNewTicket(array $ticket): array {
        return $this->notifyOwnerNewTicket($ticket);
    }
    
    public function notifyTicketUpdate(array $ticket, string $action, ?string $notes = null): array {
        return $this->notifyOwnerTicketUpdate($ticket, $action, $notes);
    }
    
    /**
     * حذف Webhook
     */
    public function deleteWebhook(): array {
        return $this->request($this->ownerBotToken, 'deleteWebhook');
    }
    
    /**
     * الرد على Callback Query
     */
    public function answerCallbackQuery(string $callbackId, string $text = '', bool $showAlert = false): array {
        $params = ['callback_query_id' => $callbackId];
        if ($text) {
            $params['text'] = $text;
            $params['show_alert'] = $showAlert;
        }
        return $this->request($this->ownerBotToken, 'answerCallbackQuery', $params);
    }
    
    /**
     * تعديل رسالة (نسخة مبسطة للـ webhook - تستخدم بوت المالك)
     * @deprecated استخدم editMessageForBot بدلاً منها
     */
    public function editMessageSimple(string $chatId, int $messageId, string $text, array $buttons = []): array {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];
        
        if (!empty($buttons)) {
            $params['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
        }
        
        return $this->request($this->ownerBotToken, 'editMessageText', $params);
    }
    
    /**
     * إرسال رسالة مع أزرار لبوت المالك
     */
    public function sendOwnerMessageWithButtons(string $chatId, string $text, array $buttons): array {
        return $this->request($this->ownerBotToken, 'sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons]),
        ]);
    }
    
    /**
     * الحصول على توكن بوت المالك
     */
    public function getOwnerBotToken(): string {
        return $this->ownerBotToken;
    }
}

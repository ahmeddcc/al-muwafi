<?php
/**
 * خدمة تسجيل الأخطاء
 * نظام المُوَفِّي لمهمات المكاتب
 */

namespace App\Services;

class ErrorLogger {
    private static string $logPath = '';
    
    /**
     * تهيئة مسار السجل
     */
    public static function init(): void {
        self::$logPath = defined('LOGS_PATH') ? LOGS_PATH : dirname(__DIR__, 2) . '/storage/logs';
        
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
    }
    
    /**
     * تسجيل خطأ
     */
    public static function logError(
        string $type,
        string $message,
        ?string $file = null,
        ?int $line = null,
        string $priority = 'medium'
    ): void {
        self::init();
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'priority' => $priority,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
        ];
        
        // الكتابة في ملف السجل
        $logFile = self::$logPath . '/errors_' . date('Y-m-d') . '.log';
        $logLine = '[' . $logEntry['timestamp'] . '] [' . strtoupper($priority) . '] ' . 
                   $type . ': ' . $message;
        
        if ($file) {
            $logLine .= ' في ' . $file;
            if ($line) {
                $logLine .= ':' . $line;
            }
        }
        
        file_put_contents($logFile, $logLine . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        // حفظ في قاعدة البيانات إذا كان الاتصال متاحاً
        self::saveToDatabase($logEntry);
        
        // إرسال إلى Telegram للأخطاء الحرجة
        if (in_array($priority, ['high', 'critical'])) {
            self::sendToTelegram($logEntry);
        }
    }
    
    /**
     * حفظ الخطأ في قاعدة البيانات
     */
    private static function saveToDatabase(array $logEntry): void {
        try {
            $db = Database::getInstance();
            
            $db->insert('error_logs', [
                'error_type' => strip_tags($logEntry['type']),
                'error_message' => strip_tags($logEntry['message']),
                'file_name' => strip_tags($logEntry['file'] ?? ''),
                'line_number' => (int)($logEntry['line'] ?? 0),
                'priority' => strip_tags($logEntry['priority']),
            ]);
        } catch (\Exception $e) {
            // تجاهل أخطاء قاعدة البيانات لتجنب الحلقة اللانهائية
        }
    }
    
    /**
     * إرسال إلى Telegram
     */
    private static function sendToTelegram(array $logEntry): void {
        try {
            $telegram = new TelegramService();
            
            if (!$telegram->isConfigured()) {
                return;
            }
            
            $priorityEmoji = match($logEntry['priority']) {
                'critical' => '🔴',
                'high' => '🟠',
                'medium' => '🟡',
                default => '🟢'
            };
            
            $message = "⚠️ *تنبيه خطأ في النظام*\n\n";
            $message .= "{$priorityEmoji} *الأولوية:* " . self::translatePriority($logEntry['priority']) . "\n";
            $message .= "📁 *النوع:* `{$logEntry['type']}`\n";
            $message .= "💬 *الرسالة:* {$logEntry['message']}\n";
            
            if ($logEntry['file']) {
                $message .= "📄 *الملف:* `" . basename($logEntry['file']) . "`\n";
                if ($logEntry['line']) {
                    $message .= "📍 *السطر:* {$logEntry['line']}\n";
                }
            }
            
            $message .= "🕐 *الوقت:* {$logEntry['timestamp']}";
            
            $telegram->sendErrorNotification($message);
            
            // تحديث حالة الإرسال
            self::markAsSent($logEntry);
            
        } catch (\Exception $e) {
            // تجاهل أخطاء Telegram
        }
    }
    
    /**
     * ترجمة الأولوية
     */
    private static function translatePriority(string $priority): string {
        return match($priority) {
            'critical' => 'حرجة',
            'high' => 'عالية',
            'medium' => 'متوسطة',
            'low' => 'منخفضة',
            default => $priority
        };
    }
    
    /**
     * تعليم الخطأ كمُرسَل
     */
    private static function markAsSent(array $logEntry): void {
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE error_logs SET telegram_sent = 1 
                 WHERE error_type = :type AND error_message = :message 
                 ORDER BY id DESC LIMIT 1",
                ['type' => $logEntry['type'], 'message' => $logEntry['message']]
            );
        } catch (\Exception $e) {
            // تجاهل
        }
    }
    
    /**
     * مسح السجلات القديمة
     */
    public static function cleanOldLogs(int $daysToKeep = 30): int {
        self::init();
        
        $deleted = 0;
        $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        
        // حذف من قاعدة البيانات
        try {
            $db = Database::getInstance();
            $deleted = $db->delete('error_logs', 'DATE(created_at) < :date', ['date' => $cutoffDate]);
        } catch (\Exception $e) {
            // تجاهل
        }
        
        // حذف ملفات السجل القديمة
        $files = glob(self::$logPath . '/errors_*.log');
        foreach ($files as $file) {
            $fileDate = str_replace(['errors_', '.log'], '', basename($file));
            if ($fileDate < $cutoffDate) {
                unlink($file);
            }
        }
        
        return $deleted;
    }
}

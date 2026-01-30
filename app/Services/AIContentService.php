<?php
/**
 * خدمة الذكاء الاصطناعي للمحتوى
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Services;

class AIContentService {
    private string $apiKey = '';
    private string $apiUrl = '';
    private bool $enabled = false;
    
    /**
     * البناء
     */
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
                "SELECT setting_key, setting_value FROM settings WHERE setting_group = 'ai'"
            );
            
            foreach ($settings as $setting) {
                match($setting['setting_key']) {
                    'ai_api_key' => $this->apiKey = $setting['setting_value'] ?? '',
                    'ai_api_url' => $this->apiUrl = $setting['setting_value'] ?? '',
                    'ai_enabled' => $this->enabled = $setting['setting_value'] === '1',
                    default => null
                };
            }
        } catch (\Exception $e) {
            $this->apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
            $this->apiUrl = defined('AI_API_URL') ? AI_API_URL : '';
        }
    }
    
    /**
     * التحقق من التفعيل
     */
    public function isEnabled(): bool {
        return $this->enabled && !empty($this->apiKey) && !empty($this->apiUrl);
    }
    
    /**
     * توليد وصف منتج احترافي
     */
    public function generateProductDescription(string $rawSpecs, string $productName): array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'خدمة الذكاء الاصطناعي غير مفعلة'];
        }
        
        $prompt = $this->buildProductPrompt($rawSpecs, $productName);
        return $this->callAPI($prompt);
    }
    
    /**
     * تحسين نص عربي
     */
    public function improveArabicText(string $text): array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'خدمة الذكاء الاصطناعي غير مفعلة'];
        }
        
        $prompt = "قم بتحسين النص العربي التالي ليكون أكثر احترافية ووضوحاً مع الحفاظ على المعنى:\n\n{$text}";
        return $this->callAPI($prompt);
    }
    
    /**
     * توليد وصف عطل
     */
    public function generateFaultDescription(string $faultCode, string $faultName): array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'خدمة الذكاء الاصطناعي غير مفعلة'];
        }
        
        $prompt = "اكتب وصفاً تقنياً احترافياً باللغة العربية لعطل آلة تصوير/طابعة ريكو:\n";
        $prompt .= "كود العطل: {$faultCode}\n";
        $prompt .= "اسم العطل: {$faultName}\n\n";
        $prompt .= "الوصف يجب أن يتضمن:\n";
        $prompt .= "1. شرح العطل\n";
        $prompt .= "2. الأسباب المحتملة\n";
        $prompt .= "3. الحل المقترح";
        
        return $this->callAPI($prompt);
    }
    
    /**
     * بناء prompt لوصف المنتج
     */
    private function buildProductPrompt(string $rawSpecs, string $productName): string {
        $prompt = "أنت خبير في كتابة أوصاف المنتجات التقنية باللغة العربية.\n\n";
        $prompt .= "قم بكتابة وصف احترافي ومفصل باللغة العربية للمنتج التالي:\n\n";
        $prompt .= "اسم المنتج: {$productName}\n\n";
        $prompt .= "المواصفات الخام:\n{$rawSpecs}\n\n";
        $prompt .= "المطلوب:\n";
        $prompt .= "1. وصف تسويقي جذاب (فقرة واحدة)\n";
        $prompt .= "2. المميزات الرئيسية (قائمة)\n";
        $prompt .= "3. المواصفات التقنية (منظمة في جدول)\n";
        $prompt .= "4. الاستخدامات المناسبة\n\n";
        $prompt .= "ملاحظة: اكتب بأسلوب احترافي يناسب موقع شركة متخصصة.";
        
        return $prompt;
    }
    
    /**
     * استدعاء API
     */
    private function callAPI(string $prompt): array {
        try {
            $ch = curl_init($this->apiUrl);
            
            $payload = json_encode([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد متخصص في كتابة المحتوى التقني باللغة العربية.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);
            
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey,
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 60,
            ]);
            
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($error) {
                throw new \Exception('خطأ في الاتصال: ' . $error);
            }
            
            if ($httpCode !== 200) {
                throw new \Exception('استجابة غير صالحة: HTTP ' . $httpCode);
            }
            
            $result = json_decode($response, true);
            
            if (!isset($result['choices'][0]['message']['content'])) {
                throw new \Exception('تنسيق استجابة غير متوقع');
            }
            
            return [
                'success' => true,
                'content' => $result['choices'][0]['message']['content'],
            ];
            
        } catch (\Exception $e) {
            ErrorLogger::logError('ai_content', $e->getMessage(), __FILE__, __LINE__);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * استخدام وصف افتراضي عند فشل AI
     */
    public function getFallbackDescription(string $productName, string $rawSpecs): string {
        $description = "<h3>{$productName}</h3>\n\n";
        $description .= "<p>منتج عالي الجودة من ريكو يتميز بالأداء الممتاز والموثوقية العالية.</p>\n\n";
        $description .= "<h4>المواصفات:</h4>\n";
        $description .= "<pre>{$rawSpecs}</pre>";
        
        return $description;
    }
}

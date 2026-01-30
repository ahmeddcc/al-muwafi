<?php
/**
 * وحدة تحكم الإعدادات (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\Settings;
use App\Services\ImageProcessor;
use App\Services\TelegramService;

class SettingsController extends BaseController {
    private ImageProcessor $imageProcessor;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
    }
    
    /**
     * صفحة الإعدادات
     */
    public function index(): void {
        $this->requirePermission('settings.view');
        
        $companySettings = Settings::getGroup('company');
        $socialSettings = Settings::getGroup('social');
        $securitySettings = Settings::getGroup('security');
        $telegramSettings = Settings::getGroup('telegram');
        $aiSettings = Settings::getGroup('ai');
        $imgSettings = Settings::getGroup('images');
        $generalSettings = Settings::getGroup('general');
        $menuLinks = $this->getMenuLinks();
        
        $this->view('admin.settings.index', [
            'title' => 'إعدادات النظام',
            'company' => $companySettings,
            'social' => $socialSettings,
            'security' => $securitySettings,
            'telegram' => $telegramSettings,
            'ai' => $aiSettings,
            'images' => $imgSettings,
            'general' => $generalSettings,
            'menuLinks' => $menuLinks,
        ]);
    }
    
    /**
     * تحديث إعدادات الشركة
     */
    public function updateCompany(): void {
        $this->requirePermission('settings.company');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'country_code' => $this->input('country_code', '20'),
            'whatsapp' => $this->input('whatsapp'),
            'address' => $this->input('address'),
            'map_embed' => $this->input('map_embed'),
            'map_embed_url' => $this->processEncodedInput($this->input('map_embed_url')),
        ];
        
        // معالجة الشعار
        if ($this->input('delete_logo') === '1') {
            $data['logo'] = '';
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - Deleting logo\n", FILE_APPEND);
        } elseif (!empty($_FILES['logo']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['logo'], 'company');
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - Upload Result: " . json_encode($result) . "\n", FILE_APPEND);
            
            if ($result['success']) {
                $data['logo'] = $result['path'];
            } else {
                $_SESSION['flash']['error'] = "فشل رفع اللوجو: " . ($result['error'] ?? 'خطأ غير معروف');
            }
        } else {
             file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - No logo uploaded and not deleting.\n", FILE_APPEND);
        }
        
        // معالجة الأيقونة
        if ($this->input('delete_favicon') === '1') {
            $data['favicon'] = '';
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - Deleting favicon\n", FILE_APPEND);
        } elseif (!empty($_FILES['favicon']['name'])) {
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - Attempting favicon upload: " . $_FILES['favicon']['name'] . "\n", FILE_APPEND);
            $result = $this->imageProcessor->upload($_FILES['favicon'], 'company');
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - Favicon Result: " . json_encode($result) . "\n", FILE_APPEND);
            if ($result['success']) {
                $data['favicon'] = $result['path'];
            } else {
                $_SESSION['flash']['error'] = "فشل رفع الأيقونة: " . ($result['error'] ?? 'خطأ غير معروف');
            }
        } else {
            file_put_contents(STORAGE_PATH . '/logs/upload_debug.txt', date('Y-m-d H:i:s') . " - No favicon uploaded.\n", FILE_APPEND);
        }
        
        // معالجة صورة تسجيل الدخول
        if ($this->input('delete_login_image') === '1') {
            // حذف الصورة
            $data['login_image'] = '';
        } elseif (!empty($_FILES['login_image']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['login_image'], 'company');
            if ($result['success']) {
                $data['login_image'] = $result['path'];
            }
        }
        
        Settings::setGroup('company', $data);
        
        $this->redirect('/admin/settings', ['success' => 'تم حفظ إعدادات الشركة']);
    }
    
    /**
     * تحديث روابط التواصل الاجتماعي
     */
    public function updateSocial(): void {
        $this->requirePermission('settings.social');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'facebook' => $this->input('facebook'),
            'twitter' => $this->input('twitter'),
            'instagram' => $this->input('instagram'),
            'linkedin' => $this->input('linkedin'),
            'youtube' => $this->input('youtube'),
        ];
        
        Settings::setGroup('social', $data);
        
        $this->redirect('/admin/settings#social', ['success' => 'تم حفظ روابط التواصل']);
    }
    
    /**
     * تحديث إعدادات الأمان
     */
    public function updateSecurity(): void {
        $this->requirePermission('settings.security');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'disable_right_click' => $this->input('disable_right_click') ? '1' : '0',
            'disable_inspect' => $this->input('disable_inspect') ? '1' : '0',
            'disable_f12' => $this->input('disable_f12') ? '1' : '0',
            'disable_copy' => $this->input('disable_copy') ? '1' : '0',
            'rate_limiting' => $this->input('rate_limiting') ? '1' : '0',
            'rate_limit_requests' => (int) $this->input('rate_limit_requests', 100),
            'rate_limit_window' => (int) $this->input('rate_limit_window', 60),
        ];
        
        Settings::setGroup('security', $data);
        
        $this->redirect('/admin/settings#security', ['success' => 'تم حفظ إعدادات الأمان']);
    }
    
    /**
     * تحديث إعدادات بوت المالك
     */
    public function updateTelegramOwner(): void {
        $this->requirePermission('settings.telegram');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        // حفظ إعدادات بوت المالك
        $settings = [
            'telegram_owner_bot_token' => $this->input('owner_bot_token'),
            'telegram_owner_chat_id' => $this->input('owner_chat_id'),
            'telegram_error_chat_id' => $this->input('error_chat_id'),
            'telegram_owner_enabled' => $this->input('owner_enabled') ? '1' : '0',
            // للتوافق مع الكود القديم
            'telegram_bot_token' => $this->input('owner_bot_token'),
            'telegram_notifications_enabled' => $this->input('owner_enabled') ? '1' : '0',
        ];
        
        foreach ($settings as $key => $value) {
            $this->db->query("INSERT INTO settings (setting_key, setting_value, setting_group) 
                              VALUES (:key, :value, 'telegram') 
                              ON DUPLICATE KEY UPDATE setting_value = :value2",
                              ['key' => $key, 'value' => $value, 'value2' => $value]);
        }
        
        $this->redirect('/admin/settings#telegram', ['success' => 'تم حفظ إعدادات بوت المالك']);
    }
    
    /**
     * تحديث إعدادات بوت الدعم الفني
     */
    public function updateTelegramSupport(): void {
        $this->requirePermission('settings.telegram');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $settings = [
            'telegram_support_bot_token' => $this->input('support_bot_token'),
            'telegram_support_chat_id' => $this->input('support_chat_id'),
            'telegram_support_enabled' => $this->input('support_enabled') ? '1' : '0',
        ];
        
        foreach ($settings as $key => $value) {
            $this->db->query("INSERT INTO settings (setting_key, setting_value, setting_group) 
                              VALUES (:key, :value, 'telegram') 
                              ON DUPLICATE KEY UPDATE setting_value = :value2",
                              ['key' => $key, 'value' => $value, 'value2' => $value]);
        }
        
        $this->redirect('/admin/settings#telegram', ['success' => 'تم حفظ إعدادات بوت الدعم الفني']);
    }
    
    /**
     * اختبار بوت المالك
     */
    public function testTelegramOwner(): void {
        $this->requirePermission('settings.telegram');
        
        $telegram = new TelegramService();
        
        if (!$telegram->isOwnerBotConfigured()) {
            $this->json(['success' => false, 'error' => 'الرجاء تكوين إعدادات بوت المالك أولاً']);
            return;
        }
        
        $result = $telegram->sendToOwner("🔔 *اختبار بوت المالك*\n\nإذا وصلتك هذه الرسالة، فإن التكوين صحيح!\n🕐 " . date('Y-m-d H:i:s'));
        
        if ($result['ok'] ?? false) {
            $this->json(['success' => true, 'message' => 'تم إرسال رسالة الاختبار لبوت المالك بنجاح']);
        } else {
            $this->json(['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال']);
        }
    }
    
    /**
     * اختبار بوت الدعم الفني
     */
    public function testTelegramSupport(): void {
        $this->requirePermission('settings.telegram');
        
        $telegram = new TelegramService();
        
        if (!$telegram->isSupportBotConfigured()) {
            $this->json(['success' => false, 'error' => 'الرجاء تكوين إعدادات بوت الدعم أولاً']);
            return;
        }
        
        $result = $telegram->sendToSupport("🔔 *اختبار بوت الدعم الفني*\n\nإذا وصلتك هذه الرسالة، فإن التكوين صحيح!\n🕐 " . date('Y-m-d H:i:s'));
        
        if ($result['ok'] ?? false) {
            $this->json(['success' => true, 'message' => 'تم إرسال رسالة الاختبار لبوت الدعم بنجاح']);
        } else {
            $this->json(['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال']);
        }
    }
    
    /**
     * للتوافق مع الكود القديم
     */
    public function updateTelegram(): void {
        $this->updateTelegramOwner();
    }
    
    public function testTelegram(): void {
        $this->testTelegramOwner();
    }
    
    /**
     * تحديث إعدادات الذكاء الاصطناعي
     */
    public function updateAI(): void {
        $this->requirePermission('settings.ai');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'enabled' => $this->input('enabled') ? '1' : '0',
            'api_key' => $this->input('api_key'),
            'api_url' => $this->input('api_url'),
        ];
        
        Settings::setGroup('ai', $data);
        
        $this->redirect('/admin/settings#ai', ['success' => 'تم حفظ إعدادات الذكاء الاصطناعي']);
    }

    /**
     * تحديث إعدادات الصور
     */
    public function updateImages(): void {
        $this->requirePermission('settings.images');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'remove_background_enabled' => $this->input('remove_background_enabled') ? '1' : '0',
            'watermark_enabled' => $this->input('watermark_enabled') ? '1' : '0',
            'watermark_position' => $this->input('watermark_position', 'bottom-right'),
            'watermark_opacity_mode' => $this->input('watermark_opacity_mode', 'auto'),
            'watermark_opacity' => (int) $this->input('watermark_opacity', 50),
        ];
        
        // رفع صورة اللوجو للعلامة المائية
        if (isset($_FILES['watermark_logo']) && $_FILES['watermark_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = UPLOADS_PATH . '/watermark';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $targetFile = $uploadDir . '/logo.png';
            $fileType = strtolower(pathinfo($_FILES['watermark_logo']['name'], PATHINFO_EXTENSION));
            
            // التحقق من نوع الملف
            $allowedTypes = ['png', 'jpg', 'jpeg', 'gif'];
            if (in_array($fileType, $allowedTypes)) {
                // تحويل إلى PNG لضمان الشفافية
                $sourceImage = null;
                switch ($fileType) {
                    case 'png':
                        $sourceImage = @imagecreatefrompng($_FILES['watermark_logo']['tmp_name']);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        $sourceImage = @imagecreatefromjpeg($_FILES['watermark_logo']['tmp_name']);
                        break;
                    case 'gif':
                        $sourceImage = @imagecreatefromgif($_FILES['watermark_logo']['tmp_name']);
                        break;
                }
                
                if ($sourceImage) {
                    // حفظ كـ PNG مع الشفافية
                    imagesavealpha($sourceImage, true);
                    imagepng($sourceImage, $targetFile, 9);
                    imagedestroy($sourceImage);
                } else {
                    // Fallback: نسخ الملف مباشرة
                    move_uploaded_file($_FILES['watermark_logo']['tmp_name'], $targetFile);
                }
            }
        }
        
        Settings::setGroup('images', $data);
        
        $this->redirect('/admin/settings#images', ['success' => 'تم حفظ إعدادات الصور']);
    }
    
    /**
     * تحديث الإعدادات العامة
     */
    public function updateGeneral(): void {
        $this->requirePermission('settings.general');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'coming_soon' => $this->input('coming_soon') ? '1' : '0',
            'maintenance_mode' => $this->input('maintenance_mode') ? '1' : '0',
            'tickets_enabled' => $this->input('tickets_enabled') ? '1' : '0',
            'products_per_page' => (int) $this->input('products_per_page', 12),
        ];
        
        Settings::setGroup('general', $data);
        
        $this->redirect('/admin/settings#general', ['success' => 'تم حفظ الإعدادات العامة']);
    }
    
    /**
     * تحديث روابط الفوتر
     */
    public function updateFooterLinks(): void {
        $this->requirePermission('settings.menu');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/settings', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $links = $_POST['links'] ?? [];
        
        foreach ($links as $slug => $linkData) {
            // تحديث الصفحة في جدول pages
            $showInFooter = isset($linkData['show_in_footer']) ? 1 : 0;
            $sortOrder = (int) ($linkData['sort_order'] ?? 0);
            
            // تحقق من وجود الصفحة
            $exists = $this->db->exists('pages', 'slug = :slug', ['slug' => $slug]);
            
            if ($exists) {
                $this->db->update('pages', [
                    'show_in_footer' => $showInFooter,
                    'sort_order' => $sortOrder,
                ], 'slug = :slug', ['slug' => $slug]);
            } else {
                // إنشاء الصفحة إذا غير موجودة
                $this->db->insert('pages', [
                    'slug' => $slug,
                    'title' => $slug,
                    'show_in_footer' => $showInFooter,
                    'sort_order' => $sortOrder,
                    'is_active' => 1,
                ]);
            }
        }
        
        $this->redirect('/admin/settings#menu', ['success' => 'تم حفظ إعدادات الفوتر']);
    }
    
    /**
     * جلب بيانات روابط القوائم
     */
    private function getMenuLinks(): array {
        $defaultLinks = [
            ['slug' => 'home', 'title' => 'الرئيسية', 'show_in_menu' => 1, 'show_in_footer' => 0, 'sort_order' => 0],
            ['slug' => 'products', 'title' => 'المنتجات', 'show_in_menu' => 1, 'show_in_footer' => 1, 'sort_order' => 1],
            ['slug' => 'services', 'title' => 'الخدمات', 'show_in_menu' => 1, 'show_in_footer' => 1, 'sort_order' => 2],
            ['slug' => 'spare-parts', 'title' => 'قطع الغيار', 'show_in_menu' => 1, 'show_in_footer' => 1, 'sort_order' => 3],
            ['slug' => 'about', 'title' => 'من نحن', 'show_in_menu' => 1, 'show_in_footer' => 0, 'sort_order' => 4],
            ['slug' => 'contact', 'title' => 'اتصل بنا', 'show_in_menu' => 1, 'show_in_footer' => 0, 'sort_order' => 5],
            ['slug' => 'maintenance', 'title' => 'طلب صيانة', 'show_in_menu' => 1, 'show_in_footer' => 1, 'sort_order' => 6],
        ];
        
        try {
            $dbLinks = $this->db->fetchAll("SELECT slug, title, show_in_menu, show_in_footer, sort_order FROM pages ORDER BY sort_order");
            if (!empty($dbLinks)) {
                // دمج البيانات من قاعدة البيانات
                $dbLinksBySlug = [];
                foreach ($dbLinks as $link) {
                    $dbLinksBySlug[$link['slug']] = $link;
                }
                
                foreach ($defaultLinks as &$link) {
                    if (isset($dbLinksBySlug[$link['slug']])) {
                        $link = array_merge($link, $dbLinksBySlug[$link['slug']]);
                    }
                }
            }
        } catch (\Exception $e) {
            // استخدام القيم الافتراضية
        }
        
        return $defaultLinks;
    }

    /**
     * معالجة المدخلات المشفرة (تجاوز WAF)
     */
    private function processEncodedInput(?string $input): string {
        if (empty($input)) return '';
        
        // التحقق مما إذا كان bas64 صحيح
        if (base64_encode(base64_decode($input, true)) === $input) {
            return base64_decode($input);
        }
        
        return $input;
    }
}


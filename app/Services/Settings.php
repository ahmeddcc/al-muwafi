<?php
/**
 * خدمة الإعدادات
 * نظام المُوَفِّي لمهمات المكاتب
 */

namespace App\Services;

class Settings {
    private static array $cache = [];
    private static bool $loaded = false;
    
    /**
     * تحميل جميع الإعدادات
     */
    private static function load(): void {
        if (self::$loaded) {
            return;
        }
        
        try {
            $db = Database::getInstance();
            $settings = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM settings");
            
            foreach ($settings as $setting) {
                self::$cache[$setting['setting_key']] = self::castValue(
                    $setting['setting_value'],
                    $setting['setting_type']
                );
            }
            
            self::$loaded = true;
        } catch (\Exception $e) {
            // إعدادات افتراضية
            self::$cache = [];
        }
    }
    
    /**
     * الحصول على قيمة إعداد
     */
    public static function get(string $key, mixed $default = null): mixed {
        self::load();
        return self::$cache[$key] ?? $default;
    }
    
    /**
     * تعيين قيمة إعداد
     */
    public static function set(string $key, mixed $value, ?string $group = null): bool {
        try {
            $db = Database::getInstance();
            
            // التحقق من وجود الإعداد
            $exists = $db->exists('settings', 'setting_key = :key', ['key' => $key]);
            
            if ($exists) {
                $updateData = ['setting_value' => is_array($value) ? json_encode($value) : (string) $value];
                // تحديث المجموعة إذا تم تحديدها
                if ($group !== null) {
                    $updateData['setting_group'] = $group;
                }
                $db->update('settings', $updateData, 'setting_key = :key', ['key' => $key]);
            } else {
                $insertData = [
                    'setting_key' => $key,
                    'setting_value' => is_array($value) ? json_encode($value) : (string) $value,
                    'setting_type' => is_array($value) ? 'json' : 'text',
                ];
                // إضافة المجموعة إذا تم تحديدها
                if ($group !== null) {
                    $insertData['setting_group'] = $group;
                }
                $db->insert('settings', $insertData);
            }
            
            self::$cache[$key] = $value;
            return true;
        } catch (\Exception $e) {
            ErrorLogger::logError('settings_set', $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    /**
     * الحصول على مجموعة إعدادات
     */
    public static function getGroup(string $group): array {
        try {
            $db = Database::getInstance();
            $settings = $db->fetchAll(
                "SELECT setting_key, setting_value, setting_type FROM settings WHERE setting_group = :group",
                ['group' => $group]
            );
            
            $result = [];
            foreach ($settings as $setting) {
                $key = str_replace($group . '_', '', $setting['setting_key']);
                $result[$key] = self::castValue($setting['setting_value'], $setting['setting_type']);
            }
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * تحديث مجموعة إعدادات
     */
    public static function setGroup(string $group, array $values): bool {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            foreach ($values as $key => $value) {
                $fullKey = strpos($key, $group) === 0 ? $key : $group . '_' . $key;
                // تمرير اسم المجموعة للتأكد من حفظها
                self::set($fullKey, $value, $group);
            }
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            ErrorLogger::logError('settings_set_group', $e->getMessage(), __FILE__, __LINE__);
            return false;
        }
    }
    
    /**
     * الحصول على إعدادات الشركة
     */
    public static function getCompanyInfo(): array {
        return [
            'name' => self::get('company_name', 'المُوَفِّي لخدمات ريكو'),
            'logo' => self::get('company_logo', ''),
            'favicon' => self::get('company_favicon', ''),
            'login_image' => self::get('company_login_image', ''),
            'email' => self::get('company_email', ''),
            'phone' => self::get('company_phone', ''),
            'whatsapp' => self::get('company_whatsapp', ''),
            'address' => self::get('company_address', ''),
            'map_embed' => self::get('company_map_embed', ''),
            'map_embed_url' => self::get('company_map_embed_url', ''),
        ];
    }
    
    /**
     * الحصول على روابط التواصل الاجتماعي
     */
    public static function getSocialLinks(): array {
        return [
            'facebook' => self::get('social_facebook', ''),
            'twitter' => self::get('social_twitter', ''),
            'instagram' => self::get('social_instagram', ''),
            'linkedin' => self::get('social_linkedin', ''),
            'youtube' => self::get('social_youtube', ''),
        ];
    }
    
    /**
     * التحقق من وضع الصيانة
     */
    public static function isMaintenanceMode(): bool {
        return (bool) self::get('general_maintenance_mode', false);
    }
    
    /**
     * التحقق من وضع قريباً (Coming Soon)
     */
    public static function isComingSoonMode(): bool {
        return (bool) self::get('general_coming_soon', false);
    }
    
    /**
     * تحويل القيمة حسب النوع
     */
    private static function castValue(?string $value, string $type): mixed {
        if ($value === null) {
            return null;
        }
        
        return match($type) {
            'boolean' => $value === '1' || $value === 'true',
            'number' => is_numeric($value) ? (int) $value : 0,
            'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }
    
    /**
     * إعادة تحميل الإعدادات
     */
    public static function refresh(): void {
        self::$cache = [];
        self::$loaded = false;
        self::load();
    }
}

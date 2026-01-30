<?php

namespace App\Models;

use App\Services\Database;

class Service {
    private static ?Database $db = null;

    private static function getDB(): Database {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    /**
     * جلب جميع الخدمات مع البحث والتقسيم
     */
    public static function getAll(int $limit, int $offset, string $search = ''): array {
        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND (name LIKE :search OR name_ar LIKE :search2)";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
        }

        return self::getDB()->fetchAll(
            "SELECT * FROM services WHERE $where ORDER BY sort_order, name LIMIT $limit OFFSET $offset",
            $params
        );
    }

    /**
     * حساب العدد الكلي للخدمات
     */
    public static function count(string $search = ''): int {
        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND (name LIKE :search OR name_ar LIKE :search2)";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
        }

        return (int) self::getDB()->fetchColumn("SELECT COUNT(*) FROM services WHERE $where", $params);
    }

    /**
     * جلب خدمة بواسطة المعرف
     */
    public static function find(int $id): ?array {
        return self::getDB()->fetchOne("SELECT * FROM services WHERE id = :id", ['id' => $id]) ?: null;
    }

    /**
     * إنشاء خدمة جديدة
     */
    public static function create(array $data): int {
        $data['slug'] = self::generateSlug($data['name']);
        return self::getDB()->insert('services', $data);
    }

    /**
     * تحديث خدمة موجودة
     */
    public static function update(int $id, array $data): bool {
        return self::getDB()->update('services', $data, 'id = :id', ['id' => $id]);
    }

    /**
     * حذف خدمة
     */
    public static function delete(int $id): bool {
        return self::getDB()->delete('services', 'id = :id', ['id' => $id]);
    }

    /**
     * تبديل الحالة
     */
    public static function toggleStatus(int $id): array {
        $service = self::find($id);
        if (!$service) {
            return ['success' => false, 'message' => 'الخدمة غير موجودة'];
        }

        $newStatus = $service['is_active'] ? 0 : 1;
        self::update($id, ['is_active' => $newStatus]);

        return [
            'success' => true,
            'is_active' => $newStatus,
            'message' => $newStatus ? 'تم تفعيل الخدمة' : 'تم تعطيل الخدمة'
        ];
    }

    /**
     * التحقق من وجود خدمة بواسطة Slug
     */
    public static function existsBySlug(string $slug): bool {
        return self::getDB()->exists('services', 'slug = :slug', ['slug' => $slug]);
    }

    /**
     * توليد Slug فريد
     */
    private static function generateSlug(string $name): string {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $name);
        $slug = trim($slug, '-');
        $slug = mb_strtolower($slug);
        
        $originalSlug = $slug;
        $counter = 1;
        
        while (self::existsBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

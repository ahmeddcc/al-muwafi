<?php
namespace App\Models;

use App\Services\Database;

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * جلب جميع الأقسام مع الفلترة والبحث
     */
    public function getAll($type = null, $search = null) {
        $where = "1=1";
        $params = [];
        
        if ($type && in_array($type, ['copier', 'printer', 'spare_part', 'service'])) {
            $where .= " AND c.type = :type";
            $params['type'] = $type;
        }

        if ($search) {
            $where .= " AND (c.name LIKE :search OR c.slug LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        return $this->db->fetchAll(
            "SELECT c.*, 
                (SELECT COUNT(*) FROM products WHERE category_id = c.id) as products_count,
                (SELECT COUNT(*) FROM spare_parts WHERE category_id = c.id) as parts_count
             FROM categories c 
             WHERE {$where} 
             ORDER BY c.type, c.sort_order",
            $params
        );
    }

    /**
     * حذف عدة أقسام
     */
    public function deleteMultiple(array $ids) {
        if (empty($ids)) return false;
        
        // التحقق من عدم وجود منتجات لأي من الأقسام
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $productsCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE category_id IN ($placeholders)",
            $ids
        );

        if ($productsCount > 0) {
            return 'linked_products';
        }

        // حذف الصور أولاً
        $images = $this->db->fetchAll(
            "SELECT image FROM categories WHERE id IN ($placeholders) AND image IS NOT NULL", 
            $ids
        );
        
        // هنا يجب معالجة حذف الصور من السيرفر في الكنترولر أو تمرير ImageProcessor
        
        return $this->db->query(
            "DELETE FROM categories WHERE id IN ($placeholders)", 
            $ids
        );
    }

    /**
     * جلب قسم بواسطة المعرف
     */
    public function find($id) {
        return $this->db->fetchOne("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
    }

    /**
     * جلب الأقسام الرئيسية (للأباء)
     */
    public function getParents($excludeId = null) {
        $sql = "SELECT id, name, type FROM categories WHERE parent_id IS NULL";
        $params = [];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $sql .= " ORDER BY type, sort_order";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * إنشاء قسم جديد
     */
    public function create($data) {
        return $this->db->insert('categories', $data);
    }

    /**
     * تحديث قسم
     */
    public function update($id, $data) {
        return $this->db->update('categories', $data, 'id = :id', ['id' => $id]);
    }

    /**
     * حذف قسم
     */
    public function delete($id) {
        return $this->db->delete('categories', 'id = :id', ['id' => $id]);
    }

    /**
     * التحقق من وجود منتجات مرتبطة
     */
    public function hasProducts($id) {
        return $this->db->count('products', 'category_id = :id', ['id' => $id]) > 0;
    }

    /**
     * تبديل الحالة
     */
    public function toggleStatus($id) {
        $category = $this->find($id);
        if (!$category) return false;

        $newStatus = $category['is_active'] ? 0 : 1;
        $this->update($id, ['is_active' => $newStatus]);

        return $newStatus;
    }

    /**
     * الحصول على الترتيب التالي
     */
    public function getNextSortOrder($type) {
        return $this->db->fetchColumn(
            "SELECT COALESCE(MAX(sort_order), 0) + 1 FROM categories WHERE type = :type",
            ['type' => $type]
        );
    }

    /**
     * تحديث الترتيب
     */
    public function updateOrder($id, $order) {
        return $this->db->update(
            'categories',
            ['sort_order' => (int) $order],
            'id = :id',
            ['id' => (int) $id]
        );
    }

    /**
     * التحقق من وجود Slug
     */
    public function slugExists($slug, $excludeId = null) {
        $where = "slug = :slug";
        $params = ['slug' => $slug];
        
        if ($excludeId) {
            $where .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        return $this->db->exists('categories', $where, $params);
    }
}

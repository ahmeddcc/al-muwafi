<?php
/**
 * وحدة تحكم قطع الغيار
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class SparePartsController extends BaseController {
    
    /**
     * صفحة قطع الغيار
     */
    public function index(): void {
        $perPage = 12;
        $page = max(1, (int) $this->query('page', 1));
        $offset = ($page - 1) * $perPage;
        $categoryId = $this->query('category');
        
        // جلب الأقسام
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories 
             WHERE is_active = 1 AND type = 'spare_part' 
             ORDER BY sort_order"
        );
        
        $where = "sp.is_active = 1";
        $params = [];
        
        $currentCategory = null;
        if ($categoryId) {
            $currentCategory = $this->db->fetchOne(
                "SELECT * FROM categories WHERE id = :id AND type = 'spare_part'",
                ['id' => $categoryId]
            );
            
            if ($currentCategory) {
                $where .= " AND sp.category_id = :category_id";
                $params['category_id'] = $categoryId;
            }
        }
        
        // عد قطع الغيار
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM spare_parts sp WHERE {$where}",
            $params
        );
        
        // جلب قطع الغيار
        $spareParts = $this->db->fetchAll(
            "SELECT sp.*, c.name as category_name 
             FROM spare_parts sp 
             LEFT JOIN categories c ON sp.category_id = c.id 
             WHERE {$where} 
             ORDER BY sp.sort_order 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // جلب الموديلات المتوافقة لكل قطعة
        foreach ($spareParts as &$part) {
            $part['compatible_models'] = $this->db->fetchAll(
                "SELECT p.name, p.model FROM products p 
                 JOIN product_spare_parts psp ON p.id = psp.product_id 
                 WHERE psp.spare_part_id = :id LIMIT 5",
                ['id' => $part['id']]
            );
        }
        
        $totalPages = ceil($total / $perPage);
        $pageData = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'spare-parts' AND is_active = 1");
        
        $this->view('public.spare-parts', [
            'title' => $pageData['meta_title'] ?? 'قطع الغيار',
            'meta_description' => $pageData['meta_description'] ?? '',
            'spareParts' => $spareParts,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
    
    /**
     * تفاصيل قطعة غيار
     */
    public function show(string $slug = ''): void {
        if (empty($slug)) {
            $this->redirect('/spare-parts');
            return;
        }
        
        $part = $this->db->fetchOne(
            "SELECT sp.*, c.name as category_name 
             FROM spare_parts sp 
             LEFT JOIN categories c ON sp.category_id = c.id 
             WHERE sp.slug = :slug AND sp.is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$part) {
            http_response_code(404);
            include VIEWS_PATH . '/errors/404.php';
            return;
        }
        
        // جلب الموديلات المتوافقة
        $compatibleModels = $this->db->fetchAll(
            "SELECT p.* FROM products p 
             JOIN product_spare_parts psp ON p.id = psp.product_id 
             WHERE psp.spare_part_id = :id AND p.is_active = 1",
            ['id' => $part['id']]
        );
        
        $this->view('public.spare-part-details', [
            'title' => $part['name'],
            'part' => $part,
            'compatibleModels' => $compatibleModels,
        ]);
    }
}

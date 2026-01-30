<?php
/**
 * وحدة تحكم المنتجات (الموقع العام)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class ProductsController extends BaseController {
    
    /**
     * صفحة المنتجات
     */
    public function index(): void {
        $categorySlug = $this->query('category');
        $perPage = (int) \App\Services\Settings::get('products_per_page', 12);
        $page = max(1, (int) $this->query('page', 1));
        $offset = ($page - 1) * $perPage;
        
        // جلب الأقسام
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories 
             WHERE is_active = 1 AND type IN ('copier', 'printer') 
             ORDER BY sort_order"
        );
        
        // بناء الاستعلام
        $where = "p.is_active = 1";
        $params = [];
        
        $category = null;
        if ($categorySlug) {
            $category = $this->db->fetchOne(
                "SELECT * FROM categories WHERE slug = :slug AND is_active = 1",
                ['slug' => $categorySlug]
            );
            
            if ($category) {
                $where .= " AND p.category_id = :category_id";
                $params['category_id'] = $category['id'];
            }
        }
        
        // عد المنتجات
        $totalProducts = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM products p WHERE {$where}",
            $params
        );
        
        // جلب المنتجات
        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE {$where} 
             ORDER BY p.sort_order 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $totalPages = ceil($totalProducts / $perPage);
        
        $pageData = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'products' AND is_active = 1");
        
        $this->view('public.products', [
            'title' => $category ? $category['name'] : ($pageData['meta_title'] ?? 'المنتجات'),
            'meta_description' => $pageData['meta_description'] ?? '',
            'categories' => $categories,
            'products' => $products,
            'currentCategory' => $category,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
        ]);
    }
    
    /**
     * صفحة تفاصيل المنتج
     */
    public function show(string $slug = ''): void {
        if (empty($slug)) {
            $this->redirect('/products');
            return;
        }
        
        $product = $this->db->fetchOne(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = :slug AND p.is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$product) {
            http_response_code(404);
            include VIEWS_PATH . '/errors/404.php';
            return;
        }
        
        // زيادة عداد المشاهدات
        $this->db->query(
            "UPDATE products SET views_count = views_count + 1 WHERE id = :id",
            ['id' => $product['id']]
        );
        
        // جلب صور المنتج
        $images = $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order",
            ['id' => $product['id']]
        );
        
        // جلب الأعطال الشائعة (إذا مفعل)
        $faults = [];
        if ($product['show_faults']) {
            $faults = $this->db->fetchAll(
                "SELECT *, fault_name as title, fault_code as error_code FROM product_faults WHERE product_id = :id ORDER BY is_common DESC",
                ['id' => $product['id']]
            );
        }
        
        // جلب قطع الغيار المتوافقة (إذا مفعل)
        $spareParts = [];
        if ($product['show_spare_parts']) {
            $spareParts = $this->db->fetchAll(
                "SELECT sp.* FROM spare_parts sp 
                 JOIN product_spare_parts psp ON sp.id = psp.spare_part_id 
                 WHERE psp.product_id = :id AND sp.is_active = 1 
                 ORDER BY sp.name",
                ['id' => $product['id']]
            );
        }
        
        // جلب منتجات مشابهة
        $relatedProducts = $this->db->fetchAll(
            "SELECT * FROM products 
             WHERE category_id = :category_id AND id != :id AND is_active = 1 
             ORDER BY RAND() LIMIT 4",
            ['category_id' => $product['category_id'], 'id' => $product['id']]
        );
        
        $this->view('public.product-details', [
            'title' => $product['name'],
            'meta_description' => $product['meta_description'] ?? mb_substr(strip_tags($product['description']), 0, 160),
            'product' => $product,
            'images' => $images,
            'faults' => $faults,
            'spareParts' => $spareParts,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}

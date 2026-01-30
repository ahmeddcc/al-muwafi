<?php
/**
 * Demo Controller for New Products Design
 */

namespace App\Controllers;

class ProductsDemoController extends BaseController {
    
    public function index(): void {
        $perPage = 12;
        $page = max(1, (int) $this->query('page', 1));
        
        // Fetch real categories
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories 
             WHERE is_active = 1 AND type IN ('copier', 'printer') 
             ORDER BY sort_order"
        );
        
        // Fetch real products (limit 12 for demo)
        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_active = 1 
             ORDER BY p.sort_order 
             LIMIT 12"
        );
        
        // Create demo filter data if no real products exist
        if (empty($products)) {
            $products = $this->getMockProducts();
        }

        // Render the new demo view
        // Note: We're not using the layout from BaseController->view() to have full control over the HTML 
        // similar to how we did with home_demo_final.php, or we can use the view() but with a new template file.
        // For the cleanest demo, I'll include the view directly or use view() if I create the file properly.
        
        // I will create views/public/products_demo.php and load it.
        $this->view('public.products_demo', [
            'title' => 'المنتجات - تصميم جديد',
            'categories' => $categories,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => 5, // Mock
            'totalProducts' => 50 // Mock
        ]);
    }

    private function getMockProducts() {
        return [
            [
                'id' => 1,
                'name' => 'Ricoh IM C2000',
                'description' => 'طابعة ليزر ملونة متعددة المهام، سرعة 20 صفحة في الدقيقة',
                'image' => 'default_product.png',
                'price' => 15000,
                'category_name' => 'طابعات ألوان',
                'is_new' => 1,
                'is_featured' => 1
            ],
            [
                'id' => 2,
                'name' => 'Ricoh MP 301',
                'description' => 'طابعة ليزر أسود متعددة المهام، اقتصادية وعملية',
                'image' => 'default_product.png',
                'price' => 8500,
                'category_name' => 'طابعات أسود',
                'is_new' => 0,
                'is_featured' => 1
            ],
            [
                'id' => 3,
                'name' => 'Ricoh Pro C5300s',
                'description' => 'طابعة إنتاجية عالية السرعة للمطابع الرقمية',
                'image' => 'default_product.png',
                'price' => 120000,
                'category_name' => 'طابعات إنتاجية',
                'is_new' => 1,
                'is_featured' => 0
            ],
            [
                'id' => 4,
                'name' => 'Ricoh IM C4500',
                'description' => 'أداء عالي للمكاتب الكبيرة، سرعة 45 صفحة في الدقيقة',
                'image' => 'default_product.png',
                'price' => 35000,
                'category_name' => 'طابعات ألوان',
                'is_new' => 0,
                'is_featured' => 1
            ],
            // Duplicate for grid filling
            [
                'id' => 5, 'name' => 'Ricoh MP C3004', 'description' => 'طابعة متعددة المهام ذكية', 'image' => 'default_product.png', 'price' => 18000, 'category_name' => 'طابعات ألوان', 'is_new' => 0, 'is_featured' => 0
            ],
            [
                'id' => 6, 'name' => 'Ricoh SP 3710DN', 'description' => 'طابعة مكتبية صغيرة الحجم', 'image' => 'default_product.png', 'price' => 4500, 'category_name' => 'طابعات صغيرة', 'is_new' => 0, 'is_featured' => 0
            ]
        ];
    }
}

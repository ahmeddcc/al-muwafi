<?php
/**
 * وحدة تحكم المنتجات (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ImageProcessor;
use App\Services\AIContentService;

class ProductsController extends BaseController {
    private ImageProcessor $imageProcessor;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
    }
    
    /**
     * قائمة المنتجات
     */
    public function index(): void {
        $this->requirePermission('products.view');
        
        $categoryId = $this->query('category');
        $search = $this->query('search');
        $page = max(1, (int) $this->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $where = "1=1";
        $params = [];
        
        if ($categoryId) {
            $where .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }
        
        if ($search) {
            $where .= " AND (p.name LIKE :search OR p.model LIKE :search2)";
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }
        
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM products p WHERE {$where}", $params);
        
        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE {$where} 
             ORDER BY p.sort_order 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE type IN ('copier', 'printer') AND is_active = 1 ORDER BY sort_order"
        );
        
        $this->view('admin.products.index', [
            'title' => 'إدارة المنتجات',
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total,
        ]);
    }
    
    /**
     * إضافة منتج
     */
    public function create(): void {
        $this->requirePermission('products.create');
        
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE type IN ('copier', 'printer') AND is_active = 1 ORDER BY type, sort_order"
        );
        
        $this->view('admin.products.form', [
            'title' => 'إضافة منتج جديد',
            'product' => null,
            'categories' => $categories,
            'images' => [],
        ]);
    }
    
    /**
     * حفظ منتج جديد
     */
    public function store(): void {
        $this->requirePermission('products.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/products', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->prepareData();
        
        if (empty($data['name'])) {
            $this->redirect('/admin/products/create', ['error' => 'اسم المنتج مطلوب']);
            return;
        }
        
        $data['slug'] = $this->generateSlug($data['name']);
        $data['sort_order'] = $this->db->fetchColumn("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM products");
        
        // توليد كود المنتج إذا لم يتم إدخاله
        if (empty($data['product_code'])) {
            $data['product_code'] = $this->generateProductCode();
        }
        
        // معالجة الصورة المصغرة مع قراءة الإعدادات
        if (!empty($_FILES['thumbnail']['name'])) {
            // قراءة إعدادات الصور من قاعدة البيانات
            $imgSettings = \App\Services\Settings::getGroup('images');
            $removeBackground = ($imgSettings['remove_background_enabled'] ?? '0') === '1';
            $addWatermark = ($imgSettings['watermark_enabled'] ?? '1') === '1';
            
            $options = [
                'resize' => ['width' => 800, 'height' => 800], // تصغير تلقائي دائماً
                'remove_background' => $removeBackground,
                'watermark' => $addWatermark
            ];
            $result = $this->imageProcessor->uploadWithProcessing($_FILES['thumbnail'], 'products', $options);
            if ($result['success']) {
                $data['thumbnail'] = $result['path'];
            }
        }
        
        $productId = $this->db->insert('products', $data);
        
        // معالجة الصور الإضافية
        $this->handleImages($productId);
        
        $this->redirect('/admin/products', ['success' => 'تم إضافة المنتج بنجاح']);
    }
    
    /**
     * تعديل منتج
     */
    public function edit(int $id): void {
        $this->requirePermission('products.edit');
        
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        
        if (!$product) {
            $this->redirect('/admin/products', ['error' => 'المنتج غير موجود']);
            return;
        }
        
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE type IN ('copier', 'printer') AND is_active = 1 ORDER BY type, sort_order"
        );
        
        $images = $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order",
            ['id' => $id]
        );
        
        // جلب جميع قطع الغيار المتاحة
        $allSpareParts = $this->db->fetchAll(
            "SELECT id, name, part_number as code FROM spare_parts WHERE is_active = 1 ORDER BY name"
        );
        
        // جلب قطع الغيار المرتبطة بالمنتج
        $linkedSpareParts = $this->db->fetchAll(
            "SELECT spare_part_id FROM product_spare_parts WHERE product_id = :id",
            ['id' => $id]
        );
        $linkedSparePartIds = array_column($linkedSpareParts, 'spare_part_id');
        
        // جلب الأعطال الشائعة للمنتج
        $productFaults = $this->db->fetchAll(
            "SELECT id, product_id, fault_name as title, description, solution FROM product_faults WHERE product_id = :id ORDER BY id",
            ['id' => $id]
        );
        
        $this->view('admin.products.form', [
            'title' => 'تعديل المنتج',
            'product' => $product,
            'categories' => $categories,
            'images' => $images,
            'allSpareParts' => $allSpareParts,
            'linkedSparePartIds' => $linkedSparePartIds,
            'productFaults' => $productFaults,
        ]);
    }
    
    /**
     * تحديث منتج
     */
    public function update(int $id): void {
        $this->requirePermission('products.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/products', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        
        if (!$product) {
            $this->redirect('/admin/products', ['error' => 'المنتج غير موجود']);
            return;
        }
        
        $data = $this->prepareData();
        
        if (empty($data['name'])) {
            $this->redirect('/admin/products/edit/' . $id, ['error' => 'اسم المنتج مطلوب']);
            return;
        }
        
        if ($data['name'] !== $product['name']) {
            $data['slug'] = $this->generateSlug($data['name'], $id);
        }
        
        // التحقق من طلب حذف الصورة المصغرة
        if ($this->input('delete_thumbnail') === '1') {
            if ($product['thumbnail']) {
                $this->imageProcessor->delete($product['thumbnail']);
            }
            $data['thumbnail'] = null;
        }
        // معالجة الصورة المصغرة الجديدة (مع حذف القديمة تلقائياً)
        elseif (!empty($_FILES['thumbnail']['name'])) {
            // حذف الصورة القديمة قبل رفع الجديدة
            if ($product['thumbnail']) {
                $this->imageProcessor->delete($product['thumbnail']);
            }
            
            // قراءة إعدادات الصور من قاعدة البيانات
            $imgSettings = \App\Services\Settings::getGroup('images');
            $removeBackground = ($imgSettings['remove_background_enabled'] ?? '0') === '1';
            $addWatermark = ($imgSettings['watermark_enabled'] ?? '1') === '1';
            
            $options = [
                'resize' => ['width' => 800, 'height' => 800], // تصغير تلقائي دائماً
                'remove_background' => $removeBackground,
                'watermark' => $addWatermark
            ];
            $result = $this->imageProcessor->uploadWithProcessing($_FILES['thumbnail'], 'products', $options);
            if ($result['success']) {
                $data['thumbnail'] = $result['path'];
            }
        }
        
        $this->db->update('products', $data, 'id = :id', ['id' => $id]);
        
        // معالجة الصور الإضافية
        $this->handleImages($id);
        
        // معالجة قطع الغيار المرتبطة
        $this->handleSpareParts($id);
        
        // معالجة الأعطال الشائعة
        $this->handleFaults($id);
        
        $this->redirect('/admin/products', ['success' => 'تم تحديث المنتج بنجاح']);
    }
    
    /**
     * حذف منتج
     */
    public function delete(int $id): void {
        $this->requirePermission('products.delete');
        
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = :id", ['id' => $id]);
        
        if (!$product) {
            $this->json(['success' => false, 'error' => 'المنتج غير موجود'], 404);
            return;
        }
        
        // حذف الصور
        if ($product['thumbnail']) {
            $this->imageProcessor->delete($product['thumbnail']);
        }
        
        $images = $this->db->fetchAll("SELECT image_path FROM product_images WHERE product_id = :id", ['id' => $id]);
        foreach ($images as $img) {
            $this->imageProcessor->delete($img['image_path']);
        }
        
        $this->db->delete('product_images', 'product_id = :id', ['id' => $id]);
        $this->db->delete('products', 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true, 'message' => 'تم حذف المنتج بنجاح']);
    }
    
    /**
     * حذف صورة إضافية (AJAX)
     */
    public function deleteImage(int $imageId): void {
        $this->requirePermission('products.edit');
        
        $image = $this->db->fetchOne("SELECT * FROM product_images WHERE id = :id", ['id' => $imageId]);
        
        if (!$image) {
            $this->json(['success' => false, 'error' => 'الصورة غير موجودة'], 404);
            return;
        }
        
        // حذف الملف من السيرفر
        if (!empty($image['image_path'])) {
            $this->imageProcessor->delete($image['image_path']);
        }
        
        // حذف السجل من قاعدة البيانات
        $this->db->delete('product_images', 'id = :id', ['id' => $imageId]);
        
        $this->json(['success' => true]);
    }
    
    /**
     * تبديل الحالة
     */
    public function toggleStatus(int $id): void {
        $this->requirePermission('products.edit');
        
        $product = $this->db->fetchOne("SELECT is_active FROM products WHERE id = :id", ['id' => $id]);
        
        if (!$product) {
            $this->json(['success' => false, 'error' => 'المنتج غير موجود'], 404);
            return;
        }
        
        $newStatus = $product['is_active'] ? 0 : 1;
        $this->db->update('products', ['is_active' => $newStatus], 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true, 'is_active' => $newStatus]);
    }
    
    /**
     * توليد وصف بالذكاء الاصطناعي
     */
    public function generateDescription(): void {
        $this->requirePermission('products.edit');
        
        $name = $this->input('name');
        $model = $this->input('model');
        
        if (empty($name)) {
            $this->json(['success' => false, 'error' => 'اسم المنتج مطلوب']);
            return;
        }
        
        $ai = new AIContentService();
        $description = $ai->generateProductDescription($name, ['model' => $model]);
        
        if ($description) {
            $this->json(['success' => true, 'description' => $description]);
        } else {
            $this->json(['success' => false, 'error' => 'فشل توليد الوصف']);
        }
    }
    
    private function prepareData(): array {
        return [
            'name' => $this->input('name'),
            'category_id' => (int) $this->input('category_id'),
            'model' => $this->input('model'),
            'description' => $this->input('description'),
            'specifications' => $this->input('specifications'),
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'is_active' => $this->input('is_active') ? 1 : 0,
            'show_faults' => $this->input('show_faults') ? 1 : 0,
            'show_spare_parts' => $this->input('show_spare_parts') ? 1 : 0,
        ];
    }
    
    private function handleImages(int $productId): void {
        if (!empty($_FILES['images']['name'][0])) {
            $results = $this->imageProcessor->uploadMultiple($_FILES['images'], 'products');
            foreach ($results as $i => $result) {
                if ($result['success']) {
                    $this->db->insert('product_images', [
                        'product_id' => $productId,
                        'image_path' => $result['path'],
                        'thumbnail_path' => $result['thumbnail'] ?? $result['path'],
                        'is_primary' => $i === 0 ? 1 : 0,
                        'sort_order' => $i,
                    ]);
                }
            }
        }
    }
    
    private function generateSlug(string $name, ?int $excludeId = null): string {
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $name);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $originalSlug = $slug;
        $counter = 1;
        
        $where = "slug = :slug";
        $params = ['slug' => $slug];
        
        if ($excludeId) {
            $where .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        while ($this->db->exists('products', $where, $params)) {
            $slug = $originalSlug . '-' . $counter;
            $params['slug'] = $slug;
            $counter++;
        }
        
        return $slug;
    }

    private function generateProductCode(): string {
        $prefix = 'PRD-';
        do {
            // توليد كود عشوائي من 6 أحرف/أرقام
            $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            $code = $prefix . $random;
        } while ($this->db->exists('products', 'product_code = :code', ['code' => $code]));
        
        return $code;
    }
    
    /**
     * معالجة قطع الغيار المرتبطة بالمنتج
     */
    private function handleSpareParts(int $productId): void {
        // حذف الروابط القديمة
        $this->db->delete('product_spare_parts', 'product_id = :id', ['id' => $productId]);
        
        // إضافة الروابط الجديدة
        $sparePartIds = $_POST['spare_parts'] ?? [];
        if (is_array($sparePartIds)) {
            foreach ($sparePartIds as $sparePartId) {
                $sparePartId = (int) $sparePartId;
                if ($sparePartId > 0) {
                    $this->db->insert('product_spare_parts', [
                        'product_id' => $productId,
                        'spare_part_id' => $sparePartId,
                    ]);
                }
            }
        }
    }
    
    /**
     * معالجة الأعطال الشائعة للمنتج
     */
    private function handleFaults(int $productId): void {
        // حذف الأعطال القديمة
        $this->db->delete('product_faults', 'product_id = :id', ['id' => $productId]);
        
        // إضافة الأعطال الجديدة
        $faultTitles = $_POST['fault_title'] ?? [];
        $faultDescriptions = $_POST['fault_description'] ?? [];
        $faultSolutions = $_POST['fault_solution'] ?? [];
        
        if (is_array($faultTitles)) {
            foreach ($faultTitles as $i => $title) {
                $title = trim($title);
                if (!empty($title)) {
                    $this->db->insert('product_faults', [
                        'product_id' => $productId,
                        'fault_name' => $title,
                        'description' => $faultDescriptions[$i] ?? '',
                        'solution' => $faultSolutions[$i] ?? '',
                    ]);
                }
            }
        }
    }
}

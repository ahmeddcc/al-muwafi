<?php
/**
 * وحدة تحكم الأقسام (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ImageProcessor;
use App\Models\Category;

class CategoriesController extends BaseController {
    private ImageProcessor $imageProcessor;
    private Category $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->imageProcessor = new ImageProcessor();
        $this->categoryModel = new Category();
    }
    
    /**
     * قائمة الأقسام
     */
    public function index(): void {
        $this->requirePermission('categories.view');
        
        $type = $this->query('type');
        $search = $this->query('search');
        
        $categories = $this->categoryModel->getAll($type, $search);
        
        $this->view('admin.categories.index', [
            'title' => 'إدارة الأقسام',
            'categories' => $categories,
            'currentType' => $type,
            'search' => $search,
        ]);
    }

    /**
     * حذف جماعي
     */
    public function bulkDelete(): void {
        $this->requirePermission('categories.delete');
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['ids'] ?? [];
        
        if (empty($ids)) {
            $this->json(['success' => false, 'error' => 'لم يتم تحديد أي عناصر'], 400);
            return;
        }
        
        // التحقق من الصور قبل الحذف لحذفها من السيرفر
        foreach ($ids as $id) {
            $category = $this->categoryModel->find($id);
            if ($category && $category['image']) {
                $this->imageProcessor->delete($category['image']);
            }
        }
        
        $result = $this->categoryModel->deleteMultiple($ids);
        
        if ($result === 'linked_products') {
            $this->json(['success' => false, 'error' => 'لا يمكن حذف بعض الأقسام لوجود منتجات مرتبطة بها'], 400);
            return;
        }
        
        $this->json(['success' => true, 'message' => 'تم حذف الأقسام المحددة بنجاح']);
    }
    
    /**
     * صفحة إنشاء قسم
     */
    public function create(): void {
        $this->requirePermission('categories.create');
        
        $parentCategories = $this->categoryModel->getParents();
        
        $this->view('admin.categories.form', [
            'title' => 'إضافة قسم جديد',
            'category' => null,
            'parentCategories' => $parentCategories,
        ]);
    }
    
    /**
     * حفظ قسم جديد
     */
    public function store(): void {
        $this->requirePermission('categories.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/categories', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->prepareData();
        
        // التحقق
        if (empty($data['name'])) {
            $this->redirect('/admin/categories/create', ['error' => 'اسم القسم مطلوب']);
            return;
        }
        
        // معالجة الصورة
        if (!empty($_FILES['image']['name'])) {
            $result = $this->imageProcessor->upload($_FILES['image'], 'categories');
            if ($result['success']) {
                $data['image'] = $result['path'];
            }
        }
        
        // توليد slug
        $data['slug'] = $this->generateSlug($data['name']);
        
        // الترتيب
        $data['sort_order'] = $this->categoryModel->getNextSortOrder($data['type']);
        
        $this->categoryModel->create($data);
        
        $this->redirect('/admin/categories', ['success' => 'تم إضافة القسم بنجاح']);
    }
    
    /**
     * صفحة تعديل قسم
     */
    public function edit(int $id): void {
        $this->requirePermission('categories.edit');
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->redirect('/admin/categories', ['error' => 'القسم غير موجود']);
            return;
        }
        
        $parentCategories = $this->categoryModel->getParents($id);
        
        $this->view('admin.categories.form', [
            'title' => 'تعديل القسم',
            'category' => $category,
            'parentCategories' => $parentCategories,
        ]);
    }
    
    /**
     * تحديث قسم
     */
    public function update(int $id): void {
        $this->requirePermission('categories.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/categories', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->redirect('/admin/categories', ['error' => 'القسم غير موجود']);
            return;
        }
        
        $data = $this->prepareData();
        
        if (empty($data['name'])) {
            $this->redirect('/admin/categories/edit/' . $id, ['error' => 'اسم القسم مطلوب']);
            return;
        }
        
        // معالجة الصورة
        if (!empty($_FILES['image']['name'])) {
            // حذف الصورة القديمة
            if ($category['image']) {
                $this->imageProcessor->delete($category['image']);
            }
            
            $result = $this->imageProcessor->upload($_FILES['image'], 'categories');
            if ($result['success']) {
                $data['image'] = $result['path'];
            }
        }
        
        // تحديث slug إذا تغير الاسم
        if ($data['name'] !== $category['name']) {
            $data['slug'] = $this->generateSlug($data['name'], $id);
        }
        
        $this->categoryModel->update($id, $data);
        
        $this->redirect('/admin/categories', ['success' => 'تم تحديث القسم بنجاح']);
    }
    
    /**
     * حذف قسم
     */
    public function delete(int $id): void {
        $this->requirePermission('categories.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $this->json(['success' => false, 'error' => 'القسم غير موجود'], 404);
            return;
        }
        
        // التحقق من عدم وجود منتجات
        if ($this->categoryModel->hasProducts($id)) {
            $this->json(['success' => false, 'error' => 'لا يمكن حذف قسم يحتوي على منتجات'], 400);
            return;
        }
        
        // حذف الصورة
        if ($category['image']) {
            $this->imageProcessor->delete($category['image']);
        }
        
        $this->categoryModel->delete($id);
        
        $this->json(['success' => true, 'message' => 'تم حذف القسم بنجاح']);
    }
    
    /**
     * إعادة ترتيب الأقسام
     */
    public function reorder(): void {
        $this->requirePermission('categories.reorder');
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'طلب غير صالح'], 400);
            return;
        }
        
        $orders = json_decode(file_get_contents('php://input'), true)['orders'] ?? [];
        
        foreach ($orders as $order) {
            $this->categoryModel->updateOrder($order['id'], $order['order']);
        }
        
        $this->json(['success' => true, 'message' => 'تم حفظ الترتيب']);
    }
    
    /**
     * تبديل الحالة
     */
    public function toggleStatus(int $id): void {
        $this->requirePermission('categories.edit');
        
        $newStatus = $this->categoryModel->toggleStatus($id);
        
        if ($newStatus === false) {
             $this->json(['success' => false, 'error' => 'القسم غير موجود'], 404);
             return;
        }
        
        $this->json(['success' => true, 'is_active' => $newStatus]);
    }
    
    /**
     * تحضير البيانات
     */
    private function prepareData(): array {
        return [
            'name' => $this->input('name'),
            'type' => $this->input('type'),
            'description' => $this->input('description'),
            'parent_id' => $this->input('parent_id') ?: null,
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];
    }
    
    /**
     * توليد slug فريد
     */
    private function generateSlug(string $name, ?int $excludeId = null): string {
        $slug = $this->slugify($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->categoryModel->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * تحويل النص إلى slug
     */
    private function slugify(string $text): string {
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}

<?php
/**
 * وحدة تحكم قطع الغيار (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ImageProcessor;
define('ROOT_PATH', dirname(__DIR__, 3)); // Ensure ROOT_PATH is defined if not global

class SparePartsController extends BaseController {
    
    /**
     * عرض قائمة قطع الغيار
     */
    public function index(): void {
        $this->requirePermission('spare_parts.view');
        
        $page = max(1, (int) $this->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $search = $this->query('search', '');
        $categoryId = $this->query('category', '');
        
        $where = "1=1";
        $params = [];
        
        if ($search) {
            $where .= " AND (sp.name LIKE :search OR sp.part_number LIKE :search2)";
            $params['search'] = "%$search%";
            $params['search2'] = "%$search%";
        }
        
        if ($categoryId) {
            $where .= " AND sp.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }
        
        // جلب قطع الغيار
        $sql = "SELECT sp.*, c.name as category_name,
                (SELECT COUNT(*) FROM product_spare_parts psp WHERE psp.spare_part_id = sp.id) as compat_count,
                (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM tags t JOIN spare_part_tags spt ON t.id = spt.tag_id WHERE spt.spare_part_id = sp.id) as tags_str
                FROM spare_parts sp 
                LEFT JOIN categories c ON sp.category_id = c.id 
                WHERE $where 
                ORDER BY sp.sort_order DESC, sp.id DESC 
                LIMIT $perPage OFFSET $offset";
        
        $spareParts = $this->db->fetchAll($sql, $params);
        
        // عدد الكل
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM spare_parts sp WHERE $where",
            $params
        );
        
        // الأقسام للفلتر
        $categories = $this->db->fetchAll(
            "SELECT id, name FROM categories WHERE type = 'spare_part' AND is_active = 1 ORDER BY name"
        );
        
        $this->view('admin.spare-parts.index', [
            'title' => 'إدارة قطع الغيار',
            'spareParts' => $spareParts,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total,
        ]);
    }
    
    /**
     * نموذج إضافة قطعة غيار
     */
    public function create(): void {
        $this->requirePermission('spare_parts.create');
        
        $categories = $this->db->fetchAll(
            "SELECT id, name FROM categories WHERE type = 'spare_part' AND is_active = 1 ORDER BY name"
        );
        
        $this->view('admin.spare-parts.form', [
            'title' => 'إضافة قطعة غيار',
            'sparePart' => null,
            'categories' => $categories,
        ]);
    }
    
    /**
     * حفظ قطعة غيار جديدة
     */
    public function store(): void {
        $this->requirePermission('spare_parts.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/spare-parts', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->allInput();
        
        // التحقق من البيانات
        if (empty($data['name']) || empty($data['part_number'])) {
            $this->redirect('/admin/spare-parts/create', ['error' => 'الاسم ورقم القطعة مطلوبان']);
            return;
        }
        
        // التحقق من عدم تكرار رقم القطعة
        $exists = $this->db->exists('spare_parts', 'part_number = :pn', ['pn' => $data['part_number']]);
        if ($exists) {
            $this->redirect('/admin/spare-parts/create', ['error' => 'رقم القطعة موجود مسبقاً']);
            return;
        }
        
        // إنشاء الـ slug
        $slug = $this->generateSlug($data['name']);
        
        // معالجة الصورة مع إزالة الخلفية والعلامة المائية
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $imageProcessor = new ImageProcessor();
            $options = [
                'resize' => ['width' => 600, 'height' => 600],
                'remove_background' => true,
                'watermark' => true
            ];
            $result = $imageProcessor->uploadWithProcessing($_FILES['image'], 'spare-parts', $options);
            if ($result['success']) {
                $image = $result['path'];
            }
        }
        
        // الإدراج
        $this->db->insert('spare_parts', [
            'name' => $data['name'],
            'slug' => $slug,
            'part_number' => $data['part_number'],
            'category_id' => $data['category_id'] ?: null,
            'description' => $data['description'] ?? '',
            'image' => $image,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ]);
        
        // معالجة الوسوم
        $this->handleTags($this->db->lastInsertId(), $data['tags'] ?? []);

        // معالجة التوافقية (الماكينات)
        $this->handleCompatibility($this->db->lastInsertId(), $data['compatible_products'] ?? []);

        // معالجة المعرض (الصور والملفات)
        $this->handleGallery($this->db->lastInsertId());
        
        $this->redirect('/admin/spare-parts', ['success' => 'تم إضافة قطعة الغيار بنجاح']);
    }
    
    /**
     * نموذج تعديل قطعة غيار
     */
    public function edit(int $id): void {
        $this->requirePermission('spare_parts.edit');
        
        $sparePart = $this->db->fetchOne("SELECT * FROM spare_parts WHERE id = :id", ['id' => $id]);
        
        if (!$sparePart) {
            $this->redirect('/admin/spare-parts', ['error' => 'قطعة الغيار غير موجودة']);
            return;
        }
        
        $categories = $this->db->fetchAll(
            "SELECT id, name FROM categories WHERE type = 'spare_part' AND is_active = 1 ORDER BY name"
        );
        
        // جلب الوسوم
        $tags = $this->db->fetchAll(
            "SELECT t.name FROM tags t 
             JOIN spare_part_tags spt ON t.id = spt.tag_id 
             WHERE spt.spare_part_id = :id",
            ['id' => $id]
        );
        $tags = array_column($tags, 'name');

        // جلب الماكينات المتوافقة
        $compatibleProducts = $this->db->fetchAll(
            "SELECT p.id, p.name, p.model, p.thumbnail FROM products p
             JOIN product_spare_parts psp ON p.id = psp.product_id
             WHERE psp.spare_part_id = :id",
            ['id' => $id]
        );

        // جلب المعرض
        $gallery = $this->db->fetchAll(
            "SELECT * FROM spare_part_media WHERE spare_part_id = :id ORDER BY sort_order ASC",
            ['id' => $id]
        );

        $this->view('admin.spare-parts.form', [
            'title' => 'تعديل قطعة الغيار',
            'sparePart' => $sparePart,
            'categories' => $categories,
            'currentTags' => $tags,
            'compatibleProducts' => $compatibleProducts,
            'gallery' => $gallery
        ]);
    }
    
    /**
     * تحديث قطعة غيار
     */
    public function update(int $id): void {
        $this->requirePermission('spare_parts.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/spare-parts', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $sparePart = $this->db->fetchOne("SELECT * FROM spare_parts WHERE id = :id", ['id' => $id]);
        
        if (!$sparePart) {
            $this->redirect('/admin/spare-parts', ['error' => 'قطعة الغيار غير موجودة']);
            return;
        }
        
        $data = $this->allInput();
        
        // التحقق من البيانات
        if (empty($data['name']) || empty($data['part_number'])) {
            $this->redirect("/admin/spare-parts/edit/$id", ['error' => 'الاسم ورقم القطعة مطلوبان']);
            return;
        }
        
        // التحقق من عدم تكرار رقم القطعة
        $exists = $this->db->exists('spare_parts', 'part_number = :pn AND id != :id', [
            'pn' => $data['part_number'],
            'id' => $id
        ]);
        if ($exists) {
            $this->redirect("/admin/spare-parts/edit/$id", ['error' => 'رقم القطعة موجود مسبقاً']);
            return;
        }
        
        $updateData = [
            'name' => $data['name'],
            'part_number' => $data['part_number'],
            'category_id' => $data['category_id'] ?: null,
            'description' => $data['description'] ?? '',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];
        
        // معالجة الصورة الجديدة مع إزالة الخلفية والعلامة المائية
        if (!empty($_FILES['image']['name'])) {
            $imageProcessor = new ImageProcessor();
            $options = [
                'resize' => ['width' => 600, 'height' => 600],
                'remove_background' => true,
                'watermark' => true
            ];
            $result = $imageProcessor->uploadWithProcessing($_FILES['image'], 'spare-parts', $options);
            if ($result['success']) {
                // حذف الصورة القديمة
                if ($sparePart['image']) {
                    $imageProcessor->delete($sparePart['image']);
                }
                $updateData['image'] = $result['path'];
            }
        }
        
        $this->db->update('spare_parts', $updateData, 'id = :id', ['id' => $id]);
        
        // معالجة الوسوم
        $this->handleTags($id, $data['tags'] ?? []);

        // معالجة التوافقية
        $this->handleCompatibility($id, $data['compatible_products'] ?? []);

        // معالجة المعرض
        $this->handleGallery($id);
        
        $this->redirect('/admin/spare-parts', ['success' => 'تم تحديث قطعة الغيار بنجاح']);
    }
    
    /**
     * حذف قطعة غيار
     */
    public function delete(int $id): void {
        $this->requirePermission('spare_parts.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        $sparePart = $this->db->fetchOne("SELECT * FROM spare_parts WHERE id = :id", ['id' => $id]);
        
        if (!$sparePart) {
            $this->json(['success' => false, 'message' => 'قطعة الغيار غير موجودة'], 404);
            return;
        }
        
        // حذف الصورة
        if ($sparePart['image']) {
            $imageProcessor = new ImageProcessor();
            $imageProcessor->delete($sparePart['image']);
        }
        
        $this->db->delete('spare_parts', 'id = :id', ['id' => $id]);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'تم حذف قطعة الغيار']);
        } else {
            $this->redirect('/admin/spare-parts', ['success' => 'تم حذف قطعة الغيار']);
        }
    }
    
    /**
     * حذف صورة قطعة الغيار
     */
    public function deleteImage(int $id): void {
        $this->requirePermission('spare_parts.edit');
        
        $sparePart = $this->db->fetchOne("SELECT id, image FROM spare_parts WHERE id = :id", ['id' => $id]);
        
        if (!$sparePart) {
            $this->json(['success' => false, 'message' => 'قطعة الغيار غير موجودة'], 404);
            return;
        }
        
        if ($sparePart['image']) {
            $imageProcessor = new ImageProcessor();
            $imageProcessor->delete($sparePart['image']);
            
            $this->db->update('spare_parts', ['image' => null], 'id = :id', ['id' => $id]);
            
            $this->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح']);
        } else {
            $this->json(['success' => false, 'message' => 'لا توجد صورة لحذفها']);
        }
    }
    
    /**
     * تبديل حالة التفعيل
     */
    public function toggleStatus(int $id): void {
        $this->requirePermission('spare_parts.edit');
        
        $sparePart = $this->db->fetchOne("SELECT id, is_active FROM spare_parts WHERE id = :id", ['id' => $id]);
        
        if (!$sparePart) {
            $this->json(['success' => false, 'message' => 'قطعة الغيار غير موجودة'], 404);
            return;
        }
        
        $newStatus = $sparePart['is_active'] ? 0 : 1;
        $this->db->update('spare_parts', ['is_active' => $newStatus], 'id = :id', ['id' => $id]);
        
        $this->json([
            'success' => true,
            'message' => $newStatus ? 'تم تفعيل قطعة الغيار' : 'تم تعطيل قطعة الغيار',
            'newStatus' => $newStatus
        ]);
    }
    
    /**
     * البحث عن قطع الغيار (JSON)
     */
    public function searchJSON(): void {
        $query = $this->query('q', '');
        
        // التحقق من صلاحية الوصول
        if (!$this->isAjax()) {
            $this->json(['results' => []]);
            return;
        }

        $params = [];
        $where = "is_active = 1";

        if (!empty($query)) {
            $where .= " AND (name LIKE :q OR part_number LIKE :q2)";
            $params['q'] = "%$query%";
            $params['q2'] = "%$query%";
        }

        $sql = "SELECT id, name, part_number, image FROM spare_parts 
                WHERE $where 
                ORDER BY name LIMIT 100";
        
        $results = $this->db->fetchAll($sql, $params);
        
        // تنسيق النتائج
        $formatted = array_map(function($item) {
            return [
                'id' => $item['id'],
                'text' => $item['name'] . ' (' . $item['part_number'] . ')',
                'name' => $item['name'],
                'part_number' => $item['part_number'],
                'image' => $item['image']
            ];
        }, $results);
        
        // الرد بتنسيق JSON
        $this->json(['results' => $formatted]);
    }

    /**
     * توليد slug فريد
     */
    /**
     * البحث عن المنتجات (للتوافقية)
     */
    public function searchProductsJSON(): void {
        $query = $this->query('q', '');
        
        if (!$this->isAjax()) {
            $this->json(['results' => []]);
            return;
        }

        $params = [];
        $where = "is_active = 1";

        if (!empty($query)) {
            $where .= " AND (name LIKE :q OR model LIKE :q2 OR product_code LIKE :q3)";
            $params['q'] = "%$query%";
            $params['q2'] = "%$query%";
            $params['q3'] = "%$query%";
        }

        $sql = "SELECT id, name, model, thumbnail FROM products 
                WHERE $where 
                ORDER BY name LIMIT 50";
        
        $results = $this->db->fetchAll($sql, $params);
        
        $formatted = array_map(function($item) {
            return [
                'id' => $item['id'],
                'text' => $item['name'] . ' (' . ($item['model'] ?: 'N/A') . ')',
                'image' => $item['thumbnail']
            ];
        }, $results);
        
        $this->json(['results' => $formatted]);
    }

    /**
     * حذف ملف من المعرض
     */
    public function deleteMedia(int $id): void {
        $this->requirePermission('spare_parts.edit');
        
        $media = $this->db->fetchOne("SELECT * FROM spare_part_media WHERE id = :id", ['id' => $id]);
        
        if ($media) {
            if (file_exists(ROOT_PATH . '/public/storage/uploads/' . $media['file_path'])) {
                unlink(ROOT_PATH . '/public/storage/uploads/' . $media['file_path']);
            }
            $this->db->delete('spare_part_media', 'id = :id', ['id' => $id]);
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'الملف غير موجود'], 404);
        }
    }

    // ================= HELPER METHODS =================

    private function handleTags(int $sparePartId, array $tags): void {
        // حذف الوسوم القديمة
        $this->db->delete('spare_part_tags', 'spare_part_id = :id', ['id' => $sparePartId]);

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;

            // التحقق أو إنشاء الوسم
            $tag = $this->db->fetchOne("SELECT id FROM tags WHERE name = :name", ['name' => $tagName]);
            if (!$tag) {
                $this->db->insert('tags', ['name' => $tagName]);
                $tagId = $this->db->lastInsertId();
            } else {
                $tagId = $tag['id'];
            }

            // ربط الوسم
            $this->db->insert('spare_part_tags', [
                'spare_part_id' => $sparePartId,
                'tag_id' => $tagId
            ]);
        }
    }

    private function handleCompatibility(int $sparePartId, array $productIds): void {
        // حذف الروابط القديمة (عكس جدول product_spare_parts حيث المفتاح هو product_id, spare_part_id)
        // نريد حذف كل الصفوف التي فيها spare_part_id = $sparePartId
        $this->db->delete('product_spare_parts', 'spare_part_id = :id', ['id' => $sparePartId]);

        foreach ($productIds as $productId) {
            $productId = (int) $productId;
            if ($productId > 0) {
                // قد يكون الرابط موجوداً من جهة المنتج، لذا نستخدم INSERT IGNORE أو نتأكد
                // لكننا حذفنا الكل للتو، لذا INSERT آمن (إلا إذا كررت الـ ID في الـ array)
                try {
                    $this->db->insert('product_spare_parts', [
                        'product_id' => $productId,
                        'spare_part_id' => $sparePartId
                    ]);
                } catch (\PDOException $e) {
                    // ignore duplicates
                }
            }
        }
    }

    private function handleGallery(int $sparePartId): void {
        if (!empty($_FILES['gallery']['name'][0])) {
            $imageProcessor = new ImageProcessor();
            $files = $_FILES['gallery'];
            
            // إعادة هيكلة مصفوفة $_FILES
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === 0) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    // التحقق من نوع الملف (PDF أو صورة)
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $type = 'image';
                    $options = ['resize' => ['width' => 1000, 'height' => 1000], 'watermark' => true];

                    if ($ext === 'pdf') {
                        $type = 'pdf';
                        // رفع مباشر بدون معالجة صور
                        $uploadDir = ROOT_PATH . '/public/storage/uploads/spare-parts/docs/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        
                        $filename = uniqid('doc_') . '.' . $ext;
                        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
                        $path = 'spare-parts/docs/' . $filename;
                    } else {
                        // معالجة صورة
                        $result = $imageProcessor->uploadWithProcessing($file, 'spare-parts/gallery', $options);
                        if ($result['success']) {
                            $path = $result['path'];
                        } else {
                            continue;
                        }
                    }

                    $this->db->insert('spare_part_media', [
                        'spare_part_id' => $sparePartId,
                        'file_path' => $path,
                        'file_type' => $type
                    ]);
                }
            }
        }
    }

    private function generateSlug(string $name): string {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $name);
        $slug = trim($slug, '-');
        $slug = mb_strtolower($slug);
        
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->db->exists('spare_parts', 'slug = :slug', ['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

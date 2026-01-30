<?php
/**
 * وحدة تحكم الصفحات (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PagesController extends BaseController {
    
    public function index(): void {
        $this->requirePermission('pages.view');
        
        try {
            $pages = $this->db->fetchAll("SELECT * FROM pages ORDER BY sort_order, title");
        } catch (\Exception $e) {
            // محاولة إنشاء الجدول إذا لم يكن موجوداً
            $this->db->query("
                CREATE TABLE IF NOT EXISTS pages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) NOT NULL,
                    content LONGTEXT,
                    meta_title VARCHAR(255),
                    meta_description TEXT,
                    sort_order INT DEFAULT 0,
                    is_active TINYINT(1) DEFAULT 1,
                    show_in_menu TINYINT(1) DEFAULT 0,
                    show_in_footer TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $pages = [];
        }
        
        $this->view('admin.pages.index', [
            'title' => 'إدارة الصفحات',
            'pages' => $pages,
        ]);
    }
    
    public function create(): void {
        $this->requirePermission('pages.create');
        
        $this->view('admin.pages.form', [
            'title' => 'إضافة صفحة',
            'page' => null,
        ]);
    }
    
    public function store(): void {
        $this->requirePermission('pages.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/pages', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->allInput();
        
        if (empty($data['title'])) {
            $this->redirect('/admin/pages/create', ['error' => 'عنوان الصفحة مطلوب']);
            return;
        }
        
        $slug = $this->generateSlug($data['title']);
        
        $this->db->insert('pages', [
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'] ?? '',
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? '',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'show_in_menu' => isset($data['show_in_menu']) ? 1 : 0,
            'show_in_footer' => isset($data['show_in_footer']) ? 1 : 0,
        ]);
        
        $this->redirect('/admin/pages', ['success' => 'تم إضافة الصفحة بنجاح']);
    }
    
    public function edit(int $id): void {
        $this->requirePermission('pages.edit');
        
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE id = :id", ['id' => $id]);
        
        if (!$page) {
            $this->redirect('/admin/pages', ['error' => 'الصفحة غير موجودة']);
            return;
        }
        
        $this->view('admin.pages.form', [
            'title' => 'تعديل الصفحة',
            'page' => $page,
        ]);
    }
    
    public function update(int $id): void {
        $this->requirePermission('pages.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/pages', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE id = :id", ['id' => $id]);
        
        if (!$page) {
            $this->redirect('/admin/pages', ['error' => 'الصفحة غير موجودة']);
            return;
        }
        
        $data = $this->allInput();
        
        $this->db->update('pages', [
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'meta_title' => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? '',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'show_in_menu' => isset($data['show_in_menu']) ? 1 : 0,
            'show_in_footer' => isset($data['show_in_footer']) ? 1 : 0,
        ], 'id = :id', ['id' => $id]);
        
        $this->redirect('/admin/pages', ['success' => 'تم تحديث الصفحة بنجاح']);
    }
    
    public function delete(int $id): void {
        $this->requirePermission('pages.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        $this->db->delete('pages', 'id = :id', ['id' => $id]);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'تم حذف الصفحة']);
        } else {
            $this->redirect('/admin/pages', ['success' => 'تم حذف الصفحة']);
        }
    }
    
    public function toggleStatus(int $id): void {
        $this->requirePermission('pages.edit');
        
        $page = $this->db->fetchOne("SELECT id, is_active FROM pages WHERE id = :id", ['id' => $id]);
        
        if (!$page) {
            $this->json(['success' => false, 'message' => 'الصفحة غير موجودة'], 404);
            return;
        }
        
        $newStatus = $page['is_active'] ? 0 : 1;
        $this->db->update('pages', ['is_active' => $newStatus], 'id = :id', ['id' => $id]);
        
        $this->json(['success' => true, 'is_active' => $newStatus]);
    }
    
    private function generateSlug(string $title): string {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $title);
        $slug = trim($slug, '-');
        $slug = mb_strtolower($slug);
        
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->db->exists('pages', 'slug = :slug', ['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

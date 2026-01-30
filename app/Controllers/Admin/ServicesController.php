<?php
/**
 * وحدة تحكم الخدمات (لوحة التحكم)
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Service;
use App\Services\ImageProcessor;

class ServicesController extends BaseController {
    
    /**
     * عرض قائمة الخدمات
     */
    public function index(): void {
        $this->requirePermission('services.view');
        
        $page = max(1, (int) $this->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $search = $this->query('search', '');
        
        // جلب الخدمات باستخدام الموديل
        $services = Service::getAll($perPage, $offset, $search);
        
        // عدد الكل
        $total = Service::count($search);
        
        $this->view('admin.services.index', [
            'title' => 'إدارة الخدمات',
            'services' => $services,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total,
        ]);
    }
    
    /**
     * نموذج إضافة خدمة
     */
    public function create(): void {
        $this->requirePermission('services.create');
        
        $this->view('admin.services.form', [
            'title' => 'إضافة خدمة',
            'service' => null,
        ]);
    }
    
    /**
     * حفظ خدمة جديدة
     */
    public function store(): void {
        $this->requirePermission('services.create');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/services', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = $this->allInput();
        
        if (empty($data['name'])) {
            $this->redirect('/admin/services/create', ['error' => 'اسم الخدمة مطلوب']);
            return;
        }
        
        $serviceData = [
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? $data['name'],
            'description' => $data['description'] ?? '',
            'description_ar' => $data['description_ar'] ?? $data['description'] ?? '',
            'icon' => $data['icon'] ?? '🔧',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];
        
        // معالجة الصورة
        if (!empty($_FILES['image']['name'])) {
            $imageProcessor = new ImageProcessor();
            $result = $imageProcessor->upload($_FILES['image'], 'services');
            if ($result['success']) {
                $serviceData['image'] = $result['filename'];
            }
        }
        
        Service::create($serviceData);
        
        $this->redirect('/admin/services', ['success' => 'تم إضافة الخدمة بنجاح']);
    }
    
    /**
     * نموذج تعديل خدمة
     */
    public function edit(int $id): void {
        $this->requirePermission('services.edit');
        
        $service = Service::find($id);
        
        if (!$service) {
            $this->redirect('/admin/services', ['error' => 'الخدمة غير موجودة']);
            return;
        }
        
        $this->view('admin.services.form', [
            'title' => 'تعديل الخدمة',
            'service' => $service,
        ]);
    }
    
    /**
     * تحديث خدمة
     */
    public function update(int $id): void {
        $this->requirePermission('services.edit');
        
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/admin/services', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $service = Service::find($id);
        
        if (!$service) {
            $this->redirect('/admin/services', ['error' => 'الخدمة غير موجودة']);
            return;
        }
        
        $data = $this->allInput();
        
        if (empty($data['name'])) {
            $this->redirect("/admin/services/edit/$id", ['error' => 'اسم الخدمة مطلوب']);
            return;
        }
        
        $updateData = [
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? $data['name'],
            'description' => $data['description'] ?? '',
            'description_ar' => $data['description_ar'] ?? $data['description'] ?? '',
            'icon' => $data['icon'] ?? '🔧',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];
        
        // معالجة الصورة الجديدة
        if (!empty($_FILES['image']['name'])) {
            $imageProcessor = new ImageProcessor();
            $result = $imageProcessor->upload($_FILES['image'], 'services');
            if ($result['success']) {
                if ($service['image']) {
                    $imageProcessor->delete($service['image']);
                }
                $updateData['image'] = $result['filename'];
            }
        }
        
        Service::update($id, $updateData);
        
        $this->redirect('/admin/services', ['success' => 'تم تحديث الخدمة بنجاح']);
    }
    
    /**
     * حذف خدمة
     */
    public function delete(int $id): void {
        $this->requirePermission('services.delete');
        
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'طلب غير صالح'], 403);
            return;
        }
        
        try {
            $service = Service::find($id);
            
            if (!$service) {
                $this->json(['success' => false, 'message' => 'الخدمة غير موجودة'], 404);
                return;
            }
            
            if ($service['image']) {
                $imageProcessor = new ImageProcessor();
                $imageProcessor->delete($service['image']);
            }
            
            Service::delete($id);
            
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'تم حذف الخدمة']);
            } else {
                $this->redirect('/admin/services', ['success' => 'تم حذف الخدمة']);
            }
        } catch (\Exception $e) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()], 500);
            } else {
                $this->redirect('/admin/services', ['error' => 'حدث خطأ أثناء الحذف']);
            }
        }
    }
    
    /**
     * تبديل حالة التفعيل
     */
    public function toggleStatus(int $id): void {
        $this->requirePermission('services.edit');
        
        $result = Service::toggleStatus($id);
        
        if (!$result['success']) {
            $this->json(['success' => false, 'message' => $result['message']], 404);
            return;
        }
        
        $this->json($result);
    }
}

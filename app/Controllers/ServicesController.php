<?php
/**
 * وحدة تحكم الخدمات
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class ServicesController extends BaseController {
    
    /**
     * صفحة الخدمات
     */
    public function index(): void {
        $services = $this->db->fetchAll(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order"
        );
        
        $pageData = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'services' AND is_active = 1");
        
        $this->view('public.services', [
            'title' => $pageData['meta_title'] ?? 'الخدمات',
            'meta_description' => $pageData['meta_description'] ?? '',
            'services' => $services,
        ]);
    }
    
    /**
     * تفاصيل خدمة
     */
    public function show(string $slug = ''): void {
        if (empty($slug)) {
            $this->redirect('/services');
            return;
        }
        
        $service = $this->db->fetchOne(
            "SELECT * FROM services WHERE slug = :slug AND is_active = 1",
            ['slug' => $slug]
        );
        
        if (!$service) {
            http_response_code(404);
            include VIEWS_PATH . '/errors/404.php';
            return;
        }
        
        $otherServices = $this->db->fetchAll(
            "SELECT * FROM services WHERE id != :id AND is_active = 1 ORDER BY sort_order LIMIT 4",
            ['id' => $service['id']]
        );
        
        $this->view('public.service-details', [
            'title' => $service['name'],
            'meta_description' => $service['short_description'] ?? '',
            'service' => $service,
            'otherServices' => $otherServices,
        ]);
    }
}

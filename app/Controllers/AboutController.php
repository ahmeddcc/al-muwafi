<?php
/**
 * وحدة تحكم صفحة من نحن
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class AboutController extends BaseController {
    
    /**
     * صفحة من نحن
     */
    public function index(): void {
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'about' AND is_active = 1");
        
        $sections = $this->db->fetchAll(
            "SELECT * FROM page_sections 
             WHERE page_id = :page_id AND is_active = 1 
             ORDER BY sort_order",
            ['page_id' => $page['id'] ?? 0]
        );
        
        $this->view('public.about', [
            'title' => $page['meta_title'] ?? 'من نحن',
            'meta_description' => $page['meta_description'] ?? '',
            'page' => $page,
            'sections' => $sections,
        ]);
    }
}

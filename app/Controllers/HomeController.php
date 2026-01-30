<?php
/**
 * وحدة تحكم الصفحة الرئيسية
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class HomeController extends BaseController {
    
    /**
     * الصفحة الرئيسية
     */
    public function index(): void {
        // جلب الخدمات المميزة
        $services = $this->db->fetchAll(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order LIMIT 6"
        );
        
        // جلب إحصائيات
        $stats = [
            'products' => $this->db->count('products', 'is_active = 1'),
            'services' => $this->db->count('services', 'is_active = 1'),
            'tickets_closed' => $this->db->count('maintenance_tickets', "status = 'closed'"),
        ];
        
        // جلب أقسام الصفحة الرئيسية
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'home' AND is_active = 1");
        
        $this->view('public.home', [
            'title' => $page['meta_title'] ?? 'الرئيسية',
            'meta_description' => $page['meta_description'] ?? '',
            'services' => $services,
            'stats' => $stats,
        ]);
    }
    public function demo(): void {
        // صفحة ديمو للتصميم القديم (Dark Glass)
        require_once VIEWS_PATH . '/public/home_demo.php';
    }

    public function demoV2(): void {
        // صفحة ديمو للتصميم الجديد (Clean Corporate)
        require_once VIEWS_PATH . '/public/home_demo_v2.php';
    }

    public function demoV3(): void {
        // صفحة ديمو من الخيال (Avant-Garde)
        require_once VIEWS_PATH . '/public/home_demo_v3.php';
    }

    public function demoV4(): void {
        // صفحة ديمو الفخامة (The Golden Standard)
        require_once VIEWS_PATH . '/public/home_demo_v4.php';
    }

    public function demoV5(): void {
        // صفحة ديمو الماكينة الرقمية (The Digital Machine)
        require_once VIEWS_PATH . '/public/home_demo_v5.php';
    }

    public function demoV6(): void {
        // صفحة ديمو الشبكة الذكية (Bespoke Bento)
        require_once VIEWS_PATH . '/public/home_demo_v6.php';
    }

    // === New Design Demos ===
    
    public function demoApple(): void {
        require_once VIEWS_PATH . '/public/home_demo_apple.php';
    }

    public function demoCorporate(): void {
        require_once VIEWS_PATH . '/public/home_demo_corporate.php';
    }

    public function demoZen(): void {
        require_once VIEWS_PATH . '/public/home_demo_zen.php';
    }

    public function demoEditorial(): void {
        require_once VIEWS_PATH . '/public/home_demo_editorial.php';
    }

    public function demoBrutalism(): void {
        require_once VIEWS_PATH . '/public/home_demo_brutalism.php';
    }

    public function demoPrecision(): void {
        // تصميم هندسة الدقة (Technology + Stability + Luxury)
        require_once VIEWS_PATH . '/public/home_demo_precision.php';
    }

    public function demoPremium(): void {
        // تصميم الهوية البصرية المتقدمة (3D + Effects + Professional)
        require_once VIEWS_PATH . '/public/home_demo_premium.php';
    }

    public function demoRefined(): void {
        // تصميم الفخامة الهادئة (Simplicity + Luxury + Technology + Elegance)
        require_once VIEWS_PATH . '/public/home_demo_refined.php';
    }

    public function demoCinematic(): void {
        // تصميم سينمائي من الخيال (Neon + Gradients + Maximum Impact)
        require_once VIEWS_PATH . '/public/home_demo_cinematic.php';
    }

    public function demoFinal(): void {
        // التصميم النهائي (مطابق لهوية لوحة التحكم)
        require_once VIEWS_PATH . '/public/home_demo_final.php';
    }
}

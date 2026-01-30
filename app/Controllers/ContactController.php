<?php
/**
 * وحدة تحكم صفحة اتصل بنا
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Controllers;

class ContactController extends BaseController {
    
    /**
     * صفحة اتصل بنا
     */
    public function index(): void {
        $page = $this->db->fetchOne("SELECT * FROM pages WHERE slug = 'contact' AND is_active = 1");
        
        $sections = $this->db->fetchAll(
            "SELECT * FROM page_sections 
             WHERE page_id = :page_id AND is_active = 1 
             ORDER BY sort_order",
            ['page_id' => $page['id'] ?? 0]
        );
        
        // Handle product inquiry from product page
        $productInquiry = '';
        $productSlug = $_GET['product'] ?? null;
        $inquiryParam = $_GET['inquiry'] ?? null;
        
        if ($productSlug) {
            // Product inquiry
            $productData = $this->db->fetchOne(
                "SELECT name, model FROM products WHERE slug = :slug AND is_active = 1",
                ['slug' => $productSlug]
            );
            if ($productData) {
                $productName = $productData['name'];
                $productModel = $productData['model'] ?? '';
                $productInquiry = "السلام عليكم،\n\nأرغب في الاستفسار عن المنتج التالي:\n• {$productName}" . ($productModel ? " ({$productModel})" : "") . "\n\nأرجو التواصل معي لمعرفة السعر والتفاصيل.\n\nشكراً لكم";
            }
        } elseif ($inquiryParam) {
            // Spare part inquiry (format: "PartNumber - PartName")
            $productInquiry = "السلام عليكم،\n\nأرغب في الاستفسار عن قطعة الغيار التالية:\n• {$inquiryParam}\n\nأرجو التواصل معي لمعرفة السعر والتوفر.\n\nشكراً لكم";
        }
        
        $this->view('public.contact', [
            'title' => $page['meta_title'] ?? 'اتصل بنا',
            'meta_description' => $page['meta_description'] ?? '',
            'sections' => $sections,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
            'productInquiry' => $productInquiry,
        ]);
    }
    
    /**
     * إرسال رسالة
     */
    public function send(): void {
        if (!$this->isMethod('POST') || !$this->validateCsrf()) {
            $this->redirect('/contact', ['error' => 'طلب غير صالح']);
            return;
        }
        
        $data = [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'subject' => $this->input('subject'),
            'message' => $this->input('message'),
        ];
        
        // التحقق من البيانات
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'الاسم مطلوب';
        }
        
        if (empty($data['message'])) {
            $errors[] = 'الرسالة مطلوبة';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'البريد الإلكتروني غير صالح';
        }
        
        if (!empty($errors)) {
            $this->redirect('/contact', ['error' => implode('<br>', $errors)]);
            return;
        }
        
        try {
            $this->db->insert('contact_messages', $data);
            $this->redirect('/contact', ['success' => 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.']);
        } catch (\Exception $e) {
            $this->redirect('/contact', ['error' => 'حدث خطأ أثناء إرسال الرسالة']);
        }
    }
    public function previewBento(): void {
        $this->view('public.contact_designs.bento', ['title' => 'معاينة: نظام الشبكة']);
    }

    public function previewVertical(): void {
        $this->view('public.contact_designs.vertical', ['title' => 'معاينة: الفخامة الرأسية']);
    }

    public function previewSplitMap(): void {
        $this->view('public.contact_designs.split_map', ['title' => 'معاينة: الخريطة العائمة']);
    }

    public function previewGlassMap(): void {
        $this->view('public.contact_designs.glass_map', ['title' => 'معاينة: الخريطة الكاملة']);
    }

    public function previewEstateMap(): void {
        $this->view('public.contact_designs.estate_map', ['title' => 'معاينة: الانقسام الرأسي']);
    }

    public function previewCinematic(): void {
        $this->view('public.contact_designs.cinematic', ['title' => 'معاينة: الاستوديو السينمائي']);
    }

    public function previewCommercial(): void {
        $this->view('public.contact_designs.commercial', ['title' => 'معاينة: الكلاسيكي الرسمي']);
    }

    public function previewGold(): void {
        $this->view('public.contact_designs.gold_premium', ['title' => 'معاينة: الهوية الذهبية']);
    }

    public function previewMaterial(): void {
        $this->view('public.contact_designs.material', ['title' => 'معاينة: بطاقات حديثة']);
    }
}

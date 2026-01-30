<?php

namespace App\Controllers;

use App\Services\Settings;

class ContactPreviewController {
    
    public function index() {
        // Fetch company info just like the real contact page
        $settings = Settings::getCompanyInfo();
        $success = ''; // Dummy variable for preview
        
        include VIEWS_PATH . '/public/contact_preview.php';
    }
}

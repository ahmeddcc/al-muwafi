<?php
/**
 * خدمة معالجة الصور
 * نظام المُوَفِّي لخدمات ريكو
 */

namespace App\Services;

class ImageProcessor {
    private string $uploadsPath;
    private array $allowedTypes;
    private int $maxSize;
    private ?string $watermarkPath = null;
    
    /**
     * البناء
     */
    public function __construct() {
        $this->uploadsPath = defined('UPLOADS_PATH') ? UPLOADS_PATH : dirname(__DIR__, 2) . '/storage/uploads';
        $this->allowedTypes = defined('ALLOWED_IMAGE_TYPES') ? ALLOWED_IMAGE_TYPES : ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $this->maxSize = defined('MAX_UPLOAD_SIZE') ? MAX_UPLOAD_SIZE : 10 * 1024 * 1024;
        
        // إنشاء مجلد الرفع إذا لم يكن موجوداً
        if (!is_dir($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
        
        // تحميل مسار العلامة المائية
        $this->loadWatermarkPath();
    }
    
    /**
     * تحميل مسار العلامة المائية
     */
    private function loadWatermarkPath(): void {
        try {
            $db = Database::getInstance();
            $logo = $db->fetchColumn(
                "SELECT setting_value FROM settings WHERE setting_key = 'company_logo'"
            );
            
            if ($logo && file_exists($this->uploadsPath . '/' . $logo)) {
                $this->watermarkPath = $this->uploadsPath . '/' . $logo;
            }
        } catch (\Exception $e) {
            // تجاهل
        }
    }
    
    /**
     * رفع صورة
     */
    public function upload(array $file, string $subfolder = 'images'): array {
        // التحقق من الأخطاء
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => $this->getUploadError($file['error'])];
        }
        
        // التحقق من الحجم
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'حجم الملف يتجاوز الحد المسموح'];
        }
        
        // التحقق من النوع
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'نوع الملف غير مسموح'];
        }
        
        // التحقق من أنها صورة فعلية (باستثناء SVG)
        if ($extension !== 'svg') {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['success' => false, 'error' => 'الملف ليس صورة صالحة'];
            }
        }
        
        // إنشاء المجلد الفرعي
        $targetDir = $this->uploadsPath . '/' . $subfolder;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // توليد اسم فريد
        $newName = $this->generateFileName($extension);
        $targetPath = $targetDir . '/' . $newName;
        
        // التأكد من وجود المجلد الكامل (بما فيه مجلدات التاريخ)
        $fullDir = dirname($targetPath);
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        
        // نقل الملف
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'error' => 'فشل في حفظ الملف'];
        }
        
        // معالجة الصورة التلقائية للمنتجات - تم نقلها إلى uploadWithProcessing
        // لا نعالج هنا لتجنب التكرار
        
        return [
            'success' => true,
            'filename' => $newName,
            'path' => $subfolder . '/' . $newName,
            'full_path' => $targetPath,
            'size' => $file['size'],
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
        ];
    }
    
    /**
     * رفع صورة مع معالجة متقدمة
     */
    public function uploadWithProcessing(array $file, string $subfolder = 'products', array $options = []): array {
        $result = $this->upload($file, $subfolder);
        
        if (!$result['success']) {
            return $result;
        }
        
        $targetPath = $result['full_path'];
        
        // اقتصاص مخصص (من المستخدم)
        if (isset($options['crop']) && extension_loaded('gd')) {
            $crop = $options['crop'];
            $this->crop($targetPath, $crop['x'], $crop['y'], $crop['width'], $crop['height']);
        }
        
        // تغيير الحجم
        if (isset($options['resize']) && extension_loaded('gd')) {
            $this->resize($targetPath, $options['resize']['width'], $options['resize']['height']);
        }
        
        // تحسين جودة الصورة تلقائياً (دائماً مفعل)
        if (extension_loaded('gd')) {
            $this->autoEnhance($targetPath);
        }
        
        // إزالة الخلفية البيضاء (محسنة) - تحويل إلى PNG (skip for SVG)
        $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        if ($ext !== 'svg' && isset($options['remove_background']) && $options['remove_background'] && extension_loaded('gd')) {
            $newPath = $this->removeWhiteBackground($targetPath);
            
            // تحديث المسارات إذا تغير الملف
            if ($newPath && $newPath !== $targetPath) {
                $targetPath = $newPath;
                // تحديث المسار النسبي في النتيجة
                $result['full_path'] = $newPath;
                $result['path'] = str_replace(UPLOADS_PATH . '/', '', $newPath);
                $result['path'] = str_replace(UPLOADS_PATH . DIRECTORY_SEPARATOR, '', $result['path']);
                $result['filename'] = basename($newPath);
            }
        }
        
        // ملاحظة: العلامة المائية تُطبق ديناميكياً عند عرض الصور عبر storage_proxy.php
        // هذا يسمح بتغيير إعدادات العلامة المائية فوراً على جميع الصور
        
        return $result;
    }
    
    /**
     * إزالة الخلفية البيضاء (محسّنة)
     * تستخدم خوارزمية ذكية للكشف عن الحواف والخلفية
     * 
     * @param string $imagePath مسار الصورة
     * @param bool $convertToPng تحويل إلى PNG (للحفاظ على الشفافية)
     * @return string|false المسار الجديد أو false عند الفشل
     */
    public function removeWhiteBackground(string $imagePath, bool $convertToPng = true): string|false {
        if (!file_exists($imagePath) || !extension_loaded('gd')) {
            return false;
        }
        
        $image = $this->loadImage($imagePath);
        if (!$image) {
            return false;
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        // التحقق من أن الصورة تحتاج لإزالة الخلفية فعلاً
        // عن طريق فحص الزوايا الأربع
        $corners = [
            imagecolorat($image, 0, 0),
            imagecolorat($image, $width - 1, 0),
            imagecolorat($image, 0, $height - 1),
            imagecolorat($image, $width - 1, $height - 1),
        ];
        
        $whiteCorners = 0;
        foreach ($corners as $color) {
            $r = ($color >> 16) & 0xFF;
            $g = ($color >> 8) & 0xFF;
            $b = $color & 0xFF;
            if ($r > 240 && $g > 240 && $b > 240) {
                $whiteCorners++;
            }
        }
        
        // إذا كانت أقل من 3 زوايا بيضاء، لا نحتاج لإزالة الخلفية
        if ($whiteCorners < 3) {
            imagedestroy($image);
            return $imagePath; // إرجاع المسار الأصلي بدون تعديل
        }
        
        // إنشاء صورة جديدة بشفافية
        $newImage = imagecreatetruecolor($width, $height);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        
        // لون شفاف
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefill($newImage, 0, 0, $transparent);
        
        // معالجة Flood Fill من الحواف
        // بدلاً من كل بكسل، نستخدم خوارزمية أذكى
        $visited = [];
        $threshold = 235; // حد اللون الأبيض (قابل للتعديل)
        
        // دالة للتحقق من أن اللون أبيض/رمادي فاتح
        $isWhiteish = function($color) use ($threshold) {
            $r = ($color >> 16) & 0xFF;
            $g = ($color >> 8) & 0xFF;
            $b = $color & 0xFF;
            // التحقق من أن اللون فاتح وموحد (أبيض/رمادي)
            return $r > $threshold && $g > $threshold && $b > $threshold && abs($r - $g) < 15 && abs($g - $b) < 15;
        };
        
        // نسخ الصورة الأصلية أولاً
        imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);
        
        // Flood fill من الزوايا الأربع
        $queue = [[0, 0], [$width - 1, 0], [0, $height - 1], [$width - 1, $height - 1]];
        
        while (!empty($queue)) {
            [$x, $y] = array_shift($queue);
            
            // تخطي إذا خارج الحدود
            if ($x < 0 || $x >= $width || $y < 0 || $y >= $height) continue;
            
            // تخطي إذا تمت زيارته
            $key = "$x,$y";
            if (isset($visited[$key])) continue;
            $visited[$key] = true;
            
            // التحقق من اللون
            $color = imagecolorat($image, $x, $y);
            if (!$isWhiteish($color)) continue;
            
            // جعل هذا البكسل شفافاً
            imagesetpixel($newImage, $x, $y, $transparent);
            
            // إضافة الجيران (4-connected)
            $queue[] = [$x + 1, $y];
            $queue[] = [$x - 1, $y];
            $queue[] = [$x, $y + 1];
            $queue[] = [$x, $y - 1];
        }
        
        // تحديد مسار الحفظ
        if ($convertToPng) {
            $newPath = preg_replace('/\.(jpg|jpeg|gif|webp)$/i', '.png', $imagePath);
            $result = imagepng($newImage, $newPath, 9);
        } else {
            // الحفظ بنفس التنسيق (لن يحفظ الشفافية في JPG)
            $newPath = $imagePath;
            $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $result = match($ext) {
                'png' => imagepng($newImage, $newPath, 9),
                'gif' => imagegif($newImage, $newPath),
                'webp' => imagewebp($newImage, $newPath, 90),
                default => imagejpeg($newImage, $newPath, 90),
            };
        }
        
        imagedestroy($image);
        imagedestroy($newImage);
        
        // حذف الملف الأصلي فقط إذا تم إنشاء ملف جديد مختلف
        if ($result && $newPath !== $imagePath && file_exists($newPath)) {
            @unlink($imagePath);
        }
        
        return $result ? $newPath : false;
    }
    
    /**
     * رفع صور متعددة
     */
    public function uploadMultiple(array $files, string $subfolder = 'images'): array {
        $results = [];
        
        // إعادة هيكلة المصفوفة
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
            
            $results[] = $this->upload($file, $subfolder);
        }
        
        return $results;
    }
    
    /**
     * تغيير حجم الصورة
     */
    public function resize(string $imagePath, int $maxWidth, int $maxHeight, bool $keepAspect = true): bool {
        if (!file_exists($imagePath)) {
            return false;
        }

        // Skip SVG
        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) === 'svg') {
            return true;
        }
        
        $image = $this->loadImage($imagePath);
        if (!$image) {
            return false;
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($keepAspect) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int) ($width * $ratio);
            $newHeight = (int) ($height * $ratio);
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxHeight;
        }
        
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // الحفاظ على الشفافية للـ PNG
        $this->preserveTransparency($newImage, $imagePath);
        
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        $result = $this->saveImage($newImage, $imagePath);
        
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $result;
    }
    
    /**
     * تحسين جودة الصورة تلقائياً
     * يشمل: السطوع، التباين، الحدة
     */
    /**
     * تحسين جودة الصورة تلقائياً
     * يشمل: السطوع، التباين، الحدة
     */
    public function autoEnhance(string $imagePath): bool {
        if (!file_exists($imagePath) || !extension_loaded('gd')) {
            return false;
        }
        
        // Skip enhancement for transparent formats (PNG, WEBP, GIF, SVG)
        // Sharpening (convolution) destroys alpha channel in GD
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'webp', 'gif', 'svg'])) {
            return true; // Return true as if successful, but do nothing
        }
        
        $image = $this->loadImage($imagePath);
        if (!$image) {
            return false;
        }
        
        // 1. تحسين التباين (Contrast) - قيمة خفيفة
        imagefilter($image, IMG_FILTER_CONTRAST, -5);
        
        // 2. تحسين السطوع (Brightness) - قيمة خفيفة
        imagefilter($image, IMG_FILTER_BRIGHTNESS, 3);
        
        // 3. زيادة الحدة (Sharpening) باستخدام Convolution Matrix
        $sharpenMatrix = [
            [-1, -1, -1],
            [-1, 16, -1],
            [-1, -1, -1]
        ];
        $divisor = array_sum(array_map('array_sum', $sharpenMatrix));
        if ($divisor == 0) $divisor = 1;
        
        imageconvolution($image, $sharpenMatrix, $divisor, 0);
        
        // حفظ الصورة المحسنة
        $result = $this->saveImage($image, $imagePath);
        imagedestroy($image);
        
        return $result;
    }
    
    /**
     * قص الصورة
     */
    public function crop(string $imagePath, int $x, int $y, int $width, int $height): bool {
        if (!file_exists($imagePath)) {
            return false;
        }

        // Skip SVG
        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) === 'svg') {
            return true;
        }
        
        $image = $this->loadImage($imagePath);
        if (!$image) {
            return false;
        }
        
        $newImage = imagecreatetruecolor($width, $height);
        $this->preserveTransparency($newImage, $imagePath);
        
        imagecopy($newImage, $image, 0, 0, $x, $y, $width, $height);
        
        $result = $this->saveImage($newImage, $imagePath);
        
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $result;
    }
    
    /**
     * إضافة علامة مائية (لوجو أو نص)
     */
    public function addWatermark(string $imagePath, ?string $watermarkPath = null, string $position = 'bottom-right', int $opacity = 50): bool {
        if (!file_exists($imagePath) || !extension_loaded('gd')) {
            return false;
        }

        // Skip SVG
        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) === 'svg') {
            return true;
        }
        
        $watermarkPath = $watermarkPath ?? $this->watermarkPath;
        
        // إذا كان اللوجو موجوداً، استخدمه
        if ($watermarkPath && file_exists($watermarkPath)) {
            return $this->addImageWatermark($imagePath, $watermarkPath, $position, $opacity);
        }
        
        // وإلا استخدم اسم الموقع كعلامة مائية نصية
        return $this->addTextWatermark($imagePath, $position, $opacity);
    }
    
    /**
     * إضافة علامة مائية صورية (شفافة وكبيرة)
     * @param int $opacity نسبة الشفافية (0-100، حيث 100 = شفاف تماماً)
     */
    private function addImageWatermark(string $imagePath, string $watermarkPath, string $position, int $opacity = 50): bool {
        $image = $this->loadImage($imagePath);
        $watermark = $this->loadImage($watermarkPath);
        
        if (!$image || !$watermark) {
            return false;
        }
        
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $wmWidth = imagesx($watermark);
        $wmHeight = imagesy($watermark);
        
        // تغيير حجم العلامة المائية لتكون كبيرة (40% من حجم الصورة)
        $maxWmSize = min($imageWidth, $imageHeight) * 0.4;
        $ratio = $maxWmSize / max($wmWidth, $wmHeight);
        $newWmWidth = (int) ($wmWidth * $ratio);
        $newWmHeight = (int) ($wmHeight * $ratio);
        
        // إنشاء صورة جديدة للعلامة المائية المصغرة
        $resizedWatermark = imagecreatetruecolor($newWmWidth, $newWmHeight);
        imagealphablending($resizedWatermark, false);
        imagesavealpha($resizedWatermark, true);
        $transparent = imagecolorallocatealpha($resizedWatermark, 0, 0, 0, 127);
        imagefill($resizedWatermark, 0, 0, $transparent);
        imagecopyresampled($resizedWatermark, $watermark, 0, 0, 0, 0, $newWmWidth, $newWmHeight, $wmWidth, $wmHeight);
        
        imagedestroy($watermark);
        $wmWidth = $newWmWidth;
        $wmHeight = $newWmHeight;
        
        // تحديد الموقع
        $padding = 20;
        [$x, $y] = match($position) {
            'top-left' => [$padding, $padding],
            'top-right' => [$imageWidth - $wmWidth - $padding, $padding],
            'bottom-left' => [$padding, $imageHeight - $wmHeight - $padding],
            'center' => [($imageWidth - $wmWidth) / 2, ($imageHeight - $wmHeight) / 2],
            default => [$imageWidth - $wmWidth - $padding, $imageHeight - $wmHeight - $padding],
        };
        
        // دمج العلامة المائية مع الشفافية المحددة
        
        // استخدام imagecopymerge مع شفافية
        // لدعم PNG مع شفافية، نستخدم طريقة بديلة
        imagealphablending($image, true);
        
        // تطبيق الشفافية على العلامة المائية
        for ($px = 0; $px < $wmWidth; $px++) {
            for ($py = 0; $py < $wmHeight; $py++) {
                $srcColor = imagecolorat($resizedWatermark, $px, $py);
                $srcAlpha = ($srcColor >> 24) & 0x7F;
                
                // تخطي البكسلات الشفافة تماماً
                if ($srcAlpha >= 127) continue;
                
                // حساب الشفافية الجديدة
                $newAlpha = min(127, $srcAlpha + (int)(127 * ($opacity / 100)));
                
                $r = ($srcColor >> 16) & 0xFF;
                $g = ($srcColor >> 8) & 0xFF;
                $b = $srcColor & 0xFF;
                
                $newColor = imagecolorallocatealpha($image, $r, $g, $b, $newAlpha);
                imagesetpixel($image, (int)$x + $px, (int)$y + $py, $newColor);
            }
        }
        
        $result = $this->saveImage($image, $imagePath);
        
        imagedestroy($image);
        imagedestroy($resizedWatermark);
        
        return $result;
    }
    
    /**
     * إضافة علامة مائية نصية
     * @param int $opacity نسبة الشفافية (0-100)
     */
    private function addTextWatermark(string $imagePath, string $position, int $opacity = 50): bool {
        // جلب اسم الموقع من الإعدادات
        $siteName = 'المُوَفِّي'; // قيمة افتراضية
        try {
            $db = Database::getInstance();
            $name = $db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'company_name'");
            if ($name) {
                $siteName = $name;
            }
        } catch (\Exception $e) {
            // استخدام القيمة الافتراضية
        }
        
        $image = $this->loadImage($imagePath);
        if (!$image) {
            return false;
        }
        
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        
        // حجم الخط بناءً على حجم الصورة
        $fontSize = max(12, min($imageWidth, $imageHeight) * 0.04);
        
        // حساب قيمة الشفافية (0-127 حيث 127 = شفاف تماماً)
        $alphaValue = (int)(127 * ($opacity / 100));
        
        // لون النص (أبيض مع شفافية)
        $textColor = imagecolorallocatealpha($image, 255, 255, 255, $alphaValue);
        $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, min(127, $alphaValue + 30));
        
        // البحث عن خط عربي أو استخدام الافتراضي
        $fontPath = $this->findArabicFont();
        
        // حساب أبعاد النص
        if ($fontPath && function_exists('imagettftext')) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $siteName);
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[7] - $bbox[1]);
        } else {
            $textWidth = strlen($siteName) * ($fontSize * 0.6);
            $textHeight = $fontSize;
        }
        
        // تحديد الموقع
        $padding = 15;
        [$x, $y] = match($position) {
            'top-left' => [$padding, $padding + $textHeight],
            'top-right' => [$imageWidth - $textWidth - $padding, $padding + $textHeight],
            'bottom-left' => [$padding, $imageHeight - $padding],
            'center' => [($imageWidth - $textWidth) / 2, ($imageHeight + $textHeight) / 2],
            default => [$imageWidth - $textWidth - $padding, $imageHeight - $padding],
        };
        
        // رسم النص
        if ($fontPath && function_exists('imagettftext')) {
            // ظل النص
            imagettftext($image, $fontSize, 0, (int)$x + 2, (int)$y + 2, $shadowColor, $fontPath, $siteName);
            // النص الرئيسي
            imagettftext($image, $fontSize, 0, (int)$x, (int)$y, $textColor, $fontPath, $siteName);
        } else {
            // استخدام الخط الافتراضي
            imagestring($image, 5, (int)$x, (int)$y - $textHeight, $siteName, $textColor);
        }
        
        $result = $this->saveImage($image, $imagePath);
        imagedestroy($image);
        
        return $result;
    }
    
    /**
     * البحث عن خط عربي متاح
     */
    private function findArabicFont(): ?string {
        $possibleFonts = [
            dirname(__DIR__, 2) . '/public/assets/fonts/Cairo-Regular.ttf',
            dirname(__DIR__, 2) . '/public/assets/fonts/Tajawal-Regular.ttf',
            dirname(__DIR__, 2) . '/public/assets/fonts/NotoSansArabic-Regular.ttf',
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        return null;
    }
    
    /**
     * إنشاء صورة مصغرة
     */
    public function createThumbnail(string $imagePath, int $width = 300, int $height = 300): ?string {
        if (!file_exists($imagePath)) {
            return null;
        }
        
        $pathInfo = pathinfo($imagePath);
        $thumbPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        
        // نسخ الملف
        if (!copy($imagePath, $thumbPath)) {
            return null;
        }
        
        // تغيير الحجم
        if (!$this->resize($thumbPath, $width, $height)) {
            unlink($thumbPath);
            return null;
        }
        
        return $thumbPath;
    }
    
    /**
     * حذف صورة
     */
    public function delete(string $path): bool {
        if (empty($path)) {
            return false;
        }
        
        // تحويل الفواصل لتتوافق مع نظام التشغيل
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        
        // تحديد المسار الكامل
        $fullPath = strpos($path, $this->uploadsPath) === 0 
            ? $path 
            : $this->uploadsPath . DIRECTORY_SEPARATOR . $path;
        
        $deleted = false;
        
        // حذف الملف الأصلي
        if (file_exists($fullPath)) {
            $deleted = @unlink($fullPath);
        }
        
        // حذف نسخة PNG إذا كانت موجودة (في حالة تحويل الخلفية)
        $pngPath = preg_replace('/\.(jpg|jpeg)$/i', '.png', $fullPath);
        if ($pngPath !== $fullPath && file_exists($pngPath)) {
            @unlink($pngPath);
            $deleted = true;
        }
        
        // حذف نسخة JPG إذا كان الملف المحذوف PNG
        $jpgPath = preg_replace('/\.png$/i', '.jpg', $fullPath);
        if ($jpgPath !== $fullPath && file_exists($jpgPath)) {
            @unlink($jpgPath);
            $deleted = true;
        }
        
        return $deleted;
    }
    
    /**
     * توليد اسم ملف فريد
     */
    private function generateFileName(string $extension): string {
        return date('Y/m/') . uniqid('img_', true) . '.' . $extension;
    }
    
    /**
     * تحميل صورة
     */
    private function loadImage(string $path): ?\GdImage {
        $info = @getimagesize($path);
        if (!$info) {
            return null;
        }
        
        return match($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };
    }
    
    /**
     * حفظ صورة
     */
    private function saveImage(\GdImage $image, string $path): bool {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        // Ensure transparency is saved for supporting formats
        if (in_array($extension, ['png', 'webp', 'gif'])) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
        
        return match($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, 90),
            'png' => imagepng($image, $path, 9),
            'gif' => imagegif($image, $path),
            'webp' => imagewebp($image, $path, 90),
            default => false,
        };
    }

    /**
     * الحفاظ على الشفافية
     */
    private function preserveTransparency(\GdImage $image, string $originalPath): void {
        $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
        
        if (in_array($extension, ['png', 'gif', 'webp'])) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        }
    }
    
    /**
     * الحصول على رسالة خطأ الرفع
     */
    private function getUploadError(int $error): string {
        return match($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'حجم الملف كبير جداً',
            UPLOAD_ERR_PARTIAL => 'تم رفع الملف جزئياً فقط',
            UPLOAD_ERR_NO_FILE => 'لم يتم اختيار ملف',
            UPLOAD_ERR_NO_TMP_DIR => 'مجلد مؤقت غير موجود',
            UPLOAD_ERR_CANT_WRITE => 'فشل في كتابة الملف',
            default => 'خطأ غير معروف',
        };
    }
}

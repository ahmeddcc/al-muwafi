<?php
/**
 * وكيل لعرض الصور من مجلد التخزين
 * يطبق العلامة المائية ديناميكياً على صور المنتجات
 */

// تسجيل الأخطاء للتشخيص
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// تعريف المسار الجذري
define('APP_PATH', __DIR__);

// تحميل الإعدادات وقاعدة البيانات
require_once APP_PATH . '/config/config.php';

// تحميل autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Services\Settings;

// استقبال المسار من الرابط
$path = $_GET['path'] ?? '';

// تنظيف المسار
$path = str_replace(['..', '//'], ['', '/'], $path);
$path = urldecode($path);

// تحويل الفواصل لتتوافق مع نظام Windows
$path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

$fullPath = APP_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $path;

// التحقق من وجود الملف
if (empty($path) || !file_exists($fullPath) || !is_file($fullPath)) {
    header("HTTP/1.0 404 Not Found");
    header('Content-Type: image/png');
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
    exit;
}

// تحديد نوع الملف
$extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml',
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedType = finfo_file($finfo, $fullPath);
    finfo_close($finfo);
    if ($detectedType) $mimeType = $detectedType;
}

// السماح فقط بالصور
if (strpos($mimeType, 'image/') !== 0) {
    header("HTTP/1.0 403 Forbidden");
    exit('Access denied');
}

// التحقق إذا كانت صورة منتج (تحتاج علامة مائية)
$isProductImage = (strpos($path, 'products') !== false);

// قراءة إعدادات العلامة المائية
$imageSettings = Settings::getGroup('images');
$watermarkEnabled = ($imageSettings['watermark_enabled'] ?? '0') === '1';

// تطبيق العلامة المائية على صور المنتجات فقط
if ($isProductImage && $watermarkEnabled && $extension !== 'svg') {
    $opacityMode = $imageSettings['watermark_opacity_mode'] ?? 'auto';
    $opacity = ($opacityMode === 'manual') 
        ? (int)($imageSettings['watermark_opacity'] ?? 50) 
        : 50;
    $position = $imageSettings['watermark_position'] ?? 'bottom-right';
    
    // البحث عن ملف العلامة المائية
    $watermarkPath = APP_PATH . '/storage/uploads/watermark/logo.png';
    if (!file_exists($watermarkPath)) {
        $watermarkPath = APP_PATH . '/public/assets/images/logo.png';
    }
    
    if (file_exists($watermarkPath)) {
        // تطبيق العلامة المائية
        $outputImage = applyWatermark($fullPath, $watermarkPath, $position, $opacity);
        
        if ($outputImage) {
            header('Content-Type: ' . $mimeType);
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header('Access-Control-Allow-Origin: *');
            
            // إخراج الصورة مع العلامة المائية
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($outputImage, null, 90);
                    break;
                case 'png':
                    imagepng($outputImage, null, 8);
                    break;
                case 'gif':
                    imagegif($outputImage);
                    break;
                case 'webp':
                    imagewebp($outputImage, null, 90);
                    break;
            }
            
            imagedestroy($outputImage);
            exit;
        }
    }
}

// إذا لم يتم تطبيق علامة مائية، عرض الصورة الأصلية
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: public, max-age=86400');
header('Access-Control-Allow-Origin: *');
readfile($fullPath);
exit;

/**
 * تطبيق العلامة المائية على الصورة
 */
function applyWatermark($imagePath, $watermarkPath, $position, $opacity) {
    // تحميل الصورة الأصلية
    $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($imagePath);
            break;
        case 'png':
            $image = @imagecreatefrompng($imagePath);
            break;
        case 'gif':
            $image = @imagecreatefromgif($imagePath);
            break;
        case 'webp':
            $image = @imagecreatefromwebp($imagePath);
            break;
        default:
            return null;
    }
    
    if (!$image) {
        return null;
    }
    
    // تحميل العلامة المائية
    $wmExtension = strtolower(pathinfo($watermarkPath, PATHINFO_EXTENSION));
    switch ($wmExtension) {
        case 'png':
            $watermark = @imagecreatefrompng($watermarkPath);
            break;
        case 'jpg':
        case 'jpeg':
            $watermark = @imagecreatefromjpeg($watermarkPath);
            break;
        default:
            $watermark = @imagecreatefrompng($watermarkPath);
    }
    
    if (!$watermark) {
        imagedestroy($image);
        return null;
    }
    
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $wmWidth = imagesx($watermark);
    $wmHeight = imagesy($watermark);
    
    // تغيير حجم العلامة المائية (40% من حجم الصورة)
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
    switch ($position) {
        case 'top-left':
            $x = $padding;
            $y = $padding;
            break;
        case 'top-right':
            $x = $imageWidth - $wmWidth - $padding;
            $y = $padding;
            break;
        case 'bottom-left':
            $x = $padding;
            $y = $imageHeight - $wmHeight - $padding;
            break;
        case 'center':
            $x = ($imageWidth - $wmWidth) / 2;
            $y = ($imageHeight - $wmHeight) / 2;
            break;
        default: // bottom-right
            $x = $imageWidth - $wmWidth - $padding;
            $y = $imageHeight - $wmHeight - $padding;
    }
    
    // دمج العلامة المائية مع الشفافية
    imagealphablending($image, true);
    
    for ($px = 0; $px < $wmWidth; $px++) {
        for ($py = 0; $py < $wmHeight; $py++) {
            $srcColor = imagecolorat($resizedWatermark, $px, $py);
            $srcAlpha = ($srcColor >> 24) & 0x7F;
            
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
    
    imagedestroy($resizedWatermark);
    
    return $image;
}

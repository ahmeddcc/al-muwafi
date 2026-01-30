<?php
/**
 * خدمة الصور من مجلد storage
 */

// تحديد المسار
$path = $_GET['path'] ?? '';

// وضع التشخيص (أزل هذا لاحقاً)
$debug = isset($_GET['debug']);

if (empty($path)) {
    http_response_code(400);
    exit('مسار غير صالح');
}

// تنظيف المسار
$path = str_replace(['../', '..\\', '..'], '', $path);
$path = ltrim($path, '/\\');

// المسار الكامل - المسار الصحيح (جذر المشروع/storage/uploads)
$storagePath = dirname(dirname(dirname(__DIR__))) . '/storage/uploads/';
$filePath = $storagePath . $path;

// تصحيح الفواصل لنظام Windows
$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

if ($debug) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Path requested: $path\n";
    echo "Storage path: $storagePath\n";
    echo "Full path: $filePath\n";
    echo "File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
    
    // عرض محتويات المجلد
    $dir = dirname($filePath);
    if (is_dir($dir)) {
        echo "Directory contents:\n";
        foreach (scandir($dir) as $f) {
            if ($f != '.' && $f != '..') echo "  - " . htmlspecialchars($f) . "\n";
        }
    }
    exit;
}

// التحقق من وجود الملف
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
        <rect fill="#1e293b" width="200" height="200"/>
        <text x="100" y="100" text-anchor="middle" fill="#64748b" font-size="14">لا توجد صورة</text>
    </svg>';
    exit;
}

// تحديد نوع المحتوى
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000');

readfile($filePath);
exit;

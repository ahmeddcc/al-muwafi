-- =============================================================================
-- نظام المُوَفِّي لخدمات ريكو
-- قاعدة البيانات الموحدة
-- الإصدار: 1.0.0
-- =============================================================================

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `al_muwafi_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `al_muwafi_db`;

-- =============================================================================
-- جداول النظام الأساسية
-- =============================================================================

-- جدول الأدوار
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE COMMENT 'اسم الدور',
    `name_ar` VARCHAR(100) NOT NULL COMMENT 'الاسم بالعربية',
    `description` TEXT NULL COMMENT 'وصف الدور',
    `is_system` TINYINT(1) DEFAULT 0 COMMENT 'دور نظام (لا يمكن حذفه)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الصلاحيات
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE COMMENT 'اسم الصلاحية',
    `name_ar` VARCHAR(100) NOT NULL COMMENT 'الاسم بالعربية',
    `group_name` VARCHAR(100) NOT NULL COMMENT 'مجموعة الصلاحية',
    `description` TEXT NULL COMMENT 'وصف الصلاحية',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_permissions_name` (`name`),
    INDEX `idx_permissions_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول علاقة الأدوار بالصلاحيات
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE COMMENT 'اسم المستخدم',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'البريد الإلكتروني',
    `password` VARCHAR(255) NOT NULL COMMENT 'كلمة المرور المشفرة',
    `full_name` VARCHAR(255) NOT NULL COMMENT 'الاسم الكامل',
    `phone` VARCHAR(20) NULL COMMENT 'رقم الهاتف',
    `avatar` VARCHAR(255) NULL COMMENT 'صورة المستخدم',
    `role_id` INT UNSIGNED NOT NULL COMMENT 'الدور',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة الحساب',
    `last_login` TIMESTAMP NULL COMMENT 'آخر تسجيل دخول',
    `telegram_chat_id` VARCHAR(100) NULL COMMENT 'معرف Telegram',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT,
    INDEX `idx_users_username` (`username`),
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول سجل نشاط المستخدمين
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL COMMENT 'معرف المستخدم',
    `action` VARCHAR(100) NOT NULL COMMENT 'نوع العملية',
    `description` TEXT NULL COMMENT 'تفاصيل العملية',
    `ip_address` VARCHAR(45) NULL COMMENT 'عنوان IP',
    `user_agent` TEXT NULL COMMENT 'معلومات المتصفح',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_activity_user` (`user_id`),
    INDEX `idx_activity_action` (`action`),
    INDEX `idx_activity_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- جداول المحتوى
-- =============================================================================

-- جدول الأقسام
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'اسم القسم',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'الرابط المختصر',
    `type` ENUM('copier', 'printer', 'spare_part', 'service') NOT NULL COMMENT 'نوع القسم',
    `description` TEXT NULL COMMENT 'وصف القسم',
    `image` VARCHAR(255) NULL COMMENT 'صورة القسم',
    `parent_id` INT UNSIGNED NULL COMMENT 'القسم الأب',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة القسم',
    `meta_title` VARCHAR(255) NULL COMMENT 'عنوان SEO',
    `meta_description` TEXT NULL COMMENT 'وصف SEO',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_categories_type` (`type`),
    INDEX `idx_categories_active` (`is_active`),
    INDEX `idx_categories_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المنتجات
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'اسم المنتج',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'الرابط المختصر',
    `product_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'كود المنتج',
    `barcode` VARCHAR(100) NULL UNIQUE COMMENT 'الباركود',
    `model` VARCHAR(255) NULL COMMENT 'الموديل',
    `category_id` INT UNSIGNED NOT NULL COMMENT 'القسم',
    `description` TEXT NULL COMMENT 'الوصف المهني',
    `raw_specs` TEXT NULL COMMENT 'المواصفات الخام',
    `thumbnail` VARCHAR(255) NULL COMMENT 'الصورة المصغرة',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة المنتج',
    `show_faults` TINYINT(1) DEFAULT 0 COMMENT 'إظهار الأعطال للعملاء',
    `show_spare_parts` TINYINT(1) DEFAULT 0 COMMENT 'إظهار قطع الغيار للعملاء',
    `meta_title` VARCHAR(255) NULL COMMENT 'عنوان SEO',
    `meta_description` TEXT NULL COMMENT 'وصف SEO',
    `views_count` INT UNSIGNED DEFAULT 0 COMMENT 'عدد المشاهدات',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_active` (`is_active`),
    INDEX `idx_products_sort` (`sort_order`),
    INDEX `idx_products_code` (`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول صور المنتجات
CREATE TABLE IF NOT EXISTS `product_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL COMMENT 'المنتج',
    `image_path` VARCHAR(255) NOT NULL COMMENT 'مسار الصورة',
    `image_path_processed` VARCHAR(255) NULL COMMENT 'مسار الصورة المعالجة',
    `alt_text` VARCHAR(255) NULL COMMENT 'النص البديل',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'صورة رئيسية',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_product_images_product` (`product_id`),
    INDEX `idx_product_images_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الأعطال الشائعة
CREATE TABLE IF NOT EXISTS `product_faults` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL COMMENT 'المنتج',
    `fault_code` VARCHAR(50) NULL COMMENT 'كود العطل',
    `fault_name` VARCHAR(255) NOT NULL COMMENT 'اسم العطل',
    `description` TEXT NULL COMMENT 'وصف العطل',
    `solution` TEXT NULL COMMENT 'الحل',
    `is_common` TINYINT(1) DEFAULT 0 COMMENT 'عطل شائع',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_faults_product` (`product_id`),
    INDEX `idx_faults_code` (`fault_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول قطع الغيار
CREATE TABLE IF NOT EXISTS `spare_parts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'اسم القطعة',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'الرابط المختصر',
    `part_number` VARCHAR(100) NOT NULL UNIQUE COMMENT 'رقم القطعة',
    `category_id` INT UNSIGNED NULL COMMENT 'القسم',
    `description` TEXT NULL COMMENT 'الوصف',
    `image` VARCHAR(255) NULL COMMENT 'الصورة',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة القطعة',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_spare_parts_category` (`category_id`),
    INDEX `idx_spare_parts_active` (`is_active`),
    INDEX `idx_spare_parts_number` (`part_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول علاقة المنتجات بقطع الغيار
CREATE TABLE IF NOT EXISTS `product_spare_parts` (
    `product_id` INT UNSIGNED NOT NULL,
    `spare_part_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `spare_part_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`spare_part_id`) REFERENCES `spare_parts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الخدمات
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'اسم الخدمة',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'الرابط المختصر',
    `short_description` VARCHAR(500) NULL COMMENT 'وصف مختصر',
    `description` TEXT NULL COMMENT 'الوصف الكامل',
    `icon` VARCHAR(100) NULL COMMENT 'أيقونة الخدمة',
    `image` VARCHAR(255) NULL COMMENT 'صورة الخدمة',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة الخدمة',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_services_active` (`is_active`),
    INDEX `idx_services_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

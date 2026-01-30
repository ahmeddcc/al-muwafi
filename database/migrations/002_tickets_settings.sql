-- =============================================================================
-- نظام المُوَفِّي لخدمات ريكو
-- جداول تذاكر الصيانة والإعدادات
-- =============================================================================

USE `al_muwafi_db`;

-- =============================================================================
-- جداول تذاكر الصيانة
-- =============================================================================

-- جدول تذاكر الصيانة
CREATE TABLE IF NOT EXISTS `maintenance_tickets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_number` VARCHAR(20) NOT NULL UNIQUE COMMENT 'رقم التذكرة',
    `customer_name` VARCHAR(255) NOT NULL COMMENT 'اسم العميل',
    `customer_phone` VARCHAR(20) NOT NULL COMMENT 'رقم الهاتف',
    `customer_address` TEXT NOT NULL COMMENT 'العنوان',
    `machine_type` ENUM('copier', 'printer') NOT NULL COMMENT 'نوع الجهاز',
    `machine_model` VARCHAR(255) NULL COMMENT 'موديل الجهاز',
    `machine_model_image` VARCHAR(255) NULL COMMENT 'صورة الموديل',
    `fault_description` TEXT NOT NULL COMMENT 'وصف العطل',
    `error_code` VARCHAR(50) NULL COMMENT 'كود الخطأ',
    `screen_image` VARCHAR(255) NULL COMMENT 'صورة شاشة الجهاز',
    `fault_type` ENUM('new', 'repeated') DEFAULT 'new' COMMENT 'نوع العطل',
    `repeat_count` INT UNSIGNED DEFAULT 0 COMMENT 'عدد التكرارات',
    `status` ENUM('new', 'received', 'in_progress', 'fixed', 'closed') DEFAULT 'new' COMMENT 'حالة التذكرة',
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium' COMMENT 'الأولوية',
    `assigned_to` INT UNSIGNED NULL COMMENT 'الفني المسؤول',
    `repair_report` VARCHAR(255) NULL COMMENT 'تقرير الإصلاح PDF',
    `repair_notes` TEXT NULL COMMENT 'ملاحظات الإصلاح',
    `closed_at` TIMESTAMP NULL COMMENT 'تاريخ الإغلاق',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_tickets_number` (`ticket_number`),
    INDEX `idx_tickets_status` (`status`),
    INDEX `idx_tickets_phone` (`customer_phone`),
    INDEX `idx_tickets_date` (`created_at`),
    INDEX `idx_tickets_assigned` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول سجل التذكرة (Timeline)
CREATE TABLE IF NOT EXISTS `ticket_timeline` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT UNSIGNED NOT NULL COMMENT 'التذكرة',
    `user_id` INT UNSIGNED NULL COMMENT 'المستخدم',
    `action` VARCHAR(100) NOT NULL COMMENT 'نوع الإجراء',
    `old_value` TEXT NULL COMMENT 'القيمة القديمة',
    `new_value` TEXT NULL COMMENT 'القيمة الجديدة',
    `notes` TEXT NULL COMMENT 'ملاحظات',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ticket_id`) REFERENCES `maintenance_tickets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_timeline_ticket` (`ticket_id`),
    INDEX `idx_timeline_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول مرفقات التذكرة
CREATE TABLE IF NOT EXISTS `ticket_attachments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT UNSIGNED NOT NULL COMMENT 'التذكرة',
    `file_name` VARCHAR(255) NOT NULL COMMENT 'اسم الملف',
    `file_path` VARCHAR(255) NOT NULL COMMENT 'مسار الملف',
    `file_type` VARCHAR(100) NULL COMMENT 'نوع الملف',
    `file_size` INT UNSIGNED NULL COMMENT 'حجم الملف',
    `uploaded_by` INT UNSIGNED NULL COMMENT 'رفع بواسطة',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ticket_id`) REFERENCES `maintenance_tickets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_attachments_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- جداول الإعدادات
-- =============================================================================

-- جدول الإعدادات العامة
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'مفتاح الإعداد',
    `setting_value` TEXT NULL COMMENT 'قيمة الإعداد',
    `setting_type` ENUM('text', 'textarea', 'number', 'boolean', 'json', 'image') DEFAULT 'text',
    `setting_group` VARCHAR(100) DEFAULT 'general' COMMENT 'مجموعة الإعداد',
    `description` VARCHAR(255) NULL COMMENT 'وصف الإعداد',
    `is_system` TINYINT(1) DEFAULT 0 COMMENT 'إعداد نظام',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`setting_key`),
    INDEX `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول صفحات الموقع
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE COMMENT 'معرف الصفحة',
    `title` VARCHAR(255) NOT NULL COMMENT 'عنوان الصفحة',
    `meta_title` VARCHAR(255) NULL COMMENT 'عنوان SEO',
    `meta_description` TEXT NULL COMMENT 'وصف SEO',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة الصفحة',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول أقسام الصفحات
CREATE TABLE IF NOT EXISTS `page_sections` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT UNSIGNED NOT NULL COMMENT 'الصفحة',
    `section_key` VARCHAR(100) NOT NULL COMMENT 'معرف القسم',
    `title` VARCHAR(255) NULL COMMENT 'عنوان القسم',
    `content` TEXT NULL COMMENT 'المحتوى',
    `image` VARCHAR(255) NULL COMMENT 'الصورة',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتيب العرض',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'حالة القسم',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_page_section` (`page_id`, `section_key`),
    INDEX `idx_sections_page` (`page_id`),
    INDEX `idx_sections_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- جداول المراقبة
-- =============================================================================

-- جدول سجل الأخطاء
CREATE TABLE IF NOT EXISTS `error_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `error_type` VARCHAR(100) NOT NULL COMMENT 'نوع الخطأ',
    `error_message` TEXT NOT NULL COMMENT 'رسالة الخطأ',
    `file_name` VARCHAR(255) NULL COMMENT 'اسم الملف',
    `line_number` INT UNSIGNED NULL COMMENT 'رقم السطر',
    `stack_trace` TEXT NULL COMMENT 'تتبع الخطأ',
    `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    `is_resolved` TINYINT(1) DEFAULT 0 COMMENT 'تم الحل',
    `telegram_sent` TINYINT(1) DEFAULT 0 COMMENT 'تم الإرسال لـ Telegram',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_errors_type` (`error_type`),
    INDEX `idx_errors_priority` (`priority`),
    INDEX `idx_errors_resolved` (`is_resolved`),
    INDEX `idx_errors_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول إشعارات النظام
CREATE TABLE IF NOT EXISTS `system_notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL COMMENT 'المستخدم',
    `type` VARCHAR(50) NOT NULL COMMENT 'نوع الإشعار',
    `title` VARCHAR(255) NOT NULL COMMENT 'العنوان',
    `message` TEXT NULL COMMENT 'الرسالة',
    `link` VARCHAR(255) NULL COMMENT 'الرابط',
    `is_read` TINYINT(1) DEFAULT 0 COMMENT 'تم القراءة',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_notifications_user` (`user_id`),
    INDEX `idx_notifications_read` (`is_read`),
    INDEX `idx_notifications_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول جلسات المستخدمين
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` VARCHAR(128) PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` TEXT NULL,
    `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_sessions_user` (`user_id`),
    INDEX `idx_sessions_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول رسائل الاتصال
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'الاسم',
    `email` VARCHAR(255) NULL COMMENT 'البريد الإلكتروني',
    `phone` VARCHAR(20) NULL COMMENT 'الهاتف',
    `subject` VARCHAR(255) NULL COMMENT 'الموضوع',
    `message` TEXT NOT NULL COMMENT 'الرسالة',
    `is_read` TINYINT(1) DEFAULT 0 COMMENT 'تم القراءة',
    `replied_at` TIMESTAMP NULL COMMENT 'تاريخ الرد',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_contact_read` (`is_read`),
    INDEX `idx_contact_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

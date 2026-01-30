-- ===========================================
-- قاعدة بيانات نظام المُوَفِّي لخدمات ريكو
-- ملف كامل للتثبيت على الاستضافة
-- ===========================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------
-- جدول: roles (يجب إنشاؤه أولاً)
-- -------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'اسم الدور',
  `name_ar` varchar(100) NOT NULL COMMENT 'الاسم بالعربية',
  `description` text DEFAULT NULL COMMENT 'وصف الدور',
  `is_system` tinyint(1) DEFAULT 0 COMMENT 'دور نظام (لا يمكن حذفه)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: permissions
-- -------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'اسم الصلاحية',
  `name_ar` varchar(100) NOT NULL COMMENT 'الاسم بالعربية',
  `group_name` varchar(100) NOT NULL COMMENT 'مجموعة الصلاحية',
  `description` text DEFAULT NULL COMMENT 'وصف الصلاحية',
  `module` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_permissions_name` (`name`),
  KEY `idx_permissions_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: role_permissions
-- -------------------------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: users
-- -------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL COMMENT 'اسم المستخدم',
  `email` varchar(255) NOT NULL COMMENT 'البريد الإلكتروني',
  `password` varchar(255) NOT NULL COMMENT 'كلمة المرور المشفرة',
  `full_name` varchar(255) NOT NULL COMMENT 'الاسم الكامل',
  `phone` varchar(20) DEFAULT NULL COMMENT 'رقم الهاتف',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'صورة المستخدم',
  `role_id` int(10) unsigned NOT NULL COMMENT 'الدور',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة الحساب',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'آخر تسجيل دخول',
  `telegram_chat_id` varchar(100) DEFAULT NULL COMMENT 'معرف Telegram',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `idx_users_username` (`username`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_active` (`is_active`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: categories
-- -------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'اسم القسم',
  `slug` varchar(255) NOT NULL COMMENT 'الرابط المختصر',
  `type` enum('copier','printer','spare_part','service') NOT NULL COMMENT 'نوع القسم',
  `description` text DEFAULT NULL COMMENT 'وصف القسم',
  `image` varchar(255) DEFAULT NULL COMMENT 'صورة القسم',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'القسم الأب',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة القسم',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'عنوان SEO',
  `meta_description` text DEFAULT NULL COMMENT 'وصف SEO',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_categories_type` (`type`),
  KEY `idx_categories_active` (`is_active`),
  KEY `idx_categories_sort` (`sort_order`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: products
-- -------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'اسم المنتج',
  `slug` varchar(255) NOT NULL COMMENT 'الرابط المختصر',
  `product_code` varchar(50) NOT NULL COMMENT 'كود المنتج',
  `barcode` varchar(100) DEFAULT NULL COMMENT 'الباركود',
  `model` varchar(255) DEFAULT NULL COMMENT 'الموديل',
  `category_id` int(10) unsigned NOT NULL COMMENT 'القسم',
  `description` text DEFAULT NULL COMMENT 'الوصف المهني',
  `specifications` longtext DEFAULT NULL,
  `raw_specs` text DEFAULT NULL COMMENT 'المواصفات الخام',
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'الصورة المصغرة',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة المنتج',
  `show_faults` tinyint(1) DEFAULT 0 COMMENT 'إظهار الأعطال للعملاء',
  `show_spare_parts` tinyint(1) DEFAULT 0 COMMENT 'إظهار قطع الغيار للعملاء',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'عنوان SEO',
  `meta_description` text DEFAULT NULL COMMENT 'وصف SEO',
  `views_count` int(10) unsigned DEFAULT 0 COMMENT 'عدد المشاهدات',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `product_code` (`product_code`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_sort` (`sort_order`),
  KEY `idx_products_code` (`product_code`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: product_images
-- -------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT 'المنتج',
  `image_path` varchar(255) NOT NULL COMMENT 'مسار الصورة',
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `image_path_processed` varchar(255) DEFAULT NULL COMMENT 'مسار الصورة المعالجة',
  `alt_text` varchar(255) DEFAULT NULL COMMENT 'النص البديل',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'صورة رئيسية',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product` (`product_id`),
  KEY `idx_product_images_primary` (`is_primary`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: product_faults
-- -------------------------------------------
DROP TABLE IF EXISTS `product_faults`;
CREATE TABLE IF NOT EXISTS `product_faults` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT 'المنتج',
  `fault_code` varchar(50) DEFAULT NULL COMMENT 'كود العطل',
  `fault_name` varchar(255) NOT NULL COMMENT 'اسم العطل',
  `description` text DEFAULT NULL COMMENT 'وصف العطل',
  `solution` text DEFAULT NULL COMMENT 'الحل',
  `is_common` tinyint(1) DEFAULT 0 COMMENT 'عطل شائع',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_faults_product` (`product_id`),
  KEY `idx_faults_code` (`fault_code`),
  CONSTRAINT `product_faults_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: spare_parts
-- -------------------------------------------
DROP TABLE IF EXISTS `spare_parts`;
CREATE TABLE IF NOT EXISTS `spare_parts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'اسم القطعة',
  `slug` varchar(255) NOT NULL COMMENT 'الرابط المختصر',
  `part_number` varchar(100) NOT NULL COMMENT 'رقم القطعة',
  `category_id` int(10) unsigned DEFAULT NULL COMMENT 'القسم',
  `description` text DEFAULT NULL COMMENT 'الوصف',
  `image` varchar(255) DEFAULT NULL COMMENT 'الصورة',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة القطعة',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `part_number` (`part_number`),
  KEY `idx_spare_parts_category` (`category_id`),
  KEY `idx_spare_parts_active` (`is_active`),
  KEY `idx_spare_parts_number` (`part_number`),
  CONSTRAINT `spare_parts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: product_spare_parts
-- -------------------------------------------
DROP TABLE IF EXISTS `product_spare_parts`;
CREATE TABLE IF NOT EXISTS `product_spare_parts` (
  `product_id` int(10) unsigned NOT NULL,
  `spare_part_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`spare_part_id`),
  KEY `spare_part_id` (`spare_part_id`),
  CONSTRAINT `product_spare_parts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_spare_parts_ibfk_2` FOREIGN KEY (`spare_part_id`) REFERENCES `spare_parts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: services
-- -------------------------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'اسم الخدمة',
  `slug` varchar(255) NOT NULL COMMENT 'الرابط المختصر',
  `short_description` varchar(500) DEFAULT NULL COMMENT 'وصف مختصر',
  `description` text DEFAULT NULL COMMENT 'الوصف الكامل',
  `icon` varchar(100) DEFAULT NULL COMMENT 'أيقونة الخدمة',
  `image` varchar(255) DEFAULT NULL COMMENT 'صورة الخدمة',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة الخدمة',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_services_active` (`is_active`),
  KEY `idx_services_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: pages
-- -------------------------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL COMMENT 'معرف الصفحة',
  `title` varchar(255) NOT NULL COMMENT 'عنوان الصفحة',
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'عنوان SEO',
  `meta_description` text DEFAULT NULL COMMENT 'وصف SEO',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة الصفحة',
  `show_in_menu` tinyint(1) DEFAULT 0 COMMENT 'عرض في القائمة',
  `show_in_footer` tinyint(1) DEFAULT 0 COMMENT 'عرض في الفوتر',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_pages_slug` (`slug`),
  KEY `idx_pages_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: page_sections
-- -------------------------------------------
DROP TABLE IF EXISTS `page_sections`;
CREATE TABLE IF NOT EXISTS `page_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL COMMENT 'الصفحة',
  `section_key` varchar(100) NOT NULL COMMENT 'معرف القسم',
  `title` varchar(255) DEFAULT NULL COMMENT 'عنوان القسم',
  `content` text DEFAULT NULL COMMENT 'المحتوى',
  `image` varchar(255) DEFAULT NULL COMMENT 'الصورة',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ترتيب العرض',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'حالة القسم',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_section` (`page_id`,`section_key`),
  KEY `idx_sections_page` (`page_id`),
  KEY `idx_sections_sort` (`sort_order`),
  CONSTRAINT `page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: settings
-- -------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL COMMENT 'مفتاح الإعداد',
  `setting_value` text DEFAULT NULL COMMENT 'قيمة الإعداد',
  `setting_type` enum('text','textarea','number','boolean','json','image') DEFAULT 'text',
  `setting_group` varchar(100) DEFAULT 'general' COMMENT 'مجموعة الإعداد',
  `description` varchar(255) DEFAULT NULL COMMENT 'وصف الإعداد',
  `is_system` tinyint(1) DEFAULT 0 COMMENT 'إعداد نظام',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_settings_key` (`setting_key`),
  KEY `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: maintenance_tickets
-- -------------------------------------------
DROP TABLE IF EXISTS `maintenance_tickets`;
CREATE TABLE IF NOT EXISTS `maintenance_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(20) NOT NULL COMMENT 'رقم التذكرة',
  `customer_name` varchar(255) NOT NULL COMMENT 'اسم العميل',
  `customer_phone` varchar(20) NOT NULL COMMENT 'رقم الهاتف',
  `customer_address` text NOT NULL COMMENT 'العنوان',
  `machine_type` enum('copier','printer') NOT NULL COMMENT 'نوع الجهاز',
  `machine_model` varchar(255) DEFAULT NULL COMMENT 'موديل الجهاز',
  `machine_model_image` varchar(255) DEFAULT NULL COMMENT 'صورة الموديل',
  `fault_description` text NOT NULL COMMENT 'وصف العطل',
  `error_code` varchar(50) DEFAULT NULL COMMENT 'كود الخطأ',
  `screen_image` varchar(255) DEFAULT NULL COMMENT 'صورة شاشة الجهاز',
  `fault_type` enum('new','repeated') DEFAULT 'new' COMMENT 'نوع العطل',
  `repeat_count` int(10) unsigned DEFAULT 0 COMMENT 'عدد التكرارات',
  `status` enum('new','received','in_progress','fixed','closed') DEFAULT 'new' COMMENT 'حالة التذكرة',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium' COMMENT 'الأولوية',
  `assigned_to` int(10) unsigned DEFAULT NULL COMMENT 'الفني المسؤول',
  `repair_report` varchar(255) DEFAULT NULL COMMENT 'تقرير الإصلاح PDF',
  `repair_notes` text DEFAULT NULL COMMENT 'ملاحظات الإصلاح',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ الإغلاق',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_tickets_number` (`ticket_number`),
  KEY `idx_tickets_status` (`status`),
  KEY `idx_tickets_phone` (`customer_phone`),
  KEY `idx_tickets_date` (`created_at`),
  KEY `idx_tickets_assigned` (`assigned_to`),
  CONSTRAINT `maintenance_tickets_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: ticket_timeline
-- -------------------------------------------
DROP TABLE IF EXISTS `ticket_timeline`;
CREATE TABLE IF NOT EXISTS `ticket_timeline` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL COMMENT 'التذكرة',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'المستخدم',
  `action` varchar(100) NOT NULL COMMENT 'نوع الإجراء',
  `old_value` text DEFAULT NULL COMMENT 'القيمة القديمة',
  `new_value` text DEFAULT NULL COMMENT 'القيمة الجديدة',
  `notes` text DEFAULT NULL COMMENT 'ملاحظات',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_timeline_ticket` (`ticket_id`),
  KEY `idx_timeline_date` (`created_at`),
  CONSTRAINT `ticket_timeline_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `maintenance_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_timeline_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: ticket_attachments
-- -------------------------------------------
DROP TABLE IF EXISTS `ticket_attachments`;
CREATE TABLE IF NOT EXISTS `ticket_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL COMMENT 'التذكرة',
  `file_name` varchar(255) NOT NULL COMMENT 'اسم الملف',
  `file_path` varchar(255) NOT NULL COMMENT 'مسار الملف',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'نوع الملف',
  `file_size` int(10) unsigned DEFAULT NULL COMMENT 'حجم الملف',
  `uploaded_by` int(10) unsigned DEFAULT NULL COMMENT 'رفع بواسطة',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_attachments_ticket` (`ticket_id`),
  CONSTRAINT `ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `maintenance_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: contact_messages
-- -------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'الاسم',
  `email` varchar(255) DEFAULT NULL COMMENT 'البريد الإلكتروني',
  `phone` varchar(20) DEFAULT NULL COMMENT 'الهاتف',
  `subject` varchar(255) DEFAULT NULL COMMENT 'الموضوع',
  `message` text NOT NULL COMMENT 'الرسالة',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'تم القراءة',
  `replied_at` timestamp NULL DEFAULT NULL COMMENT 'تاريخ الرد',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_read` (`is_read`),
  KEY `idx_contact_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: error_logs
-- -------------------------------------------
DROP TABLE IF EXISTS `error_logs`;
CREATE TABLE IF NOT EXISTS `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_type` varchar(100) NOT NULL COMMENT 'نوع الخطأ',
  `error_message` text NOT NULL COMMENT 'رسالة الخطأ',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'اسم الملف',
  `line_number` int(10) unsigned DEFAULT NULL COMMENT 'رقم السطر',
  `stack_trace` text DEFAULT NULL COMMENT 'تتبع الخطأ',
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_resolved` tinyint(1) DEFAULT 0 COMMENT 'تم الحل',
  `telegram_sent` tinyint(1) DEFAULT 0 COMMENT 'تم الإرسال لـ Telegram',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_errors_type` (`error_type`),
  KEY `idx_errors_priority` (`priority`),
  KEY `idx_errors_resolved` (`is_resolved`),
  KEY `idx_errors_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: system_notifications
-- -------------------------------------------
DROP TABLE IF EXISTS `system_notifications`;
CREATE TABLE IF NOT EXISTS `system_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'المستخدم',
  `type` varchar(50) NOT NULL COMMENT 'نوع الإشعار',
  `title` varchar(255) NOT NULL COMMENT 'العنوان',
  `message` text DEFAULT NULL COMMENT 'الرسالة',
  `link` varchar(255) DEFAULT NULL COMMENT 'الرابط',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'تم القراءة',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_read` (`is_read`),
  KEY `idx_notifications_date` (`created_at`),
  CONSTRAINT `system_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: user_sessions
-- -------------------------------------------
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user` (`user_id`),
  KEY `idx_sessions_activity` (`last_activity`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------
-- جدول: user_activity_logs
-- -------------------------------------------
DROP TABLE IF EXISTS `user_activity_logs`;
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'معرف المستخدم',
  `action` varchar(100) NOT NULL COMMENT 'نوع العملية',
  `description` text DEFAULT NULL COMMENT 'تفاصيل العملية',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'عنوان IP',
  `user_agent` text DEFAULT NULL COMMENT 'معلومات المتصفح',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_date` (`created_at`),
  CONSTRAINT `user_activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ===========================================
-- البيانات الأساسية
-- ===========================================

-- إدراج الأدوار الأساسية
INSERT INTO `roles` (`id`, `name`, `name_ar`, `description`, `is_system`) VALUES
(1, 'admin', 'مدير النظام', 'صلاحيات كاملة للنظام', 1),
(2, 'manager', 'مدير', 'صلاحيات إدارية', 0),
(3, 'technician', 'فني', 'صلاحيات الفنيين', 0),
(4, 'viewer', 'مشاهد', 'صلاحيات القراءة فقط', 0);

-- إدراج مستخدم admin افتراضي (كلمة المرور: admin123)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role_id`, `is_active`) VALUES
(1, 'admin', 'admin@almuwafi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام', 1, 1);

-- إدراج الصفحات الأساسية
INSERT INTO `pages` (`slug`, `title`, `is_active`) VALUES
('home', 'الرئيسية', 1),
('about', 'من نحن', 1),
('contact', 'اتصل بنا', 1),
('services', 'خدماتنا', 1),
('products', 'منتجاتنا', 1),
('spare-parts', 'قطع الغيار', 1),
('maintenance', 'طلب صيانة', 1);

-- ===========================================
-- تم التثبيت بنجاح
-- ===========================================

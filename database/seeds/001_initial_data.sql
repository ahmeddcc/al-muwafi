-- =============================================================================
-- نظام المُوَفِّي لمهمات المكاتب
-- البيانات الأولية
-- =============================================================================

USE `al_muwafi_db`;

-- =============================================================================
-- إدراج الأدوار الأساسية
-- =============================================================================

INSERT INTO `roles` (`name`, `name_ar`, `description`, `is_system`) VALUES
('super_admin', 'مدير النظام', 'صلاحيات كاملة على النظام', 1),
('admin', 'مدير', 'إدارة المحتوى والمستخدمين', 1),
('technician', 'فني صيانة', 'إدارة تذاكر الصيانة', 1),
('editor', 'محرر', 'إدارة المحتوى فقط', 1);

-- =============================================================================
-- إدراج الصلاحيات
-- =============================================================================

INSERT INTO `permissions` (`name`, `name_ar`, `group_name`) VALUES
-- صلاحيات لوحة التحكم
('dashboard.view', 'عرض لوحة التحكم', 'dashboard'),
('dashboard.stats', 'عرض الإحصائيات', 'dashboard'),

-- صلاحيات التذاكر
('tickets.view', 'عرض التذاكر', 'tickets'),
('tickets.create', 'إنشاء تذكرة', 'tickets'),
('tickets.edit', 'تعديل التذاكر', 'tickets'),
('tickets.delete', 'حذف التذاكر', 'tickets'),
('tickets.change_status', 'تغيير حالة التذكرة', 'tickets'),
('tickets.assign', 'تعيين فني', 'tickets'),
('tickets.close', 'إغلاق التذاكر', 'tickets'),

-- صلاحيات الأقسام
('categories.view', 'عرض الأقسام', 'categories'),
('categories.create', 'إنشاء قسم', 'categories'),
('categories.edit', 'تعديل الأقسام', 'categories'),
('categories.delete', 'حذف الأقسام', 'categories'),
('categories.reorder', 'إعادة ترتيب الأقسام', 'categories'),

-- صلاحيات المنتجات
('products.view', 'عرض المنتجات', 'products'),
('products.create', 'إنشاء منتج', 'products'),
('products.edit', 'تعديل المنتجات', 'products'),
('products.delete', 'حذف المنتجات', 'products'),
('products.reorder', 'إعادة ترتيب المنتجات', 'products'),
('products.manage_faults', 'إدارة الأعطال', 'products'),
('products.manage_parts', 'إدارة قطع الغيار', 'products'),

-- صلاحيات قطع الغيار
('spare_parts.view', 'عرض قطع الغيار', 'spare_parts'),
('spare_parts.create', 'إنشاء قطعة غيار', 'spare_parts'),
('spare_parts.edit', 'تعديل قطع الغيار', 'spare_parts'),
('spare_parts.delete', 'حذف قطع الغيار', 'spare_parts'),

-- صلاحيات الخدمات
('services.view', 'عرض الخدمات', 'services'),
('services.create', 'إنشاء خدمة', 'services'),
('services.edit', 'تعديل الخدمات', 'services'),
('services.delete', 'حذف الخدمات', 'services'),

-- صلاحيات المستخدمين
('users.view', 'عرض المستخدمين', 'users'),
('users.create', 'إنشاء مستخدم', 'users'),
('users.edit', 'تعديل المستخدمين', 'users'),
('users.delete', 'حذف المستخدمين', 'users'),
('users.toggle_status', 'تفعيل/تعطيل المستخدمين', 'users'),

-- صلاحيات الأدوار
('roles.view', 'عرض الأدوار', 'roles'),
('roles.create', 'إنشاء دور', 'roles'),
('roles.edit', 'تعديل الأدوار', 'roles'),
('roles.delete', 'حذف الأدوار', 'roles'),
('roles.permissions', 'إدارة الصلاحيات', 'roles'),

-- صلاحيات الصفحات
('pages.view', 'عرض الصفحات', 'pages'),
('pages.edit', 'تعديل الصفحات', 'pages'),

-- صلاحيات الإعدادات
('settings.view', 'عرض الإعدادات', 'settings'),
('settings.edit', 'تعديل الإعدادات', 'settings'),
('settings.security', 'إعدادات الأمان', 'settings'),
('settings.telegram', 'إعدادات Telegram', 'settings'),

-- صلاحيات السجلات
('logs.view', 'عرض السجلات', 'logs'),
('logs.clear', 'مسح السجلات', 'logs'),

-- صلاحيات الرسائل
('messages.view', 'عرض الرسائل', 'messages'),
('messages.delete', 'حذف الرسائل', 'messages');

-- =============================================================================
-- ربط الصلاحيات بالأدوار
-- =============================================================================

-- مدير النظام - جميع الصلاحيات
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`;

-- المدير - معظم الصلاحيات عدا إعدادات النظام الحساسة
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM `permissions` 
WHERE `name` NOT IN ('settings.security', 'settings.telegram', 'logs.clear', 'roles.delete');

-- فني الصيانة - صلاحيات التذاكر فقط
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, id FROM `permissions` 
WHERE `group_name` IN ('dashboard', 'tickets');

-- المحرر - صلاحيات المحتوى
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 4, id FROM `permissions` 
WHERE `group_name` IN ('dashboard', 'categories', 'products', 'spare_parts', 'services', 'pages');

-- =============================================================================
-- إنشاء المستخدم الرئيسي
-- =============================================================================

-- كلمة المرور: Admin@123 (مشفرة بـ bcrypt)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role_id`, `is_active`) VALUES
('admin', 'admin@muwafi.com', '$2y$12$LQv3c1yqBWVHxkd0LHAc.OGqmljUyYl8qOVHB2M5xvJvGGNlVV5.6', 'مدير النظام', 1, 1);

-- =============================================================================
-- إعدادات النظام الافتراضية
-- =============================================================================

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `description`, `is_system`) VALUES
-- إعدادات الشركة
('company_name', 'المُوَفِّي لمهمات المكاتب', 'text', 'company', 'اسم الشركة', 0),
('company_logo', '', 'image', 'company', 'شعار الشركة', 0),
('company_favicon', '', 'image', 'company', 'أيقونة الموقع', 0),
('company_email', 'info@muwafi.com', 'text', 'company', 'البريد الإلكتروني', 0),
('company_phone', '', 'text', 'company', 'رقم الهاتف', 0),
('company_whatsapp', '', 'text', 'company', 'رقم الواتساب', 0),
('company_address', '', 'textarea', 'company', 'العنوان', 0),
('company_map_embed', '', 'textarea', 'company', 'كود خريطة Google', 0),

-- إعدادات التواصل الاجتماعي
('social_facebook', '', 'text', 'social', 'فيسبوك', 0),
('social_twitter', '', 'text', 'social', 'تويتر', 0),
('social_instagram', '', 'text', 'social', 'انستغرام', 0),
('social_linkedin', '', 'text', 'social', 'لينكدإن', 0),
('social_youtube', '', 'text', 'social', 'يوتيوب', 0),

-- إعدادات الأمان
('security_disable_right_click', '0', 'boolean', 'security', 'تعطيل النقر بالزر الأيمن', 1),
('security_disable_inspect', '0', 'boolean', 'security', 'تعطيل أدوات المطور', 1),
('security_disable_f12', '0', 'boolean', 'security', 'تعطيل F12', 1),
('security_disable_copy', '0', 'boolean', 'security', 'تعطيل النسخ', 1),
('security_rate_limiting', '1', 'boolean', 'security', 'تفعيل حد الطلبات', 1),
('security_rate_limit_requests', '100', 'number', 'security', 'عدد الطلبات المسموح', 1),
('security_rate_limit_window', '60', 'number', 'security', 'فترة الحد (ثانية)', 1),

-- إعدادات Telegram
('telegram_bot_token', '', 'text', 'telegram', 'توكن البوت', 1),
('telegram_owner_chat_id', '', 'text', 'telegram', 'معرف محادثة المالك', 1),
('telegram_error_chat_id', '', 'text', 'telegram', 'معرف محادثة الأخطاء', 1),
('telegram_notifications_enabled', '0', 'boolean', 'telegram', 'تفعيل الإشعارات', 1),

-- إعدادات الذكاء الاصطناعي
('ai_enabled', '0', 'boolean', 'ai', 'تفعيل الذكاء الاصطناعي', 1),
('ai_api_key', '', 'text', 'ai', 'مفتاح API', 1),
('ai_api_url', '', 'text', 'ai', 'رابط API', 1),

-- إعدادات عامة
('maintenance_mode', '0', 'boolean', 'general', 'وضع الصيانة', 1),
('tickets_enabled', '1', 'boolean', 'general', 'تفعيل نظام التذاكر', 1),
('products_per_page', '12', 'number', 'general', 'عدد المنتجات في الصفحة', 0);

-- =============================================================================
-- إنشاء الصفحات الأساسية
-- =============================================================================

INSERT INTO `pages` (`slug`, `title`, `meta_title`, `meta_description`, `is_active`) VALUES
('home', 'الرئيسية', 'المُوَفِّي لمهمات المكاتب', 'شركة متخصصة في بيع وصيانة آلات تصوير وطابعات ريكو ومستلزمات المكاتب', 1),
('about', 'من نحن', 'من نحن - المُوَفِّي لمهمات المكاتب', 'تعرف على شركة المُوَفِّي لمهمات المكاتب', 1),
('contact', 'اتصل بنا', 'اتصل بنا - المُوَفِّي لمهمات المكاتب', 'تواصل معنا للاستفسارات والدعم الفني', 1),
('products', 'المنتجات', 'منتجاتنا - المُوَفِّي لمهمات المكاتب', 'تصفح منتجاتنا من آلات التصوير والطابعات', 1),
('services', 'الخدمات', 'خدماتنا - المُوَفِّي لمهمات المكاتب', 'خدمات الصيانة والدعم الفني', 1),
('spare-parts', 'قطع الغيار', 'قطع الغيار - المُوَفِّي لمهمات المكاتب', 'قطع غيار أصلية لآلات ريكو', 1),
('maintenance', 'طلب صيانة', 'طلب صيانة - المُوَفِّي لمهمات المكاتب', 'قدم طلب صيانة لجهازك', 1);

-- =============================================================================
-- أقسام صفحة من نحن
-- =============================================================================

INSERT INTO `page_sections` (`page_id`, `section_key`, `title`, `content`, `sort_order`, `is_active`) VALUES
(2, 'overview', 'نبذة عن الشركة', 'نحن شركة المُوَفِّي لمهمات المكاتب، شركة رائدة في مجال بيع وصيانة آلات التصوير والطابعات.', 1, 1),
(2, 'vision', 'رؤيتنا', 'أن نكون الخيار الأول في مجال خدمات آلات التصوير والطابعات.', 2, 1),
(2, 'mission', 'رسالتنا', 'تقديم أفضل الخدمات والمنتجات لعملائنا مع الالتزام بأعلى معايير الجودة.', 3, 1),
(2, 'values', 'قيمنا', 'الجودة - الأمانة - الاحترافية - خدمة العملاء', 4, 1),
(2, 'certifications', 'الشهادات والاعتمادات', '', 5, 0);

-- =============================================================================
-- أقسام صفحة اتصل بنا
-- =============================================================================

INSERT INTO `page_sections` (`page_id`, `section_key`, `title`, `content`, `sort_order`, `is_active`) VALUES
(3, 'form', 'نموذج الاتصال', '', 1, 1),
(3, 'info', 'معلومات الاتصال', '', 2, 1),
(3, 'map', 'الموقع على الخريطة', '', 3, 1);

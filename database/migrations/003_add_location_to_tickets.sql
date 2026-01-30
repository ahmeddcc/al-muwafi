-- =============================================================================
-- إضافة أعمدة تحديد الموقع لتذاكر الصيانة
-- =============================================================================

ALTER TABLE `maintenance_tickets`
ADD COLUMN `location_latitude` DECIMAL(10, 8) NULL COMMENT 'خط العرض' AFTER `customer_address`,
ADD COLUMN `location_longitude` DECIMAL(11, 8) NULL COMMENT 'خط الطول' AFTER `location_latitude`;

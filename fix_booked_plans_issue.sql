-- Fix booked_plans table issue - Complete Solution
-- File: d:\projects\koa_services\fix_booked_plans_issue.sql
-- This script creates both plans and booked_plans tables if they don't exist

USE koaservices;

-- Disable foreign key checks temporarily to avoid issues
SET FOREIGN_KEY_CHECKS = 0;

-- First create the plans table if it doesn't exist
CREATE TABLE IF NOT EXISTS `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `points` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Now create the booked_plans table if it doesn't exist
CREATE TABLE IF NOT EXISTS `booked_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('success','pending','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `booking_date` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booked_plans_user_id_foreign` (`user_id`),
  KEY `booked_plans_plan_id_foreign` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Add foreign key constraints if they don't exist
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
                         WHERE CONSTRAINT_SCHEMA = 'koaservices'
                         AND TABLE_NAME = 'booked_plans'
                         AND CONSTRAINT_NAME = 'booked_plans_user_id_foreign');

SET @sql = IF(@constraint_exists = 0,
              'ALTER TABLE `booked_plans` ADD CONSTRAINT `booked_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE',
              'SELECT "Foreign key booked_plans_user_id_foreign already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
                         WHERE CONSTRAINT_SCHEMA = 'koaservices'
                         AND TABLE_NAME = 'booked_plans'
                         AND CONSTRAINT_NAME = 'booked_plans_plan_id_foreign');

SET @sql = IF(@constraint_exists = 0,
              'ALTER TABLE `booked_plans` ADD CONSTRAINT `booked_plans_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE',
              'SELECT "Foreign key booked_plans_plan_id_foreign already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create migrations table if it doesn't exist
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add migration records to track these changes
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_01_21_120000_create_plans_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` AS m)),
('2025_01_28_000000_add_points_to_plans_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` AS m2)),
('2025_01_16_120000_create_booked_plans_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations` AS m3));

-- Insert sample plans data if table is empty
INSERT IGNORE INTO `plans` (`id`, `name`, `description`, `image`, `price`, `is_active`, `discount_type`, `discount_value`, `points`, `created_at`, `updated_at`) VALUES
(1, 'Basic Plan', 'Perfect for small households with basic cleaning needs', NULL, 99.99, 1, NULL, NULL, 100, NOW(), NOW()),
(2, 'Standard Plan', 'Ideal for medium-sized homes with regular cleaning requirements', NULL, 199.99, 1, 'percentage', 10.00, 200, NOW(), NOW()),
(3, 'Premium Plan', 'Comprehensive cleaning solution for large homes and offices', NULL, 299.99, 1, 'fixed', 50.00, 300, NOW(), NOW()),
(4, 'Ultimate Plan', 'All-inclusive premium service with additional benefits', NULL, 499.99, 1, 'percentage', 15.00, 500, NOW(), NOW());

-- Show tables to confirm creation
SELECT 'Tables created successfully!' as Status;
SHOW TABLES LIKE '%plans%';
DESCRIBE booked_plans;

-- ============================================
-- FIX DATABASE ISSUES
-- Run this SQL to fix missing tables and columns
-- ============================================

USE gym_website;

-- Fix 1: Create beginner_programs table if it doesn't exist
CREATE TABLE IF NOT EXISTS `beginner_programs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `title_ku` VARCHAR(200) DEFAULT NULL,
    `description` TEXT,
    `description_ku` TEXT,
    `image` VARCHAR(255) DEFAULT NULL,
    `duration_weeks` INT DEFAULT 8,
    `days_per_week` INT DEFAULT 3,
    `level` ENUM('absolute_beginner', 'beginner', 'early_intermediate') DEFAULT 'beginner',
    `goal` VARCHAR(255) DEFAULT NULL,
    `goal_ku` VARCHAR(255) DEFAULT NULL,
    `instructions` TEXT,
    `instructions_ku` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fix 2: Create pages table if it doesn't exist
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `title_ku` VARCHAR(200) DEFAULT NULL,
    `slug` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `content_ku` TEXT DEFAULT NULL,
    `meta_description` VARCHAR(500) DEFAULT NULL,
    `meta_description_ku` VARCHAR(500) DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fix 3: Remove trainer_id reference from certificates query
-- (No schema change needed - we'll fix the PHP code instead)

SELECT '✅ Database tables created successfully!' AS Status;

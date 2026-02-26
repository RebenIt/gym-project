-- Add missing tables to existing database
-- Run this in phpMyAdmin or command line:
-- mysql -u root gym_website < add-pages-table.sql

-- Create pages table
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `title_ku` varchar(200) DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `content_ku` text DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_description_ku` varchar(500) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create beginner_programs table
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

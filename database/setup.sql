-- ============================================
-- GYM WEBSITE - COMPLETE DATABASE SETUP
-- Database: gym_website
-- Collation: utf8mb4_unicode_ci
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Drop all tables for clean install
DROP TABLE IF EXISTS `user_game_lists`;
DROP TABLE IF EXISTS `user_game_list_items`;
DROP TABLE IF EXISTS `user_notes`;
DROP TABLE IF EXISTS `booking_services`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `member_plans`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `tips`;
DROP TABLE IF EXISTS `games`;
DROP TABLE IF EXISTS `beginner_games`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `plans`;
DROP TABLE IF EXISTS `trainers`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `pages_content`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- ADMINS TABLE (For Admin Panel)
-- ============================================
CREATE TABLE `admins` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `full_name_ku` VARCHAR(100),
    `phone` VARCHAR(20),
    `role` ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USERS TABLE (Gym Members/Players)
-- ============================================
CREATE TABLE `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `first_name_ku` VARCHAR(50),
    `last_name` VARCHAR(50) NOT NULL,
    `last_name_ku` VARCHAR(50),
    `phone` VARCHAR(20),
    `date_of_birth` DATE,
    `gender` ENUM('male', 'female') DEFAULT 'male',
    `address` TEXT,
    `address_ku` TEXT,
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `bio` TEXT,
    `bio_ku` TEXT,
    `join_date` DATE DEFAULT (CURRENT_DATE),
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `is_verified` TINYINT(1) DEFAULT 0,
    `verification_token` VARCHAR(255),
    `reset_token` VARCHAR(255),
    `reset_token_expires` DATETIME,
    `last_login` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TRAINERS TABLE
-- ============================================
CREATE TABLE `trainers` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `first_name` VARCHAR(50) NOT NULL,
    `first_name_ku` VARCHAR(50),
    `last_name` VARCHAR(50) NOT NULL,
    `last_name_ku` VARCHAR(50),
    `email` VARCHAR(100),
    `phone` VARCHAR(20) NOT NULL,
    `specialization` VARCHAR(200),
    `specialization_ku` VARCHAR(200),
    `bio` TEXT,
    `bio_ku` TEXT,
    `experience_years` INT DEFAULT 0,
    `certifications` TEXT,
    `certifications_ku` TEXT,
    `avatar` VARCHAR(255) DEFAULT 'default-trainer.png',
    `cover_image` VARCHAR(255),
    `social_instagram` VARCHAR(100),
    `social_facebook` VARCHAR(100),
    `social_youtube` VARCHAR(100),
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PLANS TABLE (Membership Plans)
-- ============================================
CREATE TABLE `plans` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `name_ku` VARCHAR(100),
    `description` TEXT,
    `description_ku` TEXT,
    `duration_days` INT NOT NULL DEFAULT 30,
    `price` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(10) DEFAULT 'USD',
    `features` JSON,
    `features_ku` JSON,
    `is_popular` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SERVICES TABLE
-- ============================================
CREATE TABLE `services` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `name_ku` VARCHAR(100),
    `description` TEXT,
    `description_ku` TEXT,
    `short_description` VARCHAR(255),
    `short_description_ku` VARCHAR(255),
    `icon` VARCHAR(50),
    `image` VARCHAR(255),
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- GAMES/EXERCISES TABLE
-- ============================================
CREATE TABLE `games` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `name_ku` VARCHAR(150),
    `description` TEXT,
    `description_ku` TEXT,
    `short_description` VARCHAR(255),
    `short_description_ku` VARCHAR(255),
    `image` VARCHAR(255),
    `youtube_url` VARCHAR(255),
    `difficulty` ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    `muscle_group` VARCHAR(100),
    `muscle_group_ku` VARCHAR(100),
    `equipment_needed` VARCHAR(200),
    `equipment_needed_ku` VARCHAR(200),
    `duration_minutes` INT,
    `calories_burn` INT,
    `instructions` TEXT,
    `instructions_ku` TEXT,
    `tips` TEXT,
    `tips_ku` TEXT,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_beginner_friendly` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `view_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BEGINNER PROGRAM GAMES (2-month starter)
-- ============================================
CREATE TABLE `beginner_games` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `game_id` INT NOT NULL,
    `week_number` INT NOT NULL DEFAULT 1,
    `day_of_week` ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'),
    `sets` INT DEFAULT 3,
    `reps` VARCHAR(50) DEFAULT '10-12',
    `rest_seconds` INT DEFAULT 60,
    `notes` TEXT,
    `notes_ku` TEXT,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`game_id`) REFERENCES `games`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER GAME LISTS (Personal Lists)
-- ============================================
CREATE TABLE `user_game_lists` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL DEFAULT 'My List',
    `name_ku` VARCHAR(100),
    `description` TEXT,
    `is_default` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER GAME LIST ITEMS
-- ============================================
CREATE TABLE `user_game_list_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `list_id` INT NOT NULL,
    `game_id` INT NOT NULL,
    `sets` INT DEFAULT 3,
    `reps` VARCHAR(50) DEFAULT '10-12',
    `notes` TEXT,
    `sort_order` INT DEFAULT 0,
    `is_completed` TINYINT(1) DEFAULT 0,
    `completed_at` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`list_id`) REFERENCES `user_game_lists`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`game_id`) REFERENCES `games`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER DAILY NOTES
-- ============================================
CREATE TABLE `user_notes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `note_date` DATE NOT NULL,
    `title` VARCHAR(150),
    `content` TEXT NOT NULL,
    `mood` ENUM('great', 'good', 'okay', 'tired', 'bad') DEFAULT 'good',
    `weight_kg` DECIMAL(5,2),
    `workout_done` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `user_date_unique` (`user_id`, `note_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TIPS & NEWS (Weekly Tips from Admin)
-- ============================================
CREATE TABLE `tips` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `title_ku` VARCHAR(255),
    `content` TEXT NOT NULL,
    `content_ku` TEXT,
    `excerpt` VARCHAR(500),
    `excerpt_ku` VARCHAR(500),
    `image` VARCHAR(255),
    `category` ENUM('nutrition', 'exercise', 'lifestyle', 'news', 'motivation', 'other') DEFAULT 'other',
    `author_id` INT,
    `view_count` INT DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_published` TINYINT(1) DEFAULT 0,
    `published_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CERTIFICATES & AWARDS
-- ============================================
CREATE TABLE `certificates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `title_ku` VARCHAR(200),
    `description` TEXT,
    `description_ku` TEXT,
    `image` VARCHAR(255),
    `year_received` INT,
    `issuing_organization` VARCHAR(200),
    `issuing_organization_ku` VARCHAR(200),
    `certificate_type` ENUM('certificate', 'award', 'achievement', 'recognition') DEFAULT 'certificate',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- REVIEWS TABLE
-- ============================================
CREATE TABLE `reviews` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT,
    `reviewer_name` VARCHAR(100),
    `reviewer_name_ku` VARCHAR(100),
    `reviewer_avatar` VARCHAR(255),
    `rating` TINYINT NOT NULL DEFAULT 5,
    `review_text` TEXT,
    `review_text_ku` TEXT,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_approved` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONTACT MESSAGES
-- ============================================
CREATE TABLE `contact_messages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `subject` VARCHAR(255),
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `is_replied` TINYINT(1) DEFAULT 0,
    `replied_at` DATETIME,
    `replied_by` INT,
    `admin_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`replied_by`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PAGES CONTENT (For dynamic page editing)
-- ============================================
CREATE TABLE `pages_content` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `page_key` VARCHAR(100) NOT NULL UNIQUE,
    `section_key` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255),
    `title_ku` VARCHAR(255),
    `subtitle` VARCHAR(255),
    `subtitle_ku` VARCHAR(255),
    `content` TEXT,
    `content_ku` TEXT,
    `image` VARCHAR(255),
    `extra_data` JSON,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SETTINGS TABLE
-- ============================================
CREATE TABLE `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_value_ku` TEXT,
    `setting_type` ENUM('text', 'textarea', 'number', 'boolean', 'json', 'image', 'color') DEFAULT 'text',
    `category` VARCHAR(50) DEFAULT 'general',
    `label` VARCHAR(100),
    `label_ku` VARCHAR(100),
    `description` VARCHAR(255),
    `sort_order` INT DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_games_difficulty ON games(difficulty);
CREATE INDEX idx_games_active ON games(is_active);
CREATE INDEX idx_tips_published ON tips(is_published);
CREATE INDEX idx_tips_category ON tips(category);
CREATE INDEX idx_user_notes_date ON user_notes(note_date);
CREATE INDEX idx_contact_read ON contact_messages(is_read);

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Default Admin (Password: admin123 - CHANGE THIS IN PRODUCTION!)
-- Password hash for 'admin123'
INSERT INTO `admins` (`username`, `email`, `password`, `full_name`, `full_name_ku`, `role`) VALUES
('admin', 'admin@fitzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'بەڕێوەبەری سیستەم', 'super_admin');

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_value_ku`, `setting_type`, `category`, `label`, `label_ku`, `sort_order`) VALUES
('site_name', 'FitZone', 'فیتزۆن', 'text', 'general', 'Site Name', 'ناوی ماڵپەڕ', 1),
('site_tagline', 'Premium Gymnastics Hall', 'هۆڵی جیمناستیکی سەرەکی', 'text', 'general', 'Tagline', 'وتەی ماڵپەڕ', 2),
('site_description', 'Transform your fitness journey with state-of-the-art equipment and expert trainers.', 'گەشتی تەندروستیت بگۆڕە لەگەڵ ئامێرە نوێیەکان و ڕاهێنەرە شارەزاکان.', 'textarea', 'general', 'Description', 'وەسف', 3),
('site_email', 'info@fitzone.com', NULL, 'text', 'contact', 'Email', 'ئیمەیڵ', 1),
('site_phone', '+964 750 123 4567', NULL, 'text', 'contact', 'Phone', 'ژمارەی تەلەفۆن', 2),
('site_phone_secondary', '+964 770 987 6543', NULL, 'text', 'contact', 'Secondary Phone', 'ژمارەی دووەم', 3),
('site_address', '123 Fitness Street, Sulaimani, Kurdistan Region, Iraq', '١٢٣ شەقامی تەندروستی، سلێمانی، هەرێمی کوردستان', 'textarea', 'contact', 'Address', 'ناونیشان', 4),
('working_hours', '6:00 AM - 11:00 PM Daily', '٦:٠٠ بەیانی - ١١:٠٠ ئێوارە ڕۆژانە', 'text', 'contact', 'Working Hours', 'کاتژمێری کارکردن', 5),
('social_facebook', 'https://facebook.com/fitzone', NULL, 'text', 'social', 'Facebook URL', 'لینکی فەیسبووک', 1),
('social_instagram', 'https://instagram.com/fitzone', NULL, 'text', 'social', 'Instagram URL', 'لینکی ئینستاگرام', 2),
('social_youtube', 'https://youtube.com/@fitzone', NULL, 'text', 'social', 'YouTube URL', 'لینکی یوتیوب', 3),
('social_tiktok', '', NULL, 'text', 'social', 'TikTok URL', 'لینکی تیکتۆک', 4),
('currency', 'USD', NULL, 'text', 'general', 'Currency', 'دراو', 10),
('currency_symbol', '$', NULL, 'text', 'general', 'Currency Symbol', 'نیشانەی دراو', 11),
('primary_color', '#f97316', NULL, 'color', 'appearance', 'Primary Color', 'ڕەنگی سەرەکی', 1),
('hero_title', 'Build Your Dream Body', 'جەستەی خەونی خۆت دروست بکە', 'text', 'hero', 'Hero Title', 'ناونیشانی سەرەکی', 1),
('hero_subtitle', 'Premium Gymnastics Hall', 'هۆڵی جیمناستیکی سەرەکی', 'text', 'hero', 'Hero Subtitle', 'ژێرناونیشان', 2),
('hero_description', 'Transform your fitness journey with state-of-the-art equipment, expert trainers, and a motivating environment designed for champions.', 'گەشتی تەندروستیت بگۆڕە لەگەڵ ئامێرە نوێیەکان، ڕاهێنەرە شارەزاکان، و ژینگەیەکی هاندەر کە بۆ پاڵەوانان دیزاین کراوە.', 'textarea', 'hero', 'Hero Description', 'وەسفی سەرەکی', 3),
('stat_members', '500', NULL, 'number', 'stats', 'Active Members', 'ئەندامی چالاک', 1),
('stat_trainers', '15', NULL, 'number', 'stats', 'Expert Trainers', 'ڕاهێنەری شارەزا', 2),
('stat_experience', '10', NULL, 'number', 'stats', 'Years Experience', 'ساڵی ئەزموون', 3),
('about_title', 'Why Choose Us?', 'بۆچی ئێمە هەڵبژێریت؟', 'text', 'about', 'About Title', 'ناونیشانی دەربارە', 1),
('about_description', 'Our diverse membership base creates a friendly and supportive atmosphere where you can make friends, stay motivated, and achieve your fitness goals together.', 'ئەندامانی جۆراوجۆرمان کەشوهەوایەکی هاوڕێیانە و پشتگیرانە دروست دەکەن کە دەتوانیت هاوڕێ بکەیت و ئامانجەکانت بەدەست بهێنیت.', 'textarea', 'about', 'About Description', 'وەسفی دەربارە', 2),
('about_image', 'about-gym.jpg', NULL, 'image', 'about', 'About Image', 'وێنەی دەربارە', 3),
('footer_text', '© 2024 FitZone - All Rights Reserved', '© ٢٠٢٤ فیتزۆن - هەموو مافەکان پارێزراون', 'text', 'footer', 'Footer Text', 'نووسینی فووتەر', 1);

-- Default Plans
INSERT INTO `plans` (`name`, `name_ku`, `description`, `description_ku`, `duration_days`, `price`, `features`, `features_ku`, `is_popular`, `sort_order`) VALUES
('Basic', 'سەرەتایی', 'Perfect for beginners starting their fitness journey', 'گونجاو بۆ ئەوانەی تازە دەست پێدەکەن بە گەشتی تەندروستی', 30, 50.00, 
 '["Access to gym equipment", "Locker room access", "Free fitness assessment"]',
 '["دەستگەیشتن بە ئامێرەکان", "ژووری جلوبەرگ", "هەڵسەنگاندنی بێبەرامبەر"]',
 0, 1),
('Pro', 'پڕۆ', 'Most popular plan with additional benefits', 'پلانی هەرە بەناوبانگ لەگەڵ سوودی زیاتر', 30, 80.00,
 '["All Basic features", "Group fitness classes", "Nutrition consultation", "Sauna access"]',
 '["هەموو تایبەتمەندیەکانی سەرەتایی", "پۆلی ڕاهێنانی گرووپی", "ڕاوێژی خۆراک", "دەستگەیشتن بە ساونا"]',
 1, 2),
('Premium', 'پریمیۆم', 'Ultimate package for serious fitness enthusiasts', 'پاکێجی تەواو بۆ ئەوانەی بە جدی دەیانەوێت', 30, 150.00,
 '["All Pro features", "Personal trainer (4 sessions)", "Custom workout plan", "Priority booking"]',
 '["هەموو تایبەتمەندیەکانی پڕۆ", "ڕاهێنەری تایبەت (٤ دانیشتن)", "پلانی ڕاهێنانی تایبەت", "ڕیزەربی پێشتر"]',
 0, 3);

-- Default Services
INSERT INTO `services` (`name`, `name_ku`, `description`, `description_ku`, `short_description`, `short_description_ku`, `icon`, `image`, `is_featured`, `sort_order`) VALUES
('Physical Fitness', 'لەشی تەندروست', 'Complete body workout programs designed to improve your overall fitness, strength, and endurance.', 'پڕۆگرامی ڕاهێنانی تەواوی جەستە بۆ باشترکردنی تەندروستی گشتی، هێز و بەرگری.', 'Complete body workout programs', 'پڕۆگرامی ڕاهێنانی تەواو', '💪', 'service-fitness.jpg', 1, 1),
('Weight Training', 'ڕاهێنانی کێش', 'Build muscle mass and strength with our professional weight training equipment and guidance.', 'ماسولکە و هێزت زیاد بکە لەگەڵ ئامێرە پیشەیییەکان و ڕێنماییەکانمان.', 'Build muscle mass and strength', 'ماسولکەت بەهێز بکە', '🏋️', 'service-weight.jpg', 1, 2),
('Personal Training', 'ڕاهێنانی تایبەت', 'One-on-one sessions with certified trainers to achieve your specific fitness goals faster.', 'دانیشتنی تاکە بە تاک لەگەڵ ڕاهێنەرە پسپۆڕەکان بۆ گەیشتن بە ئامانجەکانت خێراتر.', 'One-on-one sessions with trainers', 'دانیشتنی تاکە بە تاک', '👤', 'service-personal.jpg', 1, 3),
('Cardio Training', 'ڕاهێنانی کاردیۆ', 'Boost your cardiovascular health with our modern cardio equipment and classes.', 'تەندروستی دڵ و خوێنت باشتر بکە لەگەڵ ئامێرە نوێیەکان و پۆلەکانمان.', 'Boost your cardiovascular health', 'تەندروستی دڵ و خوێنت باشتر بکە', '❤️', 'service-cardio.jpg', 1, 4),
('Gymnastics', 'جیمناستیک', 'Learn gymnastics from basics to advanced levels with our expert coaches and safe equipment.', 'جیمناستیک فێربە لە ئاستی سەرەتاییەوە بۆ پێشکەوتوو لەگەڵ ڕاهێنەرە شارەزاکان.', 'Learn gymnastics from basics to advanced', 'جیمناستیک فێربە', '🤸', 'service-gymnastics.jpg', 1, 5),
('Nutrition Planning', 'پلانی خۆراک', 'Get personalized diet plans to complement your training and maximize your results.', 'پلانی خۆراکی تایبەت بۆ خۆت وەربگرە بۆ زیادکردنی ئەنجامەکانت.', 'Personalized diet plans', 'پلانی خۆراکی تایبەت', '🥗', 'service-nutrition.jpg', 1, 6);

-- Default Games/Exercises
INSERT INTO `games` (`name`, `name_ku`, `description`, `description_ku`, `short_description`, `short_description_ku`, `difficulty`, `muscle_group`, `muscle_group_ku`, `equipment_needed`, `equipment_needed_ku`, `duration_minutes`, `calories_burn`, `youtube_url`, `is_beginner_friendly`, `is_featured`, `sort_order`) VALUES
('Push-ups', 'پووش ئەپ', 'A classic bodyweight exercise that targets your chest, shoulders, and triceps. Push-ups are fundamental for building upper body strength.', 'ڕاهێنانێکی کلاسیکی کێشی جەستەیە کە سنگ و شان و بازووی دەکات. پووش ئەپ بنەڕەتیە بۆ دروستکردنی هێزی سەرەوەی جەستە.', 'Classic chest and arm exercise', 'ڕاهێنانی سنگ و باسک', 'beginner', 'Chest, Shoulders, Triceps', 'سنگ، شان، بازوو', 'None (Bodyweight)', 'هیچ (کێشی جەستە)', 10, 100, 'https://www.youtube.com/watch?v=IODxDxX7oi4', 1, 1, 1),
('Squats', 'سکوات', 'The king of leg exercises. Squats target your quadriceps, hamstrings, and glutes while also engaging your core.', 'پاشای ڕاهێنانی قاچ. سکوات ماسولکەی قاچ و کوڵ دەکات هەروەها ناوەندی جەستە بەهێز دەکات.', 'Essential lower body exercise', 'ڕاهێنانی سەرەکی خوارەوەی جەستە', 'beginner', 'Quadriceps, Hamstrings, Glutes', 'ماسولکەی قاچ و کوڵ', 'None or Barbell', 'هیچ یان باربێل', 15, 150, 'https://www.youtube.com/watch?v=aclHkVaku9U', 1, 1, 2),
('Deadlift', 'دێدلیفت', 'A compound exercise that works your entire posterior chain including back, glutes, and hamstrings.', 'ڕاهێنانێکی گەورەیە کە هەموو پشتەوەی جەستە دەکات لەگەڵ کوڵ و قاچ.', 'Full body compound movement', 'جووڵەی هاوگشت بۆ هەموو جەستە', 'intermediate', 'Back, Glutes, Hamstrings', 'پشت، کوڵ، قاچ', 'Barbell or Dumbbells', 'باربێل یان دەمبڵ', 15, 200, 'https://www.youtube.com/watch?v=op9kVnSso6Q', 0, 1, 3),
('Plank', 'پلانک', 'An isometric core exercise that strengthens your entire midsection and improves stability.', 'ڕاهێنانێکی ناوەندی جەستەیە کە هەموو ناوەندەکە بەهێز دەکات و جێگیری باشتر دەکات.', 'Core strengthening hold', 'بەهێزکردنی ناوەندی جەستە', 'beginner', 'Core, Abs', 'ناوەند، سک', 'None (Bodyweight)', 'هیچ (کێشی جەستە)', 5, 50, 'https://www.youtube.com/watch?v=pSHjTRCQxIw', 1, 1, 4),
('Bench Press', 'بێنچ پرێس', 'The ultimate chest building exercise using a barbell. Great for building upper body pushing strength.', 'ڕاهێنانی گەورەی دروستکردنی سنگ بە باربێل. نایابە بۆ دروستکردنی هێزی پاڵنانەوەی سەرەوەی جەستە.', 'Primary chest builder', 'دروستکەری سەرەکی سنگ', 'intermediate', 'Chest, Shoulders, Triceps', 'سنگ، شان، بازوو', 'Barbell, Bench', 'باربێل، بێنچ', 15, 120, 'https://www.youtube.com/watch?v=gRVjAtPip0Y', 0, 1, 5),
('Pull-ups', 'پوول ئەپ', 'A challenging upper body exercise that builds back width and bicep strength.', 'ڕاهێنانێکی قورسی سەرەوەی جەستەیە کە پانی پشت و هێزی بازوو دروست دەکات.', 'Back and bicep builder', 'دروستکەری پشت و بازوو', 'intermediate', 'Back, Biceps', 'پشت، بازوو', 'Pull-up Bar', 'باری هەڵکێشان', 10, 100, 'https://www.youtube.com/watch?v=eGo4IYlbE5g', 0, 1, 6),
('Lunges', 'لەنجەس', 'A unilateral leg exercise that improves balance and targets each leg individually.', 'ڕاهێنانی قاچی تاکەیە کە هاوسەنگی باشتر دەکات و هەر قاچێک بە جیا دەکات.', 'Single leg strength builder', 'دروستکەری هێزی قاچی تاک', 'beginner', 'Quadriceps, Glutes, Hamstrings', 'قاچ، کوڵ', 'None or Dumbbells', 'هیچ یان دەمبڵ', 12, 100, 'https://www.youtube.com/watch?v=QOVaHwm-Q6U', 1, 0, 7),
('Shoulder Press', 'شۆلدەر پرێس', 'An overhead pressing movement that builds strong, defined shoulders.', 'جووڵەی پاڵنانەوە بۆ سەرەوە کە شانی بەهێز و دیار دروست دەکات.', 'Overhead shoulder builder', 'دروستکەری شان', 'beginner', 'Shoulders, Triceps', 'شان، بازوو', 'Dumbbells or Barbell', 'دەمبڵ یان باربێل', 12, 90, 'https://www.youtube.com/watch?v=qEwKCR5JCog', 1, 0, 8),
('Bicep Curls', 'بازوو کەرڵ', 'An isolation exercise specifically targeting the biceps for arm development.', 'ڕاهێنانی تایبەتە بۆ بازوو بۆ گەشەپێدانی دەست.', 'Bicep isolation exercise', 'ڕاهێنانی تایبەتی بازوو', 'beginner', 'Biceps', 'بازوو', 'Dumbbells or Barbell', 'دەمبڵ یان باربێل', 10, 60, 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo', 1, 0, 9),
('Tricep Dips', 'ترایسێپ دیپس', 'A bodyweight exercise that effectively targets the triceps and chest.', 'ڕاهێنانی کێشی جەستەیە کە بە کاریگەری دووبازوو و سنگ دەکات.', 'Tricep focused exercise', 'ڕاهێنانی تایبەتی دووبازوو', 'beginner', 'Triceps, Chest', 'دووبازوو، سنگ', 'Parallel Bars or Bench', 'بار یان بێنچ', 10, 80, 'https://www.youtube.com/watch?v=6kALZikXxLc', 1, 0, 10);

-- Default Beginner Program (Week 1-8)
INSERT INTO `beginner_games` (`game_id`, `week_number`, `day_of_week`, `sets`, `reps`, `rest_seconds`, `notes`, `notes_ku`, `sort_order`) VALUES
(1, 1, 'monday', 3, '8-10', 60, 'Start with knee push-ups if needed', 'لە پووش ئەپی ئەژنۆ دەست پێبکە ئەگەر پێویست بوو', 1),
(2, 1, 'monday', 3, '10-12', 60, 'Focus on form, go deep', 'سەرنج بدە لە فۆرم، قووڵ بچۆ', 2),
(4, 1, 'monday', 3, '20-30 sec', 30, 'Hold position with straight body', 'پۆزیشن بگرە بە جەستەی ڕاست', 3),
(7, 1, 'wednesday', 3, '10 each leg', 60, 'Keep front knee behind toes', 'ئەژنۆی پێشەوە لە پشت پەنجەکان بهێڵەوە', 1),
(8, 1, 'wednesday', 3, '10-12', 60, 'Start with light weight', 'لە کێشی سووک دەست پێبکە', 2),
(9, 1, 'wednesday', 3, '12-15', 45, 'Control the movement', 'جووڵەکە کۆنتڕۆڵ بکە', 3),
(1, 1, 'friday', 3, '10-12', 60, 'Try to increase reps from Monday', 'هەوڵبدە ڕیپ زیاد بکەیت لە دووشەممە', 1),
(2, 1, 'friday', 3, '12-15', 60, 'Add depth to your squat', 'قووڵی زیاد بکە لە سکواتەکەت', 2),
(10, 1, 'friday', 3, '8-10', 60, 'Use a bench if parallel bars unavailable', 'بێنچ بەکاربهێنە ئەگەر بار نەبوو', 3);

-- Default Trainers
INSERT INTO `trainers` (`first_name`, `first_name_ku`, `last_name`, `last_name_ku`, `email`, `phone`, `specialization`, `specialization_ku`, `bio`, `bio_ku`, `experience_years`, `certifications`, `certifications_ku`, `social_instagram`, `social_facebook`, `is_active`, `sort_order`) VALUES
('Ahmad', 'ئەحمەد', 'Kareem', 'کەریم', 'ahmad@fitzone.com', '+964 750 111 2222', 'Strength & Conditioning', 'هێز و ئامادەکاری', 'Ahmad is a certified strength and conditioning specialist with over 8 years of experience. He has trained professional athletes and helped hundreds of clients achieve their fitness goals.', 'ئەحمەد پسپۆڕی هێز و ئامادەکاریە لەگەڵ زیاتر لە ٨ ساڵ ئەزموون. ئەو وەرزشوانی پیشەیی ڕاهێناوە و یارمەتی سەدان کەس داوە بۆ گەیشتن بە ئامانجەکانیان.', 8, 'NSCA-CSCS, CrossFit Level 2', 'NSCA-CSCS، کرۆسفیت ئاستی ٢', '@ahmad_trainer', 'ahmad.trainer', 1, 1),
('Sara', 'سارا', 'Mohammed', 'محەممەد', 'sara@fitzone.com', '+964 750 333 4444', 'Yoga & Flexibility', 'یۆگا و نەرمی', 'Sara brings a holistic approach to fitness with her expertise in yoga and flexibility training. She believes in the mind-body connection for optimal health.', 'سارا ڕێگایەکی هاوگشت بۆ تەندروستی دەهێنێت لەگەڵ شارەزاییەکەی لە یۆگا و ڕاهێنانی نەرمی. ئەو باوەڕی بە پەیوەندی مێشک و جەستە هەیە بۆ تەندروستی باش.', 6, 'RYT-500, Pilates Certified', 'RYT-500، بڕوانامەی پیلاتس', '@sara_yoga', 'sara.yoga', 1, 2),
('Omed', 'ئۆمێد', 'Hassan', 'حەسەن', 'omed@fitzone.com', '+964 750 555 6666', 'Gymnastics & Calisthenics', 'جیمناستیک و کالیستینیکس', 'Omed is a former competitive gymnast turned coach. His expertise in bodyweight training and gymnastics helps members master impressive skills.', 'ئۆمێد جیمناستیکچی پێشبڕکێکار بووە و ئێستا ڕاهێنەرە. شارەزاییەکەی لە ڕاهێنانی کێشی جەستە و جیمناستیک یارمەتی ئەندامان دەدات بۆ فێربوونی کارە جوانەکان.', 10, 'USA Gymnastics Certified, ACE-CPT', 'بڕوانامەی جیمناستیکی ئەمریکا، ACE-CPT', '@omed_gym', 'omed.gymnastics', 1, 3);

-- Default Reviews
INSERT INTO `reviews` (`reviewer_name`, `reviewer_name_ku`, `rating`, `review_text`, `review_text_ku`, `is_featured`, `is_approved`) VALUES
('Ahmad Kareem', 'ئەحمەد کەریم', 5, 'FitZone completely transformed my fitness journey. The trainers are incredibly supportive and the equipment is top-notch. I have never felt more motivated!', 'فیتزۆن بە تەواوی گەشتی تەندروستیم گۆڕی. ڕاهێنەرەکان زۆر پشتگیرن و ئامێرەکان نایابن. هەرگیز ئاوا هەست بە هاندان نەکردووم!', 1, 1),
('Sara Mohammed', 'سارا محەممەد', 5, 'The gymnastics program here is exceptional. My kids love coming here and have improved so much in just a few months. Highly recommended for families!', 'پڕۆگرامی جیمناستیک لێرە نایابە. منداڵەکانم حەز دەکەن بێنە ئێرە و زۆر باشتر بوون لە چەند مانگێکدا. زۆر پێشنیار دەکرێت بۆ خێزانەکان!', 1, 1),
('Omed Hassan', 'ئۆمێد حەسەن', 5, 'Best gym in the city! The atmosphere is amazing, staff is friendly, and results speak for themselves. Lost 15kg in 6 months with their guidance.', 'باشترین جیم لە شارەکەدا! کەشوهەواکە نایابە، ستافەکە دۆستانەن، و ئەنجامەکان بۆ خۆیان قسە دەکەن. ١٥ کیلۆم کەم کردەوە لە ٦ مانگدا!', 1, 1);

-- Default Tips
INSERT INTO `tips` (`title`, `title_ku`, `content`, `content_ku`, `excerpt`, `excerpt_ku`, `category`, `author_id`, `is_featured`, `is_published`, `published_at`) VALUES
('5 Best Foods for Muscle Building', '٥ خواردنی باشترین بۆ دروستکردنی ماسولکە', 'Building muscle requires proper nutrition. Here are the top 5 foods you should include in your diet:\n\n1. **Chicken Breast** - High in protein, low in fat\n2. **Eggs** - Complete protein source\n3. **Greek Yogurt** - Great for post-workout\n4. **Salmon** - Omega-3 and protein\n5. **Quinoa** - Plant-based protein\n\nRemember to eat protein within 30 minutes after your workout for optimal muscle recovery.', 'دروستکردنی ماسولکە پێویستی بە خۆراکی گونجاوە. لێرەدا ٥ خواردنی سەرەوە هەن کە دەبێت لە خۆراکەکەت هەبن:\n\n١. **سنگی مریشک** - پرۆتینی زۆر، چەوری کەم\n٢. **هێلکە** - سەرچاوەی پرۆتینی تەواو\n٣. **ماستی یۆنانی** - گونجاوە بۆ دوای ڕاهێنان\n٤. **ماسی سەلمۆن** - ئۆمیگا-٣ و پرۆتین\n٥. **کینوا** - پرۆتینی ڕووەکی\n\nبیرت نەچێت لە ماوەی ٣٠ خولەکدا دوای ڕاهێنان پرۆتین بخۆیت بۆ چاکبوونەوەی ماسولکە.', 'Discover the top 5 foods that help build muscle effectively.', 'باشترین ٥ خواردن بدۆزەوە کە یارمەتی دروستکردنی ماسولکە دەدەن.', 'nutrition', 1, 1, 1, NOW()),
('Morning Workout vs Evening Workout', 'ڕاهێنانی بەیانی بەرامبەر ڕاهێنانی ئێوارە', 'Many people wonder when is the best time to exercise. The truth is, both have their benefits:\n\n**Morning Workouts:**\n- Boosts metabolism all day\n- More consistent routine\n- Better sleep quality\n\n**Evening Workouts:**\n- Muscles are warmer\n- Can lift heavier\n- Stress relief after work\n\nThe best time is whenever you can consistently show up!', 'زۆر کەس دەپرسن کەی باشترین کاتە بۆ ڕاهێنان. ڕاستییەکە ئەوەیە، هەردووکیان سوودیان هەیە:\n\n**ڕاهێنانی بەیانی:**\n- میتابۆلیزم زیاد دەکات بۆ هەموو ڕۆژ\n- ڕووتینی بەردەوامتر\n- کوالیتی خەوی باشتر\n\n**ڕاهێنانی ئێوارە:**\n- ماسولکەکان گەرمترن\n- دەتوانیت کێشی قورستر هەڵبگریت\n- مامەڵە لەگەڵ فشار دوای کار\n\nباشترین کات ئەو کاتەیە کە دەتوانیت بەردەوام بێیت!', 'Find out whether morning or evening workouts are better for you.', 'بزانە ڕاهێنانی بەیانی باشترە یان ئێوارە بۆ تۆ.', 'exercise', 1, 0, 1, NOW()),
('Stay Hydrated: Water Tips', 'ئاو بخۆ: ئامۆژگاری ئاو', 'Hydration is crucial for performance. Here are some tips:\n\n- Drink at least 8 glasses of water daily\n- Drink 500ml water 2 hours before workout\n- Sip water during exercise\n- Replenish after workout\n\nSigns of dehydration: headache, fatigue, dark urine, dizziness.', 'ئاو خواردنەوە زۆر گرنگە بۆ ئەدا. لێرەدا هەندێک ئامۆژگاری:\n\n- ڕۆژانە لانی کەم ٨ گڵاس ئاو بخۆوە\n- ٥٠٠مل ئاو بخۆوە ٢ کاتژمێر پێش ڕاهێنان\n- لە کاتی ڕاهێنان ئاو بخۆوە\n- دوای ڕاهێنان ئاو بخۆوەوە\n\nنیشانەی کەمئاوی: سەرێشە، ماندوویی، میزی تاریک، سەرگێژە.', 'Learn why staying hydrated is essential for your fitness journey.', 'فێربە بۆچی ئاو خواردنەوە زۆر گرنگە بۆ گەشتی تەندروستیت.', 'lifestyle', 1, 1, 1, NOW());

-- Default Certificates
INSERT INTO `certificates` (`title`, `title_ku`, `description`, `description_ku`, `year_received`, `issuing_organization`, `issuing_organization_ku`, `certificate_type`, `sort_order`) VALUES
('Best Gym in Kurdistan 2023', 'باشترین جیم لە کوردستان ٢٠٢٣', 'Awarded for excellence in fitness services and customer satisfaction.', 'پاداشتکراوە بۆ نایابی لە خزمەتگوزاریەکانی تەندروستی و ڕازیبوونی موشتەریەکان.', 2023, 'Kurdistan Sports Federation', 'فیدراسیۆنی وەرزشی کوردستان', 'award', 1),
('ISO 9001 Certified', 'بڕوانامەی ISO 9001', 'Quality management system certification for our operations.', 'بڕوانامەی سیستەمی بەڕێوەبردنی کوالیتی بۆ کارەکانمان.', 2022, 'International Organization for Standardization', 'ڕێکخراوی نێودەوڵەتی بۆ ستانداردکردن', 'certificate', 2),
('Community Excellence Award', 'خەڵاتی نایابی کۆمەڵگا', 'Recognition for contributing to community health and wellness programs.', 'ناسینەوە بۆ بەشداریکردن لە پڕۆگرامەکانی تەندروستی کۆمەڵگا.', 2023, 'Sulaimani Municipality', 'شارەوانی سلێمانی', 'recognition', 3);

-- Pages Content
INSERT INTO `pages_content` (`page_key`, `section_key`, `title`, `title_ku`, `subtitle`, `subtitle_ku`, `content`, `content_ku`) VALUES
('home', 'services', 'Our Services', 'خزمەتگوزاریەکانمان', 'What We Offer', 'چی پێشکەش دەکەین', NULL, NULL),
('home', 'about', 'About Us', 'دەربارەی ئێمە', 'Why Choose Us?', 'بۆچی ئێمە هەڵبژێریت؟', NULL, NULL),
('home', 'pricing', 'Our Plans', 'پلانەکانمان', 'Membership Plans', 'پلانی ئەندامێتی', NULL, NULL),
('home', 'reviews', 'Client Reviews', 'بۆچوونەکان', 'Testimonials', 'بۆچوونی موشتەریەکان', NULL, NULL),
('home', 'contact', 'Get In Touch', 'پەیوەندیمان پێوەبکە', 'Contact Us', 'پەیوەندی', 'Ready to start your fitness journey? Contact us today and take the first step towards a healthier you.', 'ئامادەیت گەشتی تەندروستیت دەست پێ بکەیت؟ ئەمڕۆ پەیوەندیمان پێوەبکە و یەکەم هەنگاو بنێ بۆ خۆیەکی تەندروستتر.');

-- ============================================
-- STORED PROCEDURES (Optional but useful)
-- ============================================

DELIMITER //

-- Get user's active subscription
CREATE PROCEDURE GetUserSubscription(IN userId INT)
BEGIN
    SELECT mp.*, p.name, p.name_ku, p.features, p.features_ku
    FROM member_plans mp
    JOIN plans p ON mp.plan_id = p.id
    WHERE mp.member_id = userId 
    AND mp.status = 'active' 
    AND mp.end_date >= CURDATE()
    ORDER BY mp.end_date DESC
    LIMIT 1;
END //

-- Get dashboard stats for admin
CREATE PROCEDURE GetAdminDashboardStats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users WHERE status = 'active') as total_users,
        (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today,
        (SELECT COUNT(*) FROM contact_messages WHERE is_read = 0) as unread_messages,
        (SELECT COUNT(*) FROM games WHERE is_active = 1) as total_games,
        (SELECT COUNT(*) FROM trainers WHERE is_active = 1) as total_trainers,
        (SELECT COUNT(*) FROM tips WHERE is_published = 1) as total_tips;
END //

DELIMITER ;

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT '✅ Database setup completed successfully!' AS Status;
SELECT 'Default admin: admin@fitzone.com / admin123' AS AdminCredentials;
SELECT '⚠️ Remember to change the admin password in production!' AS Warning;

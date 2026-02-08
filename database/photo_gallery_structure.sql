-- ============================================
-- Photo Download Gallery Database Structure
-- ============================================

-- Create download_gallery_events table
CREATE TABLE IF NOT EXISTS `download_gallery_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `program_type` varchar(100) NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL,
  `delete_date` date NOT NULL COMMENT 'Auto-delete after 5 days from event_date',
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `event_date` (`event_date`),
  KEY `program_type` (`program_type`),
  KEY `delete_date` (`delete_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create download_gallery_photos table
CREATE TABLE IF NOT EXISTS `download_gallery_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Size in bytes',
  `mime_type` varchar(100) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `download_count` int(11) DEFAULT 0,
  `is_moved_to_main` tinyint(1) DEFAULT 0 COMMENT 'Moved to main gallery',
  `moved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `is_moved_to_main` (`is_moved_to_main`),
  FOREIGN KEY (`event_id`) REFERENCES `download_gallery_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin users table (for photo gallery admin)
CREATE TABLE IF NOT EXISTS `photo_gallery_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Use password_hash()',
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('admin','team') DEFAULT 'team',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create download log table
CREATE TABLE IF NOT EXISTS `photo_download_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `photo_id` (`photo_id`),
  KEY `event_id` (`event_id`),
  KEY `downloaded_at` (`downloaded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity log table
CREATE TABLE IF NOT EXISTS `photo_gallery_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default admin user
-- Username: admin
-- Password: admin123
-- ============================================
INSERT INTO `photo_gallery_admins` (`username`, `password`, `full_name`, `role`, `is_active`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 1)
ON DUPLICATE KEY UPDATE username=username;

-- Note: The password hash above is for 'admin123'
-- Change this immediately in production!

-- ============================================
-- Indexes for performance
-- ============================================
ALTER TABLE `download_gallery_events` 
ADD INDEX `idx_active_events` (`is_active`, `event_date`);

ALTER TABLE `download_gallery_photos` 
ADD INDEX `idx_active_photos` (`event_id`, `is_moved_to_main`);

-- ============================================
-- Sample Data (Optional - for testing)
-- ============================================

-- Sample Event
INSERT INTO `download_gallery_events` 
(`event_name`, `event_date`, `program_type`, `event_location`, `description`, `delete_date`, `created_by`) 
VALUES 
('जन समारोह - धार', '2026-02-01', 'जन समारोह', 'Dhar', 'Sample event for testing', DATE_ADD('2026-02-01', INTERVAL 5 DAY), 'admin');

-- ============================================
-- Auto-delete trigger (Optional)
-- ============================================

-- Create a stored procedure for cleanup
DELIMITER $$
CREATE PROCEDURE `cleanup_expired_photos`()
BEGIN
    -- Delete photos older than delete_date
    DELETE dgp FROM download_gallery_photos dgp
    INNER JOIN download_gallery_events dge ON dgp.event_id = dge.id
    WHERE dge.delete_date < CURDATE() 
    AND dgp.is_moved_to_main = 0;
    
    -- Delete empty events
    DELETE FROM download_gallery_events 
    WHERE delete_date < CURDATE() 
    AND id NOT IN (SELECT DISTINCT event_id FROM download_gallery_photos);
END$$
DELIMITER ;

-- ============================================
-- Usage Instructions
-- ============================================
-- 1. Import this SQL file into your MySQL database
-- 2. Update db_connect.php with correct credentials
-- 3. Change admin password immediately
-- 4. Set up cron job to run cleanup_expired_photos() daily
--    Example cron: 0 2 * * * php /path/to/cron-cleanup.php
-- ============================================

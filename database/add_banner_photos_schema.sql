-- ============================================
-- Banner/Flex Photo Database Update
-- ============================================

-- Add is_banner_photo column to download_gallery_photos table
-- This column will be used to mark photos for banner and flex printing

ALTER TABLE `download_gallery_photos` 
ADD COLUMN `is_banner_photo` tinyint(1) DEFAULT 0 COMMENT 'Marked as banner/flex photo (1 = yes, 0 = no)';

-- Add index for performance when filtering banner photos
CREATE INDEX `idx_is_banner_photo` ON `download_gallery_photos` (`is_banner_photo`);

-- ============================================
-- Create banner_photo_uploads table for tracking
-- ============================================

CREATE TABLE IF NOT EXISTS `banner_photo_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `dimensions_desc` varchar(100) DEFAULT NULL COMMENT 'e.g., "High-Definition", "Landscape", "Portrait"',
  PRIMARY KEY (`id`),
  UNIQUE KEY `photo_id` (`photo_id`),
  FOREIGN KEY (`photo_id`) REFERENCES `download_gallery_photos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_id`) REFERENCES `photo_gallery_admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- End of Banner Photo Setup
-- ============================================

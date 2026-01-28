-- SQL script to create the puzzle_progress table
-- Compatible with Virtuard Laravel database structure
-- Run this on your Virtuard MySQL database via phpMyAdmin

CREATE TABLE IF NOT EXISTS `puzzle_progress` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed_levels` int(11) NOT NULL DEFAULT 0,
  `completed_level_ids` text COLLATE utf8mb4_unicode_ci,
  `coins` int(11) NOT NULL DEFAULT 0,
  `timestamp` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `puzzle_progress_email_unique` (`email`),
  KEY `puzzle_progress_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

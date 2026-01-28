<?php
/**
 * One-time script to create the puzzle_progress table
 */

$host = '31.97.50.178';
$dbname = 'virtuard_db';
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '31525';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `puzzle_progress` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "Table 'puzzle_progress' created successfully or already exists.";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
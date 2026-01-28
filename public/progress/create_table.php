<?php
/**
 * One-time script to create the puzzle_progress table
 */

$host = 'localhost';
$dbname = 'virtuard';
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `puzzle_progress` (
      [id](cci:1://file:///C:/Users/User/.gemini/antigravity/scratch/AndroidPuzzle/app/src/main/java/com/antoniorutilio/puzzle/HomeActivity.kt:351:4-475:5) bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `email` varchar(255) NOT NULL,
      `completed_levels` int(11) NOT NULL DEFAULT 0,
      `completed_level_ids` text,
      `coins` int(11) NOT NULL DEFAULT 0,
      `timestamp` bigint(20) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY ([id](cci:1://file:///C:/Users/User/.gemini/antigravity/scratch/AndroidPuzzle/app/src/main/java/com/antoniorutilio/puzzle/HomeActivity.kt:351:4-475:5)),
      UNIQUE KEY `puzzle_progress_email_unique` (`email`),
      KEY `puzzle_progress_email_index` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "<h1>Table 'puzzle_progress' created successfully or already exists in database 'virtuard'.</h1>";

} catch (PDOException $e) {
    echo "<h1>Database error: " . $e->getMessage() . "</h1>";
}
?>

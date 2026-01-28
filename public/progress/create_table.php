<?php
$host = 'localhost';
$dbname = 'virtuard';
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password>);
    
    $sql = "CREATE TABLE IF NOT EXISTS `puzzle_progress` (
      [id](cci:1://file:///C:/Users/User/.gemini/antigravity/scratch/AndroidPuzzle/app/src/main/java/com/antoniorutilio/puzzle/HomeActivity.kt:351:4-475:5) bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `email` varchar(255) NOT NULL,
      `completed_levels` int(11) NOT NULL DEFAULT 0,
      `completed_level_ids` text,
      `coins` int(11) NOT NULL DEFAULT 0,
      `timestamp` bigint(20) DEFAULT NULL,
      PRIMARY KEY ([id](cci:1://file:///C:/Users/User/.gemini/antigravity/scratch/AndroidPuzzle/app/src/main/java/com/antoniorutilio/puzzle/HomeActivity.kt:351:4-475:5)),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "<h1>SUCCESSO! Tabella creata nel database.</h1>";
} catch (PDOException $e) {
    echo "<h1>ERRORE: " . $e->getMessage() . "</h1>";
}
?>

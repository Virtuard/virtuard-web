<?php
/**
 * Load User Progress API
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// CONFIGURAZIONE DATABASE
$host = 'localhost';
$dbname = 'virtuard_db'; // <--- ASSICURATI CHE IL NOME DEL DATABASE SIA QUESTO
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = isset($_GET['email']) ? $_GET['email'] : null;

    if (!$email) {
        http_response_code(400);
        echo json_encode(['status' => 0, 'message' => 'Email required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM puzzle_progress WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $progress = [
            'completedLevels' => (int) $row['completed_levels'],
            'completedLevelIds' => json_decode($row['completed_level_ids'], true) ?: [],
            'coins' => (int) $row['coins'],
            'timestamp' => (int) $row['timestamp']
        ];
        echo json_encode(['status' => 1, 'message' => 'Loaded', 'progress' => $progress]);
    } else {
        echo json_encode(['status' => 1, 'message' => 'No progress', 'progress' => ['completedLevels' => 0, 'completedLevelIds' => [], 'coins' => 0, 'timestamp' => time() * 1000]]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 0, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>

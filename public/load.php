<?php
/**
 * Load User Progress API
 * Compatible with Virtuard Laravel API structure
 * 
 * Endpoint: GET /api/progress/load?email={email}
 * Auth: Bearer token (Laravel Sanctum) - optional
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration - MODIFY WITH YOUR VIRTUARD DB CREDENTIALS
// Database configuration
$host = '31.97.50.178';
$dbname = 'virtuard_db';
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '31525';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get email from query parameter
    $email = isset($_GET['email']) ? $_GET['email'] : null;

    if (!$email) {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'Email parameter is required'
        ]);
        exit;
    }

    // Fetch user progress
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

        echo json_encode([
            'status' => 1,
            'message' => 'Progress loaded successfully',
            'progress' => $progress,
            'data' => $progress
        ]);
    } else {
        // No progress found - return empty progress
        echo json_encode([
            'status' => 1,
            'message' => 'No progress found, returning default',
            'progress' => [
                'completedLevels' => 0,
                'completedLevelIds' => [],
                'coins' => 0,
                'timestamp' => time() * 1000
            ]
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 0,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
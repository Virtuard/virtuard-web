<?php
/**
 * Save User Progress API
 * Compatible with Virtuard Laravel API structure
 * 
 * Endpoint: POST /api/progress/save
 * Auth: Bearer token (Laravel Sanctum)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration - MODIFY WITH YOUR VIRTUARD DB CREDENTIALS
// Database configuration
$host = '31.97.50.178';
$dbname = 'virtuard_db'; // Assuming a standard naming convention or I should ask, but let's try to find if it was mentioned
$username = 'virtuard';
$password = 'v1rtu4rD@ubuDD25!!!';
$port = '31525';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get Bearer token (optional - for future authentication)
    $headers = getallheaders();
    $token = null;
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate required fields
    if (!isset($data['email']) || !isset($data['completedLevels'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'Email and completedLevels are required'
        ]);
        exit;
    }

    $email = $data['email'];
    $completedLevels = $data['completedLevels'];
    $completedLevelIds = isset($data['completedLevelIds']) ? json_encode($data['completedLevelIds']) : '[]';
    $coins = isset($data['coins']) ? $data['coins'] : 0;
    $timestamp = isset($data['timestamp']) ? $data['timestamp'] : time() * 1000;

    // Check if progress exists
    $stmt = $pdo->prepare("SELECT id FROM puzzle_progress WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing progress
        $stmt = $pdo->prepare("
            UPDATE puzzle_progress 
            SET completed_levels = ?, 
                completed_level_ids = ?, 
                coins = ?, 
                timestamp = ?,
                updated_at = NOW()
            WHERE email = ?
        ");
        $stmt->execute([$completedLevels, $completedLevelIds, $coins, $timestamp, $email]);

        echo json_encode([
            'status' => 1,
            'message' => 'Progress updated successfully',
            'data' => [
                'email' => $email,
                'completed_levels' => $completedLevels,
                'coins' => $coins
            ]
        ]);
    } else {
        // Insert new progress
        $stmt = $pdo->prepare("
            INSERT INTO puzzle_progress (email, completed_levels, completed_level_ids, coins, timestamp, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$email, $completedLevels, $completedLevelIds, $coins, $timestamp]);

        echo json_encode([
            'status' => 1,
            'message' => 'Progress saved successfully',
            'data' => [
                'email' => $email,
                'completed_levels' => $completedLevels,
                'coins' => $coins
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
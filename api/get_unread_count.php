<?php
session_start();
require '../config.php';
require '../security.php';

validate_session();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Count unread messages for this user
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count
        FROM messages
        WHERE receiver_id = ? AND is_read = 0
    ");
    
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'count' => (int)$result['unread_count']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'count' => 0, 'message' => 'Database error']);
}

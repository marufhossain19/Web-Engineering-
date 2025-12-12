<?php
session_start();
require '../config.php';
require '../security.php';

validate_session();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$resource_id = $_POST['resource_id'] ?? null;
$resource_type = $_POST['resource_type'] ?? null;

if (!$resource_id || !$resource_type) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Mark all messages in this conversation as read
    $stmt = $pdo->prepare("
        UPDATE messages
        SET is_read = 1
        WHERE resource_id = ?
        AND resource_type = ?
        AND receiver_id = ?
        AND is_read = 0
    ");
    
    $stmt->execute([$resource_id, $resource_type, $user_id]);
    
    echo json_encode([
        'success' => true,
        'marked_read' => $stmt->rowCount()
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

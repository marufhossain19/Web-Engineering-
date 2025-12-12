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

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$resource_id = $_POST['resource_id'] ?? null;
$resource_type = $_POST['resource_type'] ?? null;
$message = trim($_POST['message'] ?? '');
$parent_id = $_POST['parent_id'] ?? null;

if (!$resource_id || !$resource_type || !$message) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!in_array($resource_type, ['note', 'question'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid resource type']);
    exit;
}

if (strlen($message) > 1000) {
    echo json_encode(['success' => false, 'message' => 'Message too long (max 1000 characters)']);
    exit;
}

try {
    // Get resource owner
    $table = $resource_type === 'note' ? 'notes' : 'questions';
    $stmt = $pdo->prepare("SELECT user_id, title FROM $table WHERE id = ?");
    $stmt->execute([$resource_id]);
    $resource = $stmt->fetch();
    
    if (!$resource) {
        echo json_encode(['success' => false, 'message' => 'Resource not found']);
        exit;
    }
    
    $receiver_id = $resource['user_id'];
    $sender_id = $_SESSION['user_id'];
    
    // Don't allow sending messages to yourself (optional - you can remove this check)
    if ($sender_id == $receiver_id && !$parent_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot send a message to yourself']);
        exit;
    }
    
    // If it's a reply, verify parent message exists
    if ($parent_id) {
        $stmt = $pdo->prepare("SELECT id, sender_id, receiver_id FROM messages WHERE id = ?");
        $stmt->execute([$parent_id]);
        $parent = $stmt->fetch();
        
        if (!$parent) {
            echo json_encode(['success' => false, 'message' => 'Parent message not found']);
            exit;
        }
        
        // For replies, swap sender/receiver if needed
        if ($sender_id == $parent['receiver_id']) {
            $receiver_id = $parent['sender_id'];
        } else {
            $receiver_id = $parent['receiver_id'];
        }
    }
    
    // Insert message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, resource_id, resource_type, message, parent_id, is_read)
        VALUES (?, ?, ?, ?, ?, ?, 0)
    ");
    
    $stmt->execute([
        $sender_id,
        $receiver_id,
        $resource_id,
        $resource_type,
        $message,
        $parent_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'message_id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

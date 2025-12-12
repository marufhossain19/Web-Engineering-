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

$user_id = $_SESSION['user_id'];

try {
    // Get all message threads for this user (grouped by resource)
    $stmt = $pdo->prepare("
        SELECT DISTINCT
            m.resource_id,
            m.resource_type,
            CASE 
                WHEN m.resource_type = 'note' THEN n.title
                WHEN m.resource_type = 'question' THEN q.title
            END as resource_title,
            CASE 
                WHEN m.resource_type = 'note' THEN n.course_code
                WHEN m.resource_type = 'question' THEN q.course_code
            END as course_code,
            CASE 
                WHEN m.sender_id = ? THEN m.receiver_id
                ELSE m.sender_id
            END as other_user_id,
            (SELECT COUNT(*) FROM messages 
             WHERE resource_id = m.resource_id 
             AND resource_type = m.resource_type 
             AND receiver_id = ? 
             AND is_read = 0) as unread_count
        FROM messages m
        LEFT JOIN notes n ON m.resource_type = 'note' AND m.resource_id = n.id
        LEFT JOIN questions q ON m.resource_type = 'question' AND m.resource_id = q.id
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.created_at DESC
    ");
    
    $stmt->execute([$user_id, $user_id, $user_id, $user_id]);
    $threads = $stmt->fetchAll();
    
    // For each thread, get all messages
    $conversations = [];
    foreach ($threads as $thread) {
        $stmt = $pdo->prepare("
            SELECT 
                m.*,
                u.name as sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.resource_id = ? 
            AND m.resource_type = ?
            AND (m.sender_id = ? OR m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        
        $stmt->execute([
            $thread['resource_id'],
            $thread['resource_type'],
            $user_id,
            $user_id
        ]);
        
        $messages = $stmt->fetchAll();
        
        // Get other user's name
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$thread['other_user_id']]);
        $other_user = $stmt->fetch();
        
        $conversations[] = [
            'resource_id' => $thread['resource_id'],
            'resource_type' => $thread['resource_type'],
            'resource_title' => $thread['resource_title'],
            'course_code' => $thread['course_code'],
            'other_user_name' => $other_user['name'] ?? 'Unknown',
            'unread_count' => $thread['unread_count'],
            'messages' => $messages
        ];
    }
    
    echo json_encode([
        'success' => true,
        'conversations' => $conversations
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

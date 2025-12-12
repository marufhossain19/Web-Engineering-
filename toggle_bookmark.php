<?php
session_start();
require 'config.php';
require 'functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resource_id = $_POST['resource_id'] ?? null;
    $resource_type = $_POST['resource_type'] ?? null;
    
    if (!$resource_id || !$resource_type) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    try {
        if (hasBookmarked($user_id, $resource_id, $resource_type)) {
            // Unbookmark
            $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND resource_id = ? AND resource_type = ?");
            $stmt->execute([$user_id, $resource_id, $resource_type]);
            $isBookmarked = false;
        } else {
            // Bookmark
            $stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, resource_id, resource_type) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $resource_id, $resource_type]);
            $isBookmarked = true;
        }
        
        echo json_encode(['success' => true, 'is_bookmarked' => $isBookmarked]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

<?php
session_start();
require 'config.php';
require 'functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$resourceId = $_POST['resource_id'] ?? null;
$resourceType = $_POST['resource_type'] ?? null;

if (!$resourceId || !$resourceType || !in_array($resourceType, ['note', 'question'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Try to insert download record (will fail if already downloaded by this user)
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO downloads (user_id, resource_id, resource_type) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $resourceId, $resourceType]);
    
    // Get total unique download count
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT user_id) as download_count 
        FROM downloads 
        WHERE resource_id = ? AND resource_type = ?
    ");
    $stmt->execute([$resourceId, $resourceType]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'download_count' => $result['download_count']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>

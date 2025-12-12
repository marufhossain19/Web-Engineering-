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
    
    if (!$resource_id || !$resource_type || !in_array($resource_type, ['note', 'question'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit;
    }
    
    $table = $resource_type . 's';
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT file_path FROM $table WHERE id = ? AND user_id = ?");
        $stmt->execute([$resource_id, $user_id]);
        $resource = $stmt->fetch();
        
        if (!$resource) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        if (file_exists($resource['file_path'])) {
            unlink($resource['file_path']);
        }
        
        $deleteStmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $deleteStmt->execute([$resource_id]);
        
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>

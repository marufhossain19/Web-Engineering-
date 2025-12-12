<?php
session_start();
require 'config.php';
require 'functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Get form data
    $department = $_POST['department'] ?? null;
    $batch = $_POST['batch'] ?? null;
    $section = $_POST['section'] ?? null;
    $student_id = $_POST['student_id'] ?? null;
    $github_url = $_POST['github_url'] ?? null;
    $linkedin_url = $_POST['linkedin_url'] ?? null;
    $gmail = $_POST['gmail'] ?? null;
    
    // Privacy settings (checkboxes)
    $show_student_id = isset($_POST['show_student_id']) ? 1 : 0;
    $show_github = isset($_POST['show_github']) ? 1 : 0;
    $show_linkedin = isset($_POST['show_linkedin']) ? 1 : 0;
    $show_email = isset($_POST['show_email']) ? 1 : 0;
    $show_gmail = isset($_POST['show_gmail']) ? 1 : 0;
    
    // Validate section format if provided
    if ($section && !preg_match('/^[0-9]+_[A-Z]$/', $section)) {
        echo json_encode(['success' => false, 'message' => 'Section must be in format: number_letter (e.g., 63_C)']);
        exit;
    }
    
    // Validate URLs if provided
    if ($github_url && !filter_var($github_url, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid GitHub URL']);
        exit;
    }
    
    if ($linkedin_url && !filter_var($linkedin_url, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid LinkedIn URL']);
        exit;
    }
    
    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users SET 
            department = ?,
            batch = ?,
            section = ?,
            student_id = ?,
            github_url = ?,
            linkedin_url = ?,
            gmail = ?,
            show_student_id = ?,
            show_github = ?,
            show_linkedin = ?,
            show_email = ?,
            show_gmail = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $department,
        $batch,
        $section,
        $student_id,
        $github_url,
        $linkedin_url,
        $gmail,
        $show_student_id,
        $show_github,
        $show_linkedin,
        $show_email,
        $show_gmail,
        $userId
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

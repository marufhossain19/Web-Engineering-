<?php
// Helper Functions

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user data
function getUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Require login (redirect if not logged in)
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Get uploader name by user_id
function getUploaderName($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    return $user ? $user['name'] : 'Unknown';
}

// Check if user liked a resource
function hasLiked($user_id, $resource_id, $resource_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND resource_id = ? AND resource_type = ?");
    $stmt->execute([$user_id, $resource_id, $resource_type]);
    return $stmt->fetchColumn() > 0;
}

// Get like count for a resource
function getLikeCount($resource_id, $resource_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE resource_id = ? AND resource_type = ?");
    $stmt->execute([$resource_id, $resource_type]);
    return $stmt->fetchColumn();
}

// Check if a user has bookmarked a resource
function hasBookmarked($user_id, $resource_id, $type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookmarks WHERE user_id = ? AND resource_id = ? AND resource_type = ?");
    $stmt->execute([$user_id, $resource_id, $type]);
    return $stmt->fetchColumn() > 0;
}

// Get user's bookmarks
function getBookmarks($user_id) {
    global $pdo;
    
    // Get bookmarked notes (including deleted/private ones)
    $stmt = $pdo->prepare("
        SELECT n.*, u.name as uploader_name, 'note' as type, b.created_at as bookmarked_at, b.resource_id
        FROM bookmarks b 
        LEFT JOIN notes n ON b.resource_id = n.id 
        LEFT JOIN users u ON n.user_id = u.id 
        WHERE b.user_id = ? AND b.resource_type = 'note'
    ");
    $stmt->execute([$user_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get bookmarked questions (including deleted/private ones)
    $stmt = $pdo->prepare("
        SELECT q.*, u.name as uploader_name, 'question' as type, b.created_at as bookmarked_at, b.resource_id
        FROM bookmarks b 
        LEFT JOIN questions q ON b.resource_id = q.id 
        LEFT JOIN users u ON q.user_id = u.id 
        WHERE b.user_id = ? AND b.resource_type = 'question'
    ");
    $stmt->execute([$user_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Merge and sort by bookmarked time
    $bookmarks = array_merge($notes, $questions);
    usort($bookmarks, function($a, $b) {
        return strtotime($b['bookmarked_at']) - strtotime($a['bookmarked_at']);
    });
    
    return $bookmarks;
}

// Get download count for a resource
function getDownloadCount($resource_id, $resource_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM downloads WHERE resource_id = ? AND resource_type = ?");
    $stmt->execute([$resource_id, $resource_type]);
    return $stmt->fetchColumn();
}

// Check if user has downloaded a resource
function hasDownloaded($user_id, $resource_id, $resource_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM downloads WHERE user_id = ? AND resource_id = ? AND resource_type = ?");
    $stmt->execute([$user_id, $resource_id, $resource_type]);
    return $stmt->fetchColumn() > 0;
}
?>

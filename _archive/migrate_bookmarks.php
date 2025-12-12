<?php
require 'config.php';

try {
    // Create bookmarks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookmarks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        resource_id INT NOT NULL,
        resource_type ENUM('note', 'question') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_bookmark (user_id, resource_id, resource_type),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Bookmarks table created successfully.<br>";
    
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
?>

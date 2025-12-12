<?php
require 'config.php';

echo "Creating downloads table...\n\n";

try {
    // Create downloads table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS downloads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            resource_id INT NOT NULL,
            resource_type ENUM('note', 'question') NOT NULL,
            downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_download (user_id, resource_id, resource_type),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    echo "✓ Downloads table created successfully\n";
    echo "✓ Added unique constraint to prevent duplicate downloads\n";
    echo "✓ Added foreign key for user_id\n";
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>

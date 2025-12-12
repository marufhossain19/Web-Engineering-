<?php
require 'config.php';

echo "Adding gmail column to users table...\n\n";

try {
    // Add gmail column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS gmail VARCHAR(255) DEFAULT NULL");
    echo "✓ Added gmail column\n";
    
    // Add show_gmail column for privacy
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS show_gmail TINYINT(1) DEFAULT 1");
    echo "✓ Added show_gmail column\n";
    
    echo "\n✅ Migration completed successfully!\n";
    echo "Gmail columns have been added to the users table.\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>

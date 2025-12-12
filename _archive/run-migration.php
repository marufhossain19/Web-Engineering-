<?php
require 'config.php';

echo "Running database migration...\n\n";

try {
    // Add department column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT NULL");
    echo "✓ Added department column\n";
    
    // Add batch column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS batch VARCHAR(10) DEFAULT NULL");
    echo "✓ Added batch column\n";
    
    // Add section column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS section VARCHAR(10) DEFAULT NULL");
    echo "✓ Added section column\n";
    
    // Add student_id column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS student_id VARCHAR(50) DEFAULT NULL");
    echo "✓ Added student_id column\n";
    
    // Add github_url column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS github_url VARCHAR(255) DEFAULT NULL");
    echo "✓ Added github_url column\n";
    
    // Add linkedin_url column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS linkedin_url VARCHAR(255) DEFAULT NULL");
    echo "✓ Added linkedin_url column\n";
    
    // Add show_student_id column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS show_student_id TINYINT(1) DEFAULT 1");
    echo "✓ Added show_student_id column\n";
    
    // Add show_github column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS show_github TINYINT(1) DEFAULT 1");
    echo "✓ Added show_github column\n";
    
    // Add show_linkedin column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS show_linkedin TINYINT(1) DEFAULT 1");
    echo "✓ Added show_linkedin column\n";
    
    // Add show_email column
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS show_email TINYINT(1) DEFAULT 1");
    echo "✓ Added show_email column\n";
    
    echo "\n✅ Migration completed successfully!\n";
    echo "All new columns have been added to the users table.\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>

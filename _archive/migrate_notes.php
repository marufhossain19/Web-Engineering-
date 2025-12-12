<?php
// Database migration script - Run this once to add teacher and description columns
require 'config.php';

try {
    // Add teacher column
    $pdo->exec("ALTER TABLE notes ADD COLUMN teacher VARCHAR(255) DEFAULT 'Course Teacher' AFTER course_code");
    echo "✅ Added 'teacher' column to notes table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ 'teacher' column already exists\n";
    } else {
        echo "❌ Error adding teacher column: " . $e->getMessage() . "\n";
    }
}

try {
    // Add description column
    $pdo->exec("ALTER TABLE notes ADD COLUMN description TEXT NULL AFTER file_path");
    echo "✅ Added 'description' column to notes table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ 'description' column already exists\n";
    } else {
        echo "❌ Error adding description column: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Database migration completed!\n";
?>

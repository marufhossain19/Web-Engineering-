<?php
// Database migration script - Run this once to add exam_type and description columns to questions
require 'config.php';

try {
    // Add exam_type column
    $pdo->exec("ALTER TABLE questions ADD COLUMN exam_type VARCHAR(50) DEFAULT 'Exam' AFTER course_code");
    echo "✅ Added 'exam_type' column to questions table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ 'exam_type' column already exists\n";
    } else {
        echo "❌ Error adding exam_type column: " . $e->getMessage() . "\n";
    }
}

try {
    // Add description column
    $pdo->exec("ALTER TABLE questions ADD COLUMN description TEXT NULL AFTER file_path");
    echo "✅ Added 'description' column to questions table\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ 'description' column already exists\n";
    } else {
        echo "❌ Error adding description column: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Database migration completed!\n";
?>

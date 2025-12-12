<?php
require 'config.php';

try {
    // Add is_public to notes
    $pdo->exec("ALTER TABLE notes ADD COLUMN is_public TINYINT(1) DEFAULT 1");
    echo "Added is_public to notes table.<br>";

    // Add is_public to questions
    $pdo->exec("ALTER TABLE questions ADD COLUMN is_public TINYINT(1) DEFAULT 1");
    echo "Added is_public to questions table.<br>";
    
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') { // Duplicate column error
        echo "Columns already exist.<br>";
    } else {
        die("Migration failed: " . $e->getMessage());
    }
}
?>

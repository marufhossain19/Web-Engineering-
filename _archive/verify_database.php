<?php
require 'config.php';

echo "=== WEBY DATABASE VERIFICATION ===\n\n";

// Check all tables
echo "📊 TABLES IN DATABASE:\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "  ✅ $table\n";
}

echo "\n👥 USERS TABLE:\n";
$users = $pdo->query("SELECT id, name, email, department, created_at FROM users")->fetchAll();
if (empty($users)) {
    echo "  ❌ No users found\n";
} else {
    foreach ($users as $user) {
        echo "  ✅ ID: {$user['id']}\n";
        echo "     Name: {$user['name']}\n";
        echo "     Email: {$user['email']}\n";
        echo "     Department: {$user['department']}\n";
        echo "     Created: {$user['created_at']}\n\n";
    }
}

echo "📝 NOTES TABLE:\n";
$notes = $pdo->query("SELECT COUNT(*) as count FROM notes")->fetch();
echo "  Total Notes: {$notes['count']}\n\n";

echo "❓ QUESTIONS TABLE:\n";
$questions = $pdo->query("SELECT COUNT(*) as count FROM questions")->fetch();
echo "  Total Questions: {$questions['count']}\n\n";

echo "❤️ LIKES TABLE:\n";
$likes = $pdo->query("SELECT COUNT(*) as count FROM likes")->fetch();
echo "  Total Likes: {$likes['count']}\n\n";

echo "✅ DATABASE VERIFICATION COMPLETE!\n";
?>

<?php
// Database Configuration
$host = 'localhost';
$dbname = 'weby_db';
$user = 'root';
$pass = ''; // Default for XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// ========================================
// Gemini AI API Configuration
// ========================================
// IMPORTANT: Keep this key secret! Never commit to version control.
define('GEMINI_API_KEY', 'AIzaSyAZkaY8Cvzz3MBHzNesY7k5S_1mrKomSf4');
define('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent');
?>

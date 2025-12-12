<?php
session_start();
require 'config.php';
require 'security.php';

$error = "";

// Check for security errors FIRST (before redirect logic)
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'session_hijacked':
            $error = "⚠️ Session security violation detected. Please login again.";
            break;
        case 'session_timeout':
            $error = "⏱️ Your session has expired. Please login again.";
            break;
    }
}

// If already logged in and no error to display, redirect to notes
if (isset($_SESSION['user_id']) && empty($error)) {
    header("Location: notes.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Fetch user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        // Initialize session security fingerprints
        init_session_security();
        
        header("Location: notes.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-purple-500 mb-2">Weby</h1>
                <p class="text-gray-400">Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">email</span>
                        <input type="email" name="email" required
                               class="w-full pl-12 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">lock</span>
                        <input type="password" name="password" required
                               class="w-full pl-12 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-purple-600 hover:bg-[#0a0a2e] text-white font-semibold py-3 rounded-lg transition duration-200 border border-transparent hover:border-white">
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-400">Don't have an account? 
                    <a href="register.php" class="text-purple-500 hover:text-purple-400 font-semibold">Register</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

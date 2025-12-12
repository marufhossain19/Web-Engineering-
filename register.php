<?php
session_start();
require 'config.php';
require 'security.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $department = trim($_POST['department']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, department) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $department]);
            
            // Set success message and redirect to login
            $success = "Registration successful! Please login with your credentials.";
            
            // Optional: Auto-redirect to login page after 2 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email already exists.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-purple-500 mb-2">Weby</h1>
                <p class="text-gray-400">Create your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($success) ?> <a href="login.php" class="underline font-semibold">Login now</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <div class="relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">person</span>
                        <input type="text" name="name" required
                               class="w-full pl-12 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">email</span>
                        <input type="email" name="email" required
                               class="w-full pl-12 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Department</label>
                    <div class="relative">
                        <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">school</span>
                        <input type="text" name="department" required
                               class="w-full pl-12 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="e.g., Computer Science">
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
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-400">Already have an account? 
                    <a href="login.php" class="text-purple-500 hover:text-purple-400 font-semibold">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

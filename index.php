<?php
session_start();
require 'security.php';

validate_session();

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weby - Academic Resource Sharing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50 backdrop-blur-md bg-opacity-90">
        <div class="w-full mx-auto px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-purple-500">Weby</a>
                
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="notes.php" class="text-gray-300 hover:text-white transition">Notes</a>
                    <a href="questions.php" class="text-gray-300 hover:text-white transition">Questions</a>
                    <a href="about.php" class="text-gray-300 hover:text-white transition">About</a>
                </div>
                
                <div class="flex items-center gap-3">
                    <?php if ($isLoggedIn): ?>
                        <!-- Messages Notification Icon -->
                        <a href="profile.php#messages" class="relative text-gray-300 hover:text-white transition" title="Messages">
                            <span class="material-icons-round text-2xl">notifications</span>
                            <span id="messagesBadgeNav" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden animate-pulse">0</span>
                        </a>
                        <a href="profile.php" class="text-gray-300 hover:text-white flex items-center gap-2">
                            <span class="material-icons-round">person</span>
                            <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        </a>
                        <a href="logout.php" class="bg-red-600 hover:bg-[#0a0a2e] text-white px-4 py-2 rounded-lg transition text-sm border border-transparent hover:border-white">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-300 hover:text-white">Login</a>
                        <a href="register.php" class="bg-purple-600 hover:bg-[#0a0a2e] text-white px-4 py-2 rounded-lg transition border border-transparent hover:border-white">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <main class="w-full mx-auto px-8 py-16 text-center page-content">
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 fade-in-up">
            Welcome to <span class="text-purple-500">Weby</span>
        </h1>
        
        <p class="text-xl text-gray-400 max-w-3xl mx-auto mb-12 fade-in-up stagger-2">
            The ultimate platform for university students to share notes, download previous questions, 
            and build a powerful academic network.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-20">
            <a href="register.php" class="bg-purple-600 hover:bg-[#0a0a2e] text-white px-8 py-4 rounded-lg text-lg font-semibold transition border border-transparent hover:border-white">
                Get Started
            </a>
            <a href="notes.php" class="bg-gray-800 hover:bg-[#0a0a2e] text-white px-8 py-4 rounded-lg text-lg font-semibold transition border border-gray-700 hover:border-white">
                Browse Notes
            </a>
        </div>
        
        <!-- Features -->
        <h2 class="text-3xl font-bold mb-12">Why Weby?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-purple-500 transition">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-3xl text-white">share</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Share Notes</h3>
                <p class="text-gray-400">
                    Easily upload and share your class notes with peers. Find helpful resources from other students.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-purple-500 transition">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-3xl text-white">help_outline</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Ask Questions</h3>
                <p class="text-gray-400">
                    Stuck on a problem? Access previous exam questions and practice materials.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-purple-500 transition">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-3xl text-white">people</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Network Socially</h3>
                <p class="text-gray-400">
                    Connect with students from your department or batch. Build your professional network.
                </p>
            </div>
            
            <!-- Feature 4 -->
            <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-purple-500 transition">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-3xl text-white">library_books</span>
                </div>
                <h3 class="text-xl font-semibold mb-3">Centralized Resources</h3>
                <p class="text-gray-400">
                    Access a vast library of notes and questions, all in one place and organized by course.
                </p>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 py-8 mt-20">
        <div class="w-full mx-auto px-8 text-center text-gray-400">
            <p>&copy; 2025 Weby. All rights reserved.</p>
        </div>
    </footer>
    
    <?php if ($isLoggedIn): ?>
    <script>
    async function loadNavUnreadCount() {
        try {
            const response = await fetch('api/get_unread_count.php');
            const result = await response.json();
            const badge = document.getElementById('messagesBadgeNav');
            
            if (result.success && result.count > 0) {
                badge.textContent = result.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error loading unread count:', error);
        }
    }
    
    loadNavUnreadCount();
    setInterval(loadNavUnreadCount, 30000);
    </script>
    <?php endif; ?>
</body>
</html>

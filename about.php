<?php
session_start();
require 'config.php';
require 'functions.php';

$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Round" rel="stylesheet">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .team-card {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        .team-card:nth-child(1) { animation-delay: 0.2s; }
        .team-card:nth-child(2) { animation-delay: 0.4s; }
        .team-card:nth-child(3) { animation-delay: 0.6s; }
        
        .profile-img {
            transition: transform 0.3s ease;
        }
        
        .team-card:hover .profile-img {
            transform: scale(1.1);
        }
        
        .social-icon {
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="w-full mx-auto px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-purple-500">Weby</a>
                
                <div class="flex items-center gap-6">
                    <a href="index.php" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="notes.php" class="text-gray-300 hover:text-white transition">Notes</a>
                    <a href="questions.php" class="text-gray-300 hover:text-white transition">Questions</a>
                    <a href="about.php" class="text-white bg-gray-700 px-8 py-1.5 rounded-lg">About</a>
                </div>
                
                <div class="flex items-center gap-3">
                    <?php if ($isLoggedIn): ?>
                        <!-- Messages Notification Icon -->
                        <a href="profile.php#messages" class="relative text-gray-300 hover:text-white transition" title="Messages">
                            <span class="material-icons-round text-2xl">notifications</span>
                            <span id="messagesBadgeNav" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden animate-pulse">0</span>
                        </a>
                        <a href="profile.php" class="text-gray-300 hover:text-white transition flex items-center gap-2">
                            <span class="material-icons-round">person</span>
                            Profile
                        </a>
                        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-300 hover:text-white transition">Login</a>
                        <a href="register.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="w-full mx-auto px-8 py-16">
        <!-- Header -->
        <div class="text-center mb-16 fade-in-up">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">
                About Weby
            </h1>
            <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Weby is a dedicated platform designed to foster collaboration and knowledge sharing among university students. Our mission is to make academic life easier by providing a centralized hub for notes, questions, and networking.
            </p>
        </div>
        
        <!-- Team Section -->
        <div class="max-w-6xl mx-auto mb-16">
            <h2 class="text-4xl font-bold text-center mb-12 fade-in-up">Meet Our Team</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Team Member 1 - Maruf Hossain -->
                <div class="team-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 hover:border-purple-500 transition-all duration-300 shadow-lg hover:shadow-purple-500/20">
                    <div class="flex flex-col items-center">
                        <!-- Profile Image -->
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 p-1 mb-6">
                            <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center profile-img overflow-hidden">
                                <img src="images/maruf.jpg" alt="Maruf Hossain" class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <!-- Name -->
                        <h3 class="text-2xl font-bold mb-2">Md. Maruf Hossen</h3>
                        
                        <!-- Student ID -->
                        <p class="text-gray-400 mb-6">ID: 0242220005101373</p>
                        
                        <!-- Social Links -->
                        <div class="flex gap-4">
                            <a href="https://github.com/marufhossain19" target="_blank" class="social-icon bg-gray-700 hover:bg-purple-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/in/marufhossain19" target="_blank" class="social-icon bg-gray-700 hover:bg-blue-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:maruf1015hossain@gmail.com" target="_blank" class="social-icon bg-gray-700 hover:bg-red-500 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Team Member 2 - Shazidul Haque Simanta -->
                <div class="team-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 hover:border-purple-500 transition-all duration-300 shadow-lg hover:shadow-purple-500/20">
                    <div class="flex flex-col items-center">
                        <!-- Profile Image -->
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 p-1 mb-6">
                            <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center profile-img overflow-hidden">
                                <img src="images/simanto.jpg" alt="Shazidul Haque Simanta" class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <!-- Name -->
                        <h3 class="text-2xl font-bold mb-2">Md. Shazidul Haque</h3>
                        
                        <!-- Student ID -->
                        <p class="text-gray-400 mb-6">ID: 0242220005101404</p>
                        
                        <!-- Social Links -->
                        <div class="flex gap-4">
                            <a href="https://github.com/Shazidul-Haque-Simanta" target="_blank" class="social-icon bg-gray-700 hover:bg-purple-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/in/shazidul-haque-simanta/" target="_blank" class="social-icon bg-gray-700 hover:bg-blue-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:haque22205101404@diu.edu.bd" target="_blank" class="social-icon bg-gray-700 hover:bg-red-500 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Team Member 3 - Ismile Hossain -->
                <div class="team-card bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 hover:border-purple-500 transition-all duration-300 shadow-lg hover:shadow-purple-500/20">
                    <div class="flex flex-col items-center">
                        <!-- Profile Image -->
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 p-1 mb-6">
                            <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center profile-img overflow-hidden">
                                <img src="images/Md. Ismile Hossain.jpg" alt="Ismile Hossain" class="w-full h-full object-cover">
                            </div>
                        </div>
                        
                        <!-- Name -->
                        <h3 class="text-2xl font-bold mb-2">Md. Ismile Hossain</h3>
                        
                        <!-- Student ID -->
                        <p class="text-gray-400 mb-6">ID: 0242220005101001</p>
                        
                        <!-- Social Links -->
                        <div class="flex gap-4">
                            <a href="https://github.com/ismilehossain1001" target="_blank" class="social-icon bg-gray-700 hover:bg-purple-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/in/ismile-hossain/" class="social-icon bg-gray-700 hover:bg-blue-600 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="mailto:ismilehossain211@gmail.com" target="_blank" class="social-icon bg-gray-700 hover:bg-red-500 p-3 rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Developer Info Section -->
        <div class="max-w-4xl mx-auto fade-in-up">
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 border border-gray-700 shadow-lg">
                <h2 class="text-3xl font-bold mb-4">Developer Info</h2>
                <p class="text-gray-300 mb-6">
                    This application was brought to you by a passionate team dedicated to creating useful tools for students.
                </p>
                <div class="flex justify-center gap-6">
                    <a href="#" class="social-icon bg-gray-700 hover:bg-purple-600 p-4 rounded-full transition">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon bg-gray-700 hover:bg-blue-600 p-4 rounded-full transition">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon bg-gray-700 hover:bg-blue-400 p-4 rounded-full transition">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                </div>
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

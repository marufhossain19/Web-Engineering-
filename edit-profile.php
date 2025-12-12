<?php
session_start();
require 'config.php';
require 'functions.php';
require 'security.php';

validate_session();

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="w-full mx-auto px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-purple-500">Weby</a>
                
                <div class="flex items-center gap-6">
                    <a href="index.php" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="notes.php" class="text-gray-300 hover:text-white transition">Notes</a>
                    <a href="questions.php" class="text-gray-300 hover:text-white transition">Questions</a>
                    <a href="about.php" class="text-gray-300 hover:text-white transition">About</a>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="profile.php" class="text-gray-300 hover:text-white transition flex items-center gap-2">
                        <span class="material-icons-round">person</span>
                        Profile
                    </a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="w-full mx-auto px-8 py-12 max-w-4xl">
        <!-- Top Back Button -->
        <div class="flex justify-end mb-6">
            <a href="profile.php" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition flex items-center gap-2 border border-gray-700 hover:border-purple-500">
                <span class="material-icons-round">arrow_back</span>
                Back
            </a>
        </div>
        
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8">
            <h1 class="text-4xl font-bold mb-8 text-center bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">
                Edit Profile
            </h1>
            
            <form id="editProfileForm" class="space-y-6">
                <!-- Academic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Department</label>
                        <select name="department" class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select Department</option>
                            <option value="Computer Science" <?= $user['department'] == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                            <option value="Software Engineering" <?= $user['department'] == 'Software Engineering' ? 'selected' : '' ?>>Software Engineering</option>
                            <option value="Electrical Engineering" <?= $user['department'] == 'Electrical Engineering' ? 'selected' : '' ?>>Electrical Engineering</option>
                            <option value="Business Administration" <?= $user['department'] == 'Business Administration' ? 'selected' : '' ?>>Business Administration</option>
                            <option value="Civil Engineering" <?= $user['department'] == 'Civil Engineering' ? 'selected' : '' ?>>Civil Engineering</option>
                        </select>
                    </div>
                    
                    <!-- Batch -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Batch (e.g., 211)</label>
                        <input type="text" name="batch" value="<?= htmlspecialchars($user['batch'] ?? '') ?>" 
                               placeholder="223" maxlength="10"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Section (e.g., 63_C)</label>
                        <input type="text" name="section" value="<?= htmlspecialchars($user['section'] ?? '') ?>" 
                               placeholder="63_C" maxlength="10" pattern="[0-9]+_[A-Z]"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                        <p class="text-xs text-gray-400 mt-1">Format: number_letter (e.g., 63_C)</p>
                    </div>
                    
                    <!-- Student ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Student ID</label>
                        <input type="text" name="student_id" value="<?= htmlspecialchars($user['student_id'] ?? '') ?>" 
                               placeholder="0242220005101373"
                               class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
                
                <!-- Social & Professional Links -->
                <div class="border-t border-gray-700 pt-6 mt-6">
                    <h2 class="text-2xl font-bold mb-4">Social & Professional Links</h2>
                    
                    <div class="space-y-4">
                        <!-- GitHub -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">GitHub Profile URL</label>
                            <input type="url" name="github_url" value="<?= htmlspecialchars($user['github_url'] ?? '') ?>" 
                                   placeholder="https://github.com/username"
                                   class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <!-- LinkedIn -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">LinkedIn Profile URL</label>
                            <input type="url" name="linkedin_url" value="<?= htmlspecialchars($user['linkedin_url'] ?? '') ?>" 
                                   placeholder="https://linkedin.com/in/username"
                                   class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <!-- Gmail -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Gmail Address</label>
                            <input type="email" name="gmail" value="<?= htmlspecialchars($user['gmail'] ?? '') ?>" 
                                   placeholder="yourname@gmail.com"
                                   class="w-full bg-gray-700 text-white px-4 py-3 rounded-lg border border-gray-600 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                </div>
                
                <!-- Privacy Settings -->
                <div class="border-t border-gray-700 pt-6 mt-6">
                    <h2 class="text-2xl font-bold mb-4">Privacy Settings</h2>
                    <p class="text-gray-400 mb-4">Control what information is visible to other users</p>
                    
                    <div class="space-y-4">
                        <!-- Show Student ID -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                            <span class="text-white">Show Student ID to others</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_student_id" value="1" <?= ($user['show_student_id'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <!-- Show GitHub -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                            <span class="text-white">Show GitHub Profile to others</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_github" value="1" <?= ($user['show_github'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <!-- Show LinkedIn -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                            <span class="text-white">Show LinkedIn Profile to others</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_linkedin" value="1" <?= ($user['show_linkedin'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <!-- Show Email -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                            <span class="text-white">Show Email to others</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_email" value="1" <?= ($user['show_email'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <!-- Show Gmail -->
                        <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                            <span class="text-white">Show Gmail to others</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_gmail" value="1" <?= ($user['show_gmail'] ?? 1) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6">
                    <a href="profile.php" class="flex-1 text-gray-400 hover:text-purple-500 px-8 py-4 rounded-lg text-center font-semibold transition flex items-center justify-center gap-2">
                        <span class="material-icons-round">arrow_back</span>
                        Back to Profile
                    </a>
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-gray-900 text-white px-6 py-4 rounded-lg font-semibold transition border border-purple-600 hover:border-white">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <script>
        document.getElementById('editProfileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validate section format
            const section = formData.get('section');
            if (section && !/^[0-9]+_[A-Z]$/.test(section)) {
                alert('Section must be in format: number_letter (e.g., 63_C)');
                return;
            }
            
            try {
                const response = await fetch('update-profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Profile updated successfully!');
                    window.location.href = 'profile.php';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });
    </script>
</body>
</html>

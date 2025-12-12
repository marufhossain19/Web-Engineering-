<?php
session_start();
require 'config.php';
require 'functions.php';
require 'security.php';

validate_session();
requireLogin(); // Must be logged in to upload

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $course_code = trim($_POST['course_code']);
    $exam_type = $_POST['exam_type'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $description = trim($_POST['description'] ?? '');
    
    // File upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowed = ['pdf'];
        $filename = $_FILES['file']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($filetype, $allowed)) {
            $error = "Only PDF files are allowed.";
        } else {
            // Create uploads directory if not exists
            if (!file_exists('uploads/questions')) {
                mkdir('uploads/questions', 0777, true);
            }
            
            // Generate unique filename
            $newFilename = uniqid() . '_' . $filename;
            $destination = 'uploads/questions/' . $newFilename;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
                // Insert into database
                $stmt = $pdo->prepare("INSERT INTO questions (user_id, title, course_code, exam_type, semester, year, file_path, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $title, $course_code, $exam_type, $semester, $year, $destination, $description]);
                
                $success = "Question uploaded successfully!";
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "Please select a file to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Question - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700">
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
                    <a href="profile.php" class="text-gray-300 hover:text-white flex items-center gap-2">
                        <span class="material-icons-round">person</span>
                        <span class="hidden md:inline"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </a>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition text-sm">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="mx-auto px-1 py-12 max-w-2xl">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8">
            <h1 class="text-3xl font-bold mb-6">Upload Question</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg mb-6">
                    <?= htmlspecialchars($success) ?> <a href="questions.php" class="underline font-semibold">View all questions</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Title *</label>
                    <input type="text" name="title" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                           placeholder="e.g., Data Structures Final Exam 2024">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Course Code *</label>
                    <input type="text" name="course_code" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                           placeholder="e.g., CSE201">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Exam Type *</label>
                    <select name="exam_type" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Exam Type</option>
                        <option value="Quiz">Quiz</option>
                        <option value="Mid">Mid</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Semester *</label>
                        <select name="semester" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Select Semester</option>
                            <option value="Spring">Spring</option>
                            <option value="Summer">Summer</option>
                            <option value="Fall">Fall</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Year *</label>
                        <select name="year" required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Select Year</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload PDF File *</label>
                    <input type="file" name="file" accept=".pdf" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-400 mt-2">Only PDF files are allowed</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                              placeholder="Please mention if there was any update instruction of question"></textarea>
                    <p class="text-xs text-gray-400 mt-2">Add any special instructions or updates about this question</p>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold transition">
                        Upload Question
                    </button>
                    <a href="questions.php" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

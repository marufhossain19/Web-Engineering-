<?php
session_start();
require 'config.php';
require 'functions.php';
require 'security.php';

validate_session();

$isLoggedIn = isLoggedIn();

// Check if viewing another user's profile or own profile
$viewingUserId = $_GET['user_id'] ?? ($_SESSION['user_id'] ?? null);

if (!$viewingUserId) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$viewingUserId]);
$profileUser = $stmt->fetch();

if (!$profileUser) {
    header("Location: index.php");
    exit;
}

$isOwnProfile = $isLoggedIn && $_SESSION['user_id'] == $viewingUserId;

// Get user's uploads (notes and questions combined)
// If viewing another user's profile, only show public items
$privacyFilter = $isOwnProfile ? "" : "AND is_public = 1";

$stmt = $pdo->prepare("SELECT *, 'note' as type FROM notes WHERE user_id = ? $privacyFilter
                       UNION ALL 
                       SELECT *, 'question' as type FROM questions WHERE user_id = ? $privacyFilter
                       ORDER BY created_at DESC");
$stmt->execute([$viewingUserId, $viewingUserId]);
$contributions = $stmt->fetchAll();

// Count notes and questions separately
$notesCount = count(array_filter($contributions, fn($c) => $c['type'] === 'note'));
$questionsCount = count(array_filter($contributions, fn($c) => $c['type'] === 'question'));
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profileUser['name']) ?>'s Profile - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/animations.css">
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
                    <?php if ($isLoggedIn): ?>
                        <?php if ($isOwnProfile): ?>
                        <!-- Messages Notification Icon (Only on own profile) -->
                        <button id="openMessagesDrawer" class="relative text-gray-300 hover:text-white transition">
                            <span class="material-icons-round text-2xl">notifications</span>
                            <span id="messagesBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden animate-pulse">0</span>
                        </button>
                        <?php endif; ?>
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
    
    <!-- Main Content -->
    <main class="w-full mx-auto px-8 py-12">
        <!-- Profile Header -->
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 bg-purple-600 rounded-full flex items-center justify-center text-4xl font-bold">
                        <?= strtoupper(substr($profileUser['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold"><?= htmlspecialchars($profileUser['name']) ?><?= $isOwnProfile ? ' (You)' : '' ?></h1>
                            
                            <?php 
                            $totalContributions = $notesCount + $questionsCount;
                            if ($totalContributions >= 10): 
                            ?>
                                <!-- Gold Star Badge for 10+ contributions -->
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg" title="<?= $totalContributions ?> contributions - Gold Contributor">
                                    <span class="material-icons-round text-white text-xl">star</span>
                                </div>
                            <?php elseif ($totalContributions >= 5): ?>
                                <!-- Silver Star Badge for 5+ contributions -->
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center shadow-lg" title="<?= $totalContributions ?> contributions - Silver Contributor">
                                    <span class="material-icons-round text-white text-xl">star</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($profileUser['department']): ?>
                            <p class="text-purple-400 mb-3 mt-2"><?= htmlspecialchars($profileUser['department']) ?></p>
                        <?php endif; ?>
                        
                        <!-- Social Icons -->
                        <div class="flex gap-3">
                            <?php if (($profileUser['github_url'] ?? null) && (($profileUser['show_github'] ?? 1) || $isOwnProfile)): ?>
                                <a href="<?= htmlspecialchars($profileUser['github_url']) ?>" target="_blank" class="bg-gray-700 hover:bg-purple-600 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (($profileUser['linkedin_url'] ?? null) && (($profileUser['show_linkedin'] ?? 1) || $isOwnProfile)): ?>
                                <a href="<?= htmlspecialchars($profileUser['linkedin_url']) ?>" target="_blank" class="bg-gray-700 hover:bg-blue-600 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (($profileUser['gmail'] ?? null) && (($profileUser['show_gmail'] ?? 1) || $isOwnProfile)): ?>
                                <a href="mailto:<?= htmlspecialchars($profileUser['gmail']) ?>" class="bg-gray-700 hover:bg-red-500 p-2 rounded-full transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($isOwnProfile): ?>
                    <a href="edit-profile.php" class="bg-purple-600 hover:bg-gray-900 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition border border-purple-600 hover:border-white">
                        <span class="material-icons-round">edit</span>
                        Edit Profile
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Academic Information -->
            <?php if (($profileUser['student_id'] ?? null) || ($profileUser['batch'] ?? null) || ($profileUser['section'] ?? null)): ?>
                <div class="border-t border-gray-700 pt-6 mt-6">
                    <h2 class="text-xl font-bold mb-4">Academic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php if (($profileUser['student_id'] ?? null) && (($profileUser['show_student_id'] ?? 1) || $isOwnProfile)): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Student ID</p>
                                <p class="text-white font-semibold"><?= htmlspecialchars($profileUser['student_id']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($profileUser['batch'] ?? null): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Batch</p>
                                <p class="text-white font-semibold"><?= htmlspecialchars($profileUser['batch']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($profileUser['section'] ?? null): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Section</p>
                                <p class="text-white font-semibold"><?= htmlspecialchars($profileUser['section']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Bookmarked Resources (Private) -->
        <?php if ($isOwnProfile): 
            $bookmarks = getBookmarks($viewingUserId);
        ?>
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <span class="material-icons-round text-purple-500">bookmark</span>
                Bookmarked Resources
                <span class="text-sm font-normal text-gray-400 bg-gray-800 px-8 py-1 rounded-full ml-2">Private</span>
            </h2>
            
            <?php if (empty($bookmarks)): ?>
                <div class="bg-gray-800 rounded-xl p-8 text-center border border-gray-700">
                    <span class="material-icons-round text-4xl text-gray-600 mb-2">bookmark_border</span>
                    <p class="text-gray-400">You haven't bookmarked any resources yet.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($bookmarks as $item): 
                        $isNote = $item['type'] === 'note';
                        // Check if resource is available (not deleted and public)
                        $isAvailable = !empty($item['title']) && ($item['is_public'] == 1);
                    ?>
                    
                    <?php if (!$isAvailable): ?>
                        <!-- Unavailable Resource Placeholder -->
                        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 p-6 opacity-60">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons-round text-4xl text-gray-500">block</span>
                                <button onclick="event.stopPropagation(); toggleBookmark(<?= $item['resource_id'] ?>, '<?= $item['type'] ?>', this, true)" 
                                        class="p-2 rounded-full transition-colors duration-200 text-red-500 hover:text-red-600">
                                    <span class="material-icons-round text-2xl">bookmark_remove</span>
                                </button>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-400 mb-2">Resource Unavailable</h3>
                            <p class="text-sm text-gray-500">This resource is no longer available. The owner may have deleted it or changed privacy settings.</p>
                        </div>
                    <?php else: ?>
                        <!-- Available Resource Card -->
                        <div onclick="window.location.href='view.php?type=<?= $item['type'] ?>&id=<?= $item['id'] ?>'" class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-<?= $isNote ? 'purple' : 'blue' ?>-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-<?= $isNote ? 'purple' : 'blue' ?>-500/20 cursor-pointer">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-<?= $isNote ? 'purple' : 'blue' ?>-600 to-<?= $isNote ? 'purple' : 'blue' ?>-700 px-4 py-3">
                                <h3 class="text-lg font-bold text-white line-clamp-1"><?= htmlspecialchars($item['title']) ?></h3>
                                <p class="text-<?= $isNote ? 'purple' : 'blue' ?>-200 text-sm font-medium mt-1"><?= htmlspecialchars($item['course_code']) ?></p>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3 text-gray-300">
                                    <span class="material-icons-round text-lg">calendar_today</span>
                                    <span class="text-sm font-medium">
                                        <?php
                                        $semesterMap = [
                                            '2.1' => 'Fall', '2.2' => 'Spring',
                                            '3.1' => 'Fall', '3.2' => 'Spring',
                                            '4.1' => 'Fall', '4.2' => 'Spring'
                                        ];
                                        $semesterName = $semesterMap[$item['semester']] ?? $item['semester'];
                                        echo htmlspecialchars($semesterName . ' ' . $item['year']);
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2 mb-4 text-gray-300">
                                    <span class="material-icons-round text-lg">person</span>
                                    <span class="text-sm">By <a href="profile.php?user_id=<?= $item['user_id'] ?>" onclick="event.stopPropagation()" class="text-<?= $isNote ? 'purple' : 'blue' ?>-400 hover:text-<?= $isNote ? 'purple' : 'blue' ?>-500 underline transition"><?= htmlspecialchars($item['uploader_name']) ?></a></span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex gap-2 items-center">
                                    <button onclick="event.stopPropagation(); toggleBookmark(<?= $item['id'] ?>, '<?= $item['type'] ?>', this, true)" 
                                            class="p-2 rounded-full transition-colors duration-200 text-<?= $isNote ? 'purple' : 'blue' ?>-500 hover:text-<?= $isNote ? 'purple' : 'blue' ?>-600">
                                        <span class="material-icons-round text-2xl">bookmark</span>
                                    </button>
                                    <a href="view.php?type=<?= $item['type'] ?>&id=<?= $item['id'] ?>" onclick="event.stopPropagation()" 
                                       class="flex-1 bg-<?= $isNote ? 'purple' : 'blue' ?>-600 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                                        View
                                    </a>
                                    <a href="<?= htmlspecialchars($item['file_path']) ?>" download onclick="event.stopPropagation()"
                                       class="flex-1 bg-gray-700 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 text-center">
                <span class="material-icons-round text-4xl text-purple-500 mb-2">description</span>
                <h3 class="text-3xl font-bold mb-1"><?= $notesCount ?></h3>
                <p class="text-gray-400">Notes Uploaded</p>
            </div>
            
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 text-center">
                <span class="material-icons-round text-4xl text-purple-500 mb-2">help_outline</span>
                <h3 class="text-3xl font-bold mb-1"><?= $questionsCount ?></h3>
                <p class="text-gray-400">Questions Uploaded</p>
            </div>
            
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 text-center">
                <span class="material-icons-round text-4xl text-purple-500 mb-2">favorite</span>
                <h3 class="text-3xl font-bold mb-1"><?= count($contributions) ?></h3>
                <p class="text-gray-400">Total Contributions</p>
            </div>
        </div>
        
        <!-- My Contributions -->
        <div>
            <h2 class="text-2xl font-bold mb-6"><?= $isOwnProfile ? 'My' : htmlspecialchars($profileUser['name']) . "'s" ?> Contributions</h2>
            
            <?php if (empty($contributions)): ?>
                <p class="text-gray-400"><?= $isOwnProfile ? "You haven't" : "This user hasn't" ?> uploaded any contributions yet.</p>
            <?php else: 
                // Separate notes and questions
                $notes = array_filter($contributions, fn($c) => $c['type'] === 'note');
                $questions = array_filter($contributions, fn($c) => $c['type'] === 'question');
            ?>
            
            <!-- Notes Section -->
            <?php if (!empty($notes)): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-purple-500">description</span>
                    Notes
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($notes as $note): 
                        $likeCount = getLikeCount($note['id'], 'note');
                        $isPrivate = !$note['is_public'];
                    ?>
                    <div onclick="window.location.href='view.php?type=note&id=<?= $note['id'] ?>'" 
                         class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-purple-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-purple-500/20 cursor-pointer <?= $isPrivate ? 'opacity-40' : '' ?>">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-3">
                            <h3 class="text-lg font-bold text-white line-clamp-1"><?= htmlspecialchars($note['title']) ?></h3>
                            <p class="text-purple-200 text-sm font-medium mt-1"><?= htmlspecialchars($note['course_code']) ?></p>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="p-5 relative">
                            <!-- Eye Icon (Top Right) - Owner Only -->
                            <?php if ($isOwnProfile): ?>
                            <button onclick="event.stopPropagation(); togglePrivacy(<?= $note['id'] ?>, 'note', this)" 
                                    class="absolute top-3 right-3 p-2 rounded-full transition-colors duration-200 <?= $isPrivate ? 'text-gray-500' : 'text-purple-500 hover:text-purple-600' ?>">
                                <span class="material-icons-round text-xl"><?= $isPrivate ? 'visibility_off' : 'visibility' ?></span>
                            </button>
                            <?php endif; ?>
                            
                            <!-- Semester/Year -->
                            <div class="flex items-center gap-2 mb-3 text-gray-300">
                                <span class="material-icons-round text-lg">calendar_today</span>
                                <span class="text-sm font-medium">
                                    <?php
                                    $semesterMap = ['2.1' => 'Fall', '2.2' => 'Spring', '3.1' => 'Fall', '3.2' => 'Spring', '4.1' => 'Fall', '4.2' => 'Spring'];
                                    echo htmlspecialchars(($semesterMap[$note['semester']] ?? $note['semester']) . ' ' . $note['year']);
                                    ?>
                                </span>
                            </div>
                            
                            <!-- Like and Download Count -->
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex items-center gap-1 text-gray-400">
                                    <span class="material-icons-round text-lg">favorite</span>
                                    <span class="text-sm font-medium"><?= $likeCount ?></span>
                                </div>
                                <div class="flex items-center gap-1 text-gray-400">
                                    <span class="material-icons-round text-lg">download</span>
                                    <span class="text-sm font-medium">0</span>
                                </div>
                                <?php if ($isPrivate): ?>
                                <span class="private-badge inline-block bg-orange-600 text-white px-2 py-1 rounded text-xs font-semibold">Private</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Delete Icon (Bottom Right) - Owner Only -->
                            <?php if ($isOwnProfile): ?>
                            <button onclick="event.stopPropagation(); confirmDelete(<?= $note['id'] ?>, 'note', '<?= htmlspecialchars($note['title']) ?>')" 
                                    class="absolute bottom-3 right-3 p-2 rounded-full transition-colors duration-200 text-red-500 hover:text-red-600">
                                <span class="material-icons-round text-xl">delete</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Questions Section -->
            <?php if (!empty($questions)): ?>
            <div>
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-blue-500">help_outline</span>
                    Questions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($questions as $question): 
                        $likeCount = getLikeCount($question['id'], 'question');
                        $isPrivate = !$question['is_public'];
                    ?>
                    <div onclick="window.location.href='view.php?type=question&id=<?= $question['id'] ?>'" 
                         class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-blue-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-blue-500/20 cursor-pointer <?= $isPrivate ? 'opacity-40' : '' ?>">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3">
                            <h3 class="text-lg font-bold text-white line-clamp-1"><?= htmlspecialchars($question['title']) ?></h3>
                            <p class="text-blue-200 text-sm font-medium mt-1"><?= htmlspecialchars($question['course_code']) ?></p>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="p-5 relative">
                            <!-- Eye Icon (Top Right) - Owner Only -->
                            <?php if ($isOwnProfile): ?>
                            <button onclick="event.stopPropagation(); togglePrivacy(<?= $question['id'] ?>, 'question', this)" 
                                    class="absolute top-3 right-3 p-2 rounded-full transition-colors duration-200 <?= $isPrivate ? 'text-gray-500' : 'text-blue-500 hover:text-blue-600' ?>">
                                <span class="material-icons-round text-xl"><?= $isPrivate ? 'visibility_off' : 'visibility' ?></span>
                            </button>
                            <?php endif; ?>
                            
                            <!-- Semester/Year -->
                            <div class="flex items-center gap-2 mb-3 text-gray-300">
                                <span class="material-icons-round text-lg">calendar_today</span>
                                <span class="text-sm font-medium">
                                    <?php
                                    $semesterMap = ['2.1' => 'Fall', '2.2' => 'Spring', '3.1' => 'Fall', '3.2' => 'Spring', '4.1' => 'Fall', '4.2' => 'Spring'];
                                    echo htmlspecialchars(($semesterMap[$question['semester']] ?? $question['semester']) . ' ' . $question['year']);
                                    ?>
                                </span>
                            </div>
                            
                            <!-- Like and Download Count -->
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex items-center gap-1 text-gray-400">
                                    <span class="material-icons-round text-lg">favorite</span>
                                    <span class="text-sm font-medium"><?= $likeCount ?></span>
                                </div>
                                <div class="flex items-center gap-1 text-gray-400">
                                    <span class="material-icons-round text-lg">download</span>
                                    <span class="text-sm font-medium">0</span>
                                </div>
                                <?php if ($isPrivate): ?>
                                <span class="private-badge inline-block bg-orange-600 text-white px-2 py-1 rounded text-xs font-semibold">Private</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Delete Icon (Bottom Right) - Owner Only -->
                            <?php if ($isOwnProfile): ?>
                            <button onclick="event.stopPropagation(); confirmDelete(<?= $question['id'] ?>, 'question', '<?= htmlspecialchars($question['title']) ?>')" 
                                    class="absolute bottom-3 right-3 p-2 rounded-full transition-colors duration-200 text-red-500 hover:text-red-600">
                                <span class="material-icons-round text-xl">delete</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        // Toggle Bookmark (Profile Version)
        function toggleBookmark(resourceId, resourceType, btn, isProfile = false) {
            fetch('toggle_bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resource_id=${resourceId}&resource_type=${resourceType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isProfile && !data.is_bookmarked) {
                        const card = btn.closest('.bg-gradient-to-br');
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.remove();
                        }, 300);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Toggle Privacy
        function togglePrivacy(resourceId, resourceType, btn) {
            fetch('toggle_privacy.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resource_id=${resourceId}&resource_type=${resourceType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = btn.closest('.bg-gradient-to-br');
                    const icon = btn.querySelector('.material-icons-round');
                    const cardBody = btn.closest('.p-5');
                    const countsRow = cardBody.querySelector('.flex.items-center.gap-4');
                    
                    if (data.is_public) {
                        // Make public
                        card.classList.remove('opacity-40');
                        icon.textContent = 'visibility';
                        btn.classList.remove('text-gray-500');
                        btn.classList.add(resourceType === 'note' ? 'text-purple-500' : 'text-blue-500');
                        btn.classList.add(resourceType === 'note' ? 'hover:text-purple-600' : 'hover:text-blue-600');
                        
                        // Remove private badge
                        const privateBadge = countsRow.querySelector('.private-badge');
                        if (privateBadge) privateBadge.remove();
                    } else {
                        // Make private
                        card.classList.add('opacity-40');
                        icon.textContent = 'visibility_off';
                        btn.classList.remove(resourceType === 'note' ? 'text-purple-500' : 'text-blue-500');
                        btn.classList.remove(resourceType === 'note' ? 'hover:text-purple-600' : 'hover:text-blue-600');
                        btn.classList.add('text-gray-500');
                        
                        // Add private badge to counts row
                        const badge = document.createElement('span');
                        badge.className = 'private-badge inline-block bg-orange-600 text-white px-2 py-1 rounded text-xs font-semibold';
                        badge.textContent = 'Private';
                        countsRow.appendChild(badge);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Confirm Delete
        function confirmDelete(resourceId, resourceType, title) {
            if (confirm(`Are you sure you want to permanently delete "${title}"? This action cannot be undone.`)) {
                deleteResource(resourceId, resourceType);
            }
        }
        
        // Delete Resource
        function deleteResource(resourceId, resourceType) {
            fetch('delete_resource.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resource_id=${resourceId}&resource_type=${resourceType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find and remove the card
                    const cards = document.querySelectorAll('.bg-gradient-to-br');
                    cards.forEach(card => {
                        const deleteBtn = card.querySelector(`button[onclick*="confirmDelete(${resourceId}"]`);
                        if (deleteBtn) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                card.remove();
                                // Reload if no more cards
                                const remainingCards = document.querySelectorAll('.bg-gradient-to-br').length;
                                if (remainingCards === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    });
                } else {
                    alert('Failed to delete resource');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    
    <?php if ($isOwnProfile): ?>
    <!-- Messages Drawer (Only for own profile) -->
    <div id="messagesBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden"></div>
    
    <aside id="messagesDrawer" class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-gray-800 border-l border-gray-700 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between bg-gray-900">
            <div>
                <h2 class="text-xl font-semibold text-white">Messages</h2>
                <p class="text-xs text-gray-400 mt-0.5">Conversations about your resources</p>
            </div>
            <button id="closeMessagesDrawer" class="h-10 w-10 rounded-full flex items-center justify-center bg-gray-700 border border-gray-600 text-gray-400 hover:text-white hover:bg-gray-600 transition">
                <span class="material-icons-round text-lg">close</span>
            </button>
        </div>
        
        <!-- Messages Content -->
        <div id="messagesContent" class="flex-1 overflow-y-auto p-6">
            <div id="messagesLoading" class="text-center py-12">
                <span class="material-icons-round text-4xl text-purple-500 animate-spin">refresh</span>
                <p class="text-gray-400 mt-2">Loading messages...</p>
            </div>
            
            <div id="messagesEmpty" class="text-center py-12 hidden">
                <span class="material-icons-round text-6xl text-gray-600 mb-4">chat_bubble_outline</span>
                <p class="text-xl text-gray-400">No messages yet</p>
                <p class="text-sm text-gray-500 mt-2">Messages from users about your notes and questions will appear here</p>
            </div>
            
            <div id="conversationsList" class="space-y-4 hidden"></div>
        </div>
    </aside>
    
    <script>
    // Messages Drawer Management
    const messagesDrawer = document.getElementById('messagesDrawer');
    const messagesBackdrop = document.getElementById('messagesBackdrop');
    const openMessagesBtn = document.getElementById('openMessagesDrawer');
    const closeMessagesBtn = document.getElementById('closeMessagesDrawer');
    const messagesBadge = document.getElementById('messagesBadge');
    
    let conversations = [];
    let activeConversation = null;
    
    // Open drawer
    openMessagesBtn.addEventListener('click', () => {
        messagesBackdrop.classList.remove('hidden');
        messagesDrawer.classList.remove('translate-x-full');
        loadMessages();
    });
    
    // Close drawer
    function closeDrawer() {
        messagesDrawer.classList.add('translate-x-full');
        setTimeout(() => {
            messagesBackdrop.classList.add('hidden');
        }, 300);
    }
    
    closeMessagesBtn.addEventListener('click', closeDrawer);
    messagesBackdrop.addEventListener('click', closeDrawer);
    
    // Load unread count
    async function loadUnreadCount() {
        try {
            const response = await fetch('api/get_unread_count.php');
            const result = await response.json();
            
            if (result.success && result.count > 0) {
                messagesBadge.textContent = result.count;
                messagesBadge.classList.remove('hidden');
            } else {
                messagesBadge.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error loading unread count:', error);
        }
    }
    
    // Load messages
    async function loadMessages() {
        const loading = document.getElementById('messagesLoading');
        const empty = document.getElementById('messagesEmpty');
        const list = document.getElementById('conversationsList');
        
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        list.classList.add('hidden');
        
        try {
            const response = await fetch('api/get_messages.php');
            const result = await response.json();
            
            loading.classList.add('hidden');
            
            if (result.success && result.conversations.length > 0) {
                conversations = result.conversations;
                renderConversations();
                list.classList.remove('hidden');
            } else {
                empty.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }
    
    // Render conversations
    function renderConversations() {
        const list = document.getElementById('conversationsList');
        list.innerHTML = '';
        
        conversations.forEach((conv, index) => {
            const isNote = conv.resource_type === 'note';
            const badgeColor = isNote ? 'purple' : 'blue';
            const lastMessage = conv.messages[conv.messages.length - 1];
            
            const convDiv = document.createElement('div');
            convDiv.className = 'bg-gray-900 rounded-xl border border-gray-700 overflow-hidden hover:border-' + badgeColor + '-500 transition-all duration-300';
            
            convDiv.innerHTML = `
                <div class="p-4 cursor-pointer" onclick="toggleConversation(${index})">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-${badgeColor}-500/20 text-${badgeColor}-400 border border-${badgeColor}-500">
                                    ${conv.resource_type === 'note' ? 'Note' : 'Question'}
                                </span>
                                ${conv.unread_count > 0 ? `<span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">${conv.unread_count} new</span>` : ''}
                            </div>
                            <h4 class="text-white font-semibold line-clamp-1">${escapeHtml(conv.resource_title)}</h4>
                            <p class="text-gray-400 text-sm">${escapeHtml(conv.course_code)}</p>
                        </div>
                        <span class="material-icons-round text-gray-500 transform transition-transform" id="chevron-${index}">
                            expand_more
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        <span class="material-icons-round text-xs align-middle">person</span>
                        Conversation with ${escapeHtml(conv.other_user_name)}
                    </p>
                </div>
                
                <div id="conversation-${index}" class="hidden border-t border-gray-700">
                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto bg-gray-950">
                        ${renderMessages(conv.messages)}
                    </div>
                    <div class="p-4 border-t border-gray-700 bg-gray-900">
                        <div class="flex gap-2">
                            <input type="text" id="reply-${index}" placeholder="Type your reply..." 
                                   class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-${badgeColor}-500 text-sm"
                                   onkeypress="if(event.key==='Enter') sendReply(${index})">
                            <button onclick="sendReply(${index})" 
                                    class="px-4 py-2 rounded-lg bg-${badgeColor}-600 hover:bg-${badgeColor}-700 text-white transition flex items-center gap-1">
                                <span class="material-icons-round text-sm">send</span>
                                Reply
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            list.appendChild(convDiv);
        });
    }
    
    // Render messages
    function renderMessages(messages) {
        return messages.map(msg => {
            const isOwn = msg.sender_id == <?= $_SESSION['user_id'] ?>;
            const alignment = isOwn ? 'ml-auto' : 'mr-auto';
            const bgColor = isOwn ? 'bg-purple-600' : 'bg-gray-800';
            const time = new Date(msg.created_at).toLocaleString('en-US', { 
                month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' 
            });
            
            return `
                <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
                    <div class="${alignment} max-w-[80%]">
                        <div class="${bgColor} rounded-2xl px-4 py-2 shadow-lg">
                            <p class="text-xs text-gray-300 mb-1 font-medium">${escapeHtml(msg.sender_name)}</p>
                            <p class="text-white text-sm">${escapeHtml(msg.message)}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 ${isOwn ? 'text-right' : 'text-left'}">${time}</p>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Toggle conversation
    window.toggleConversation = async function(index) {
        const convDiv = document.getElementById(`conversation-${index}`);
        const chevron = document.getElementById(`chevron-${index}`);
        const conv = conversations[index];
        
        if (convDiv.classList.contains('hidden')) {
            // Close all other conversations
            document.querySelectorAll('[id^="conversation-"]').forEach(el => {
                if (el.id !== `conversation-${index}`) {
                    el.classList.add('hidden');
                }
            });
            document.querySelectorAll('[id^="chevron-"]').forEach(el => {
                if (el.id !== `chevron-${index}`) {
                    el.classList.remove('rotate-180');
                }
            });
            
            convDiv.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            
            // Mark as read
            if (conv.unread_count > 0) {
                await markAsRead(conv.resource_id, conv.resource_type);
                conv.unread_count = 0;
                loadUnreadCount();
                renderConversations();
            }
        } else {
            convDiv.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    };
    
    // Mark messages as read
    async function markAsRead(resourceId, resourceType) {
        try {
            const formData = new FormData();
            formData.append('resource_id', resourceId);
            formData.append('resource_type', resourceType);
            
            await fetch('api/mark_messages_read.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }
    
    // Send reply
    window.sendReply = async function(index) {
        const input = document.getElementById(`reply-${index}`);
        const message = input.value.trim();
        
        if (!message) return;
        
        const conv = conversations[index];
        const lastMessage = conv.messages[conv.messages.length - 1];
        
        try {
            const formData = new FormData();
            formData.append('resource_id', conv.resource_id);
            formData.append('resource_type', conv.resource_type);
            formData.append('message', message);
            formData.append('parent_id', lastMessage.id);
            
            const response = await fetch('api/send_message.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                input.value = '';
                // Reload messages to show the new reply
                await loadMessages();
                // Re-open the conversation
                setTimeout(() => toggleConversation(index), 100);
            } else {
                alert(result.message || 'Failed to send reply');
            }
        } catch (error) {
            console.error('Error sending reply:', error);
            alert('Network error. Please try again.');
        }
    };
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Load unread count on page load
    loadUnreadCount();
    
    // Refresh unread count every 30 seconds
    setInterval(loadUnreadCount, 30000);
    </script>
    <?php endif; ?>
    
</body>
</html>


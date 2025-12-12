<?php
session_start();
require 'config.php';
require 'functions.php';
require 'security.php';

validate_session();

$isLoggedIn = isLoggedIn();
$currentUser = $isLoggedIn ? getUser() : null;

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'toggle_like') {
    if (!$isLoggedIn) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $resource_id = $_POST['resource_id'];
    $resource_type = 'question';
    
    // Check if already liked
    if (hasLiked($_SESSION['user_id'], $resource_id, $resource_type)) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND resource_id = ? AND resource_type = ?");
        $stmt->execute([$_SESSION['user_id'], $resource_id, $resource_type]);
        $liked = false;
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, resource_id, resource_type) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $resource_id, $resource_type]);
        $liked = true;
    }
    
    $likeCount = getLikeCount($resource_id, $resource_type);
    echo json_encode(['success' => true, 'liked' => $liked, 'count' => $likeCount]);
    exit;
}

// Fetch all questions
$search = $_GET['search'] ?? '';
$semester = $_GET['semester'] ?? '';
$year = $_GET['year'] ?? '';

$sql = "SELECT * FROM questions WHERE is_public = 1";
$params = [];

if ($search) {
    $sql .= " AND (title LIKE ? OR course_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($semester) {
    $sql .= " AND semester = ?";
    $params[] = $semester;
}

if ($year) {
    $sql .= " AND year = ?";
    $params[] = $year;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions - Weby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/animations.css">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="w-full mx-auto px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold text-purple-500">Weby</a>
                
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-gray-300 hover:text-white transition">Home</a>
                    <a href="notes.php" class="text-gray-300 hover:text-white transition">Notes</a>
                    <a href="questions.php" class="text-white bg-gray-700 px-8 py-1.5 rounded-lg">Questions</a>
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
    
    <!-- Main Content -->
    <main class="w-full mx-auto px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold">Shared Questions</h1>
            
            <div class="flex items-center gap-3">
                <!-- Search Toggle Button -->
                <button id="toggleSearchBtn" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-lg transition flex items-center justify-center">
                    <span class="material-icons-round">search</span>
                </button>
                
                <?php if ($isLoggedIn): ?>
                    <a href="upload-question.php" class="bg-blue-600 hover:bg-[#0a0a2e] text-white px-8 py-3 rounded-lg flex items-center gap-2 transition border border-transparent hover:border-white">
                        <span class="material-icons-round">upload</span>
                        Upload Question
                    </a>
                <?php else: ?>
                    <a href="login.php" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg flex items-center gap-2 transition">
                        <span class="material-icons-round">login</span>
                        Login to Upload
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Search & Filters (Collapsible) -->
        <div id="filterBar" class="bg-gray-800 p-6 rounded-xl border border-gray-700 mb-8 overflow-hidden transition-all duration-300" style="display: none;">
            <div class="flex items-center justify-between gap-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                    <input type="text" id="searchInput" placeholder="Search by title, course code..." 
                           class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    
                    <select id="examTypeFilter" class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Exam Types</option>
                        <option value="Quiz">Quiz</option>
                        <option value="Mid">Mid</option>
                        <option value="Final">Final</option>
                    </select>
                    
                    <select id="semesterFilter" class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Semesters</option>
                        <option value="Spring">Spring</option>
                        <option value="Summer">Summer</option>
                        <option value="Fall">Fall</option>
                    </select>
                    
                    <select id="yearFilter" class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Years</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                
                <button id="clearFiltersBtn" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg transition flex items-center justify-center gap-2 whitespace-nowrap">
                    <span class="material-icons-round text-sm">close</span>
                    Clear Filters
                </button>
            </div>
        </div>
        
        <!-- Questions Grid -->
        <?php if (empty($questions)): ?>
            <div class="text-center py-16">
                <span class="material-icons-round text-6xl text-gray-600 mb-4">folder_open</span>
                <p class="text-xl text-gray-400">No questions found. Be the first to upload!</p>
            </div>
        <?php else: ?>
            <div id="questionsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($questions as $question): ?>
                    <?php 
                        $uploaderName = getUploaderName($question['user_id']);
                        $likeCount = getLikeCount($question['id'], 'question');
                        $isLiked = $isLoggedIn && hasLiked($_SESSION['user_id'], $question['id'], 'question');
                        $isBookmarked = $isLoggedIn && hasBookmarked($_SESSION['user_id'], $question['id'], 'question');
                        $downloadCount = getDownloadCount($question['id'], 'question');
                    ?>
                    <div onclick="window.location.href='view.php?type=question&id=<?= $question['id'] ?>'" class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-blue-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-blue-500/20 cursor-pointer card-animate hover-lift">
                        <!-- Card Header with Course Code -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5">
                            <h3 class="text-base font-bold text-white line-clamp-1"><?= htmlspecialchars($question['title']) ?></h3>
                            <p class="text-blue-200 text-xs font-medium mt-0.5"><?= htmlspecialchars($question['course_code']) ?></p>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="p-4">
                            <!-- Semester/Year with Calendar Icon -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2 text-gray-300">
                                    <span class="material-icons-round text-lg">calendar_today</span>
                                    <span class="text-sm font-medium">
                                        <?= htmlspecialchars($question['semester'] . ' ' . $question['year']) ?>
                                    </span>
                                </div>
                                
                                <!-- Exam Type Badge -->
                                <?php
                                $examType = $question['exam_type'] ?? 'Exam';
                                $badgeColors = [
                                    'Quiz' => 'bg-green-500/20 text-green-400 border-green-500',
                                    'Mid' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                    'Final' => 'bg-orange-500/20 text-orange-400 border-orange-500'
                                ];
                                $badgeColor = $badgeColors[$examType] ?? 'bg-gray-500/20 text-gray-400 border-gray-500';
                                ?>
                                <span class="px-2 py-1 rounded text-xs font-semibold border <?= $badgeColor ?>">
                                    <?= htmlspecialchars($examType) ?>
                                </span>
                            </div>
                            
                            <!-- Uploader Info -->
                            <div class="flex items-center gap-2 mb-4 text-gray-300">
                                <span class="material-icons-round text-lg">person</span>
                                <span class="text-sm">By <a href="profile.php?user_id=<?= $question['user_id'] ?>" onclick="event.stopPropagation()" class="text-blue-400 hover:text-blue-500 underline transition"><?= htmlspecialchars($uploaderName) ?></a></span>
                            </div>
                            
                            <!-- Like and Download Count -->
                            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-700">
                                <button onclick="event.stopPropagation(); toggleLike(<?= $question['id'] ?>, this)" 
                                        class="like-btn flex items-center gap-1 <?= $isLiked ? 'text-red-500' : 'text-gray-400' ?> hover:text-red-500 transition"
                                        data-question-id="<?= $question['id'] ?>">
                                    <span class="material-icons-round text-lg"><?= $isLiked ? 'favorite' : 'favorite_border' ?></span>
                                    <span class="like-count text-sm font-medium"><?= $likeCount ?></span>
                                </button>
                                <div class="flex items-center gap-1 text-gray-400">
                                    <span class="material-icons-round text-lg">download</span>
                                    <span class="download-count text-sm font-medium" data-question-id="<?= $question['id'] ?>"><?= $downloadCount ?></span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2 items-center">
                                <button onclick="event.stopPropagation(); toggleBookmark(<?= $question['id'] ?>, 'question', this)" 
                                        class="p-2 rounded-full transition-colors duration-200 <?= $isBookmarked ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500' ?>" title="Bookmark">
                                    <span class="material-icons-round text-2xl"><?= $isBookmarked ? 'bookmark' : 'bookmark_border' ?></span>
                                </button>
                                <?php if ($isLoggedIn): ?>
                                <button onclick="event.stopPropagation(); openMessageModal(<?= $question['id'] ?>, 'question')" 
                                        class="p-2 rounded-full transition-colors duration-200 text-gray-400 hover:text-blue-500" title="Ask a question">
                                    <span class="material-icons-round text-2xl">chat_bubble_outline</span>
                                </button>
                                <?php endif; ?>
                                <a href="view.php?type=question&id=<?= $question['id'] ?>" onclick="event.stopPropagation()" 
                                   class="flex-1 bg-blue-600 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                                    View
                                </a>
                                <a href="<?= htmlspecialchars($question['file_path']) ?>" download 
                                   onclick="event.stopPropagation(); trackDownload(<?= $question['id'] ?>, 'question', this)"
                                   class="flex-1 bg-gray-700 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <script>
        function toggleLike(noteId, button) {
            <?php if (!$isLoggedIn): ?>
                alert('Please login to like questions');
                window.location.href = 'login.php';
                return;
            <?php endif; ?>
            
            fetch('questions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle_like&resource_id=${noteId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = button.querySelector('.material-icons-round');
                    const count = button.querySelector('.like-count');
                    
                    if (data.liked) {
                        icon.textContent = 'favorite';
                        button.classList.add('text-red-500');
                    } else {
                        icon.textContent = 'favorite_border';
                        button.classList.remove('text-red-500');
                    }
                    
                    count.textContent = data.count;
                }
            });
        }

        // Toggle Bookmark
        function toggleBookmark(resourceId, resourceType, btn) {
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
                    const icon = btn.querySelector('.material-icons-round');
                    if (data.is_bookmarked) {
                        icon.textContent = 'bookmark';
                        btn.classList.add('text-purple-500');
                        btn.classList.remove('text-gray-400');
                    } else {
                        icon.textContent = 'bookmark_border';
                        btn.classList.remove('text-purple-500');
                        btn.classList.add('text-gray-400');
                    }
                } else if (data.message === 'Please login first') {
                    window.location.href = 'login.php';
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Toggle Search Bar
        const toggleSearchBtn = document.getElementById('toggleSearchBtn');
        const filterBar = document.getElementById('filterBar');
        let isFilterBarVisible = false;
        
        toggleSearchBtn.addEventListener('click', function() {
            isFilterBarVisible = !isFilterBarVisible;
            if (isFilterBarVisible) {
                filterBar.style.display = 'block';
                setTimeout(() => {
                    filterBar.style.opacity = '1';
                }, 10);
            } else {
                filterBar.style.opacity = '0';
                setTimeout(() => {
                    filterBar.style.display = 'none';
                }, 300);
            }
        });
        
        // AJAX Search Functionality
        const searchInput = document.getElementById('searchInput');
        const examTypeFilter = document.getElementById('examTypeFilter');
        const semesterFilter = document.getElementById('semesterFilter');
        const yearFilter = document.getElementById('yearFilter');
        const questionsGrid = document.getElementById('questionsGrid');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        
        let searchTimeout;
        
        function performSearch() {
            const search = searchInput.value;
            const examType = examTypeFilter.value;
            const semester = semesterFilter.value;
            const year = yearFilter.value;
            
            const params = new URLSearchParams({
                search: search,
                exam_type: examType,
                semester: semester,
                year: year
            });
            
            fetch('search_questions_ajax.php?' + params.toString())
                .then(response => response.text())
                .then(html => {
                    questionsGrid.innerHTML = html;
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }
        
        // Debounced search on input
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });
        
        // Immediate search on filter change
        examTypeFilter.addEventListener('change', performSearch);
        semesterFilter.addEventListener('change', performSearch);
        yearFilter.addEventListener('change', performSearch);
        
        // Clear filters
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            examTypeFilter.value = '';
            semesterFilter.value = '';
            yearFilter.value = '';
            performSearch();
        });
        
        // Track download
        window.trackDownload = async function(resourceId, resourceType, element) {
            try {
                const formData = new FormData();
                formData.append('resource_id', resourceId);
                formData.append('resource_type', resourceType);
                
                const response = await fetch('track-download.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update download count in UI
                    const countElement = document.querySelector(`.download-count[data-question-id="${resourceId}"]`);
                    if (countElement) {
                        countElement.textContent = result.download_count;
                    }
                }
            } catch (error) {
                console.error('Download tracking error:', error);
            }
        };
        
        // Stagger card animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-animate');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>

    <!-- Ask Gemini Floating Button -->
    <div class="fixed bottom-6 right-6 z-40">
        <button id="askGeminiBtn" class="flex items-center gap-2 bg-gray-800 border border-gray-700 hover:border-purple-500 text-white px-4 py-3 rounded-full shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 group">
            <span class="material-icons-round text-purple-500 group-hover:scale-110 transition-transform text-xl">auto_awesome</span>
            <span class="font-medium text-sm">Ask Gemini</span>
        </button>
    </div>

    <!-- Backdrop for drawer -->
    <div id="geminiBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden"></div>

    <!-- Right Drawer -->
    <aside id="geminiDrawer" class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-gray-800 border-l border-gray-700 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between bg-gray-900">
            <div>
                <h2 class="text-base font-semibold text-white">Ask Gemini AI</h2>
                <p class="text-xs text-gray-400 mt-0.5">Your AI study assistant</p>
            </div>
            <button id="closeGeminiDrawer" class="h-9 w-9 rounded-full flex items-center justify-center bg-gray-800 border border-gray-700 text-gray-400 hover:text-white hover:bg-gray-700 transition">
                <span class="material-icons-round text-lg">close</span>
            </button>
        </div>

        <!-- Suggested Prompts -->
        <div class="px-5 pt-3 pb-3 border-b border-gray-700">
            <p class="text-gray-400 text-xs mb-2">Quick prompts:</p>
            <div class="flex flex-wrap gap-2">
                <button data-gemini-suggest="Explain this concept in simple terms" class="px-3 py-1.5 rounded-full bg-gray-800 border border-gray-700 hover:border-purple-500 hover:text-white text-xs text-gray-300 transition">
                    Explain concept
                </button>
                <button data-gemini-suggest="What are the key points to remember?" class="px-3 py-1.5 rounded-full bg-gray-800 border border-gray-700 hover:border-purple-500 hover:text-white text-xs text-gray-300 transition">
                    Key points
                </button>
                <button data-gemini-suggest="Create 3 practice questions for this topic" class="px-3 py-1.5 rounded-full bg-gray-800 border border-gray-700 hover:border-purple-500 hover:text-white text-xs text-gray-300 transition">
                    Practice questions
                </button>
                <button data-gemini-suggest="How does this relate to the exam syllabus?" class="px-3 py-1.5 rounded-full bg-gray-800 border border-gray-700 hover:border-purple-500 hover:text-white text-xs text-gray-300 transition">
                    Exam relevance
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="geminiMessages" class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
            <div class="text-sm text-gray-400 text-center py-8">
                <span class="material-icons-round text-4xl text-purple-500 mb-2 block">auto_awesome</span>
                <p>Start a conversation!</p>
                <p class="text-xs mt-1">I can help you understand concepts, summarize notes, and more.</p>
            </div>
        </div>

        <!-- Input Form -->
        <form id="geminiForm" class="px-4 py-4 border-t border-gray-700 bg-gray-900">
            <div class="flex items-end gap-2">
                <textarea id="geminiInput" rows="1" class="flex-1 resize-none bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Ask about questions, concepts, or any topic..."></textarea>
                <button type="submit" id="geminiSendBtn" class="h-10 w-10 rounded-full bg-purple-600 hover:bg-purple-700 flex items-center justify-center text-white shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-icons-round text-lg">send</span>
                </button>
            </div>
            <p id="geminiStatus" class="mt-2 text-xs text-gray-500"></p>
        </form>
    </aside>

    <!-- Gemini AI Script -->
    <script src="js/gemini.js"></script>
    
    <!-- Messages Script -->
    <script src="js/messages.js"></script>
    
    <?php if ($isLoggedIn): ?>
    <script>
    // Load unread count for navbar badge
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

<?php
session_start();
require 'config.php';
require 'functions.php';
require 'security.php';

validate_session();

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? 0;

if (!in_array($type, ['note', 'question']) || !$id) {
    header("Location: index.php");
    exit;
}

$table = $type == 'note' ? 'notes' : 'questions';

// Fetch resource
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$id]);
$resource = $stmt->fetch();

if (!$resource) {
    header("Location: index.php");
    exit;
}

$uploaderName = getUploaderName($resource['user_id']);
$likeCount = getLikeCount($id, $type);
$downloadCount = getDownloadCount($id, $type);
$isLoggedIn = isLoggedIn();
$isLiked = $isLoggedIn && hasLiked($_SESSION['user_id'], $id, $type);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resource['title']) ?> - Weby</title>
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
    <main class="mx-auto px-8 py-12 max-w-4xl">
        <!-- Top Back Button -->
        <div class="flex justify-end mb-4">
            <a href="<?= $type == 'note' ? 'notes.php' : 'questions.php' ?>" 
               class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition flex items-center gap-2 border border-gray-700 hover:border-purple-500">
                <span class="material-icons-round">arrow_back</span>
                Back
            </a>
        </div>
        
        <div class="bg-gray-800 rounded-2xl border border-gray-700 p-8">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-icons-round text-4xl text-purple-500">
                            <?= $type == 'note' ? 'description' : 'help_outline' ?>
                        </span>
                        <h1 class="text-3xl font-bold"><?= htmlspecialchars($resource['title']) ?></h1>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <button onclick="toggleLike()" id="likeBtn"
                            class="<?= $isLiked ? 'text-red-500' : 'text-gray-400' ?> hover:text-red-500 transition flex items-center gap-2">
                        <span class="material-icons-round text-3xl"><?= $isLiked ? 'favorite' : 'favorite_border' ?></span>
                        <span id="likeCount" class="text-xl font-semibold"><?= $likeCount ?></span>
                    </button>
                    <div class="flex items-center gap-2 text-gray-400">
                        <span class="material-icons-round text-3xl">download</span>
                        <span id="downloadCount" class="text-xl font-semibold"><?= $downloadCount ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Details -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <p class="text-sm text-gray-400 mb-1">Course Code</p>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($resource['course_code']) ?></p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-400 mb-1">Semester</p>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($resource['semester']) ?></p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-400 mb-1">Year</p>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($resource['year']) ?></p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-400 mb-1">Uploaded By</p>
                    <p class="text-lg font-semibold">
                        <a href="profile.php?user_id=<?= $resource['user_id'] ?>" class="text-purple-400 hover:text-purple-500 hover:underline transition">
                            <?= htmlspecialchars($uploaderName) ?>
                        </a>
                    </p>
                </div>
                
                <?php if ($type == 'note' && !empty($resource['teacher'])): ?>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Course Teacher</p>
                    <p class="text-lg font-semibold"><?= htmlspecialchars($resource['teacher']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-8">
                <p class="text-sm text-gray-400 mb-1">Uploaded On</p>
                <p class="text-lg"><?= formatDate($resource['created_at']) ?></p>
            </div>
            
            <!-- Description Section -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-2xl">description</span>
                    <h2 class="text-xl font-bold">Description</h2>
                </div>
                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                    <p class="text-gray-300">No description was provided for this <?= $type ?>.</p>
                </div>
            </div>
            
            <!-- PDF Preview Section -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-2xl">visibility</span>
                    <h2 class="text-xl font-bold"><?= ucfirst($type) ?> Preview</h2>
                </div>
                <div class="bg-gray-900 rounded-lg border border-gray-700 overflow-hidden" style="height: 600px;">
                    <?php
                    $fileExtension = strtolower(pathinfo($resource['file_path'], PATHINFO_EXTENSION));
                    if ($fileExtension === 'pdf'):
                    ?>
                        <iframe 
                            src="<?= htmlspecialchars($resource['file_path']) ?>#toolbar=0&navpanes=0&scrollbar=1" 
                            class="w-full h-full"
                            frameborder="0"
                            style="border: none;">
                        </iframe>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <span class="material-icons-round text-6xl mb-4">picture_as_pdf</span>
                            <p class="text-lg">PDF preview is not available for this file type.</p>
                            <p class="text-sm mt-2">Please download the file to view it.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4">
                <a href="<?= $type == 'note' ? 'notes.php' : 'questions.php' ?>"
                   class="flex-1 text-gray-400 hover:text-purple-500 px-8 py-4 rounded-lg text-center font-semibold transition flex items-center justify-center gap-2">
                    <span class="material-icons-round">arrow_back</span>
                    Back to <?= $type == 'note' ? 'Notes' : 'Questions' ?>
                </a>
                
                <a href="<?= htmlspecialchars($resource['file_path']) ?>" download
                   onclick="trackDownload(<?= $id ?>, '<?= $type ?>', this)"
                   class="flex-1 bg-purple-600 hover:bg-[#0a0a2e] text-white px-8 py-4 rounded-lg text-center font-semibold transition-all duration-200 flex items-center justify-center gap-2 border border-transparent hover:border-white">
                    <span class="material-icons-round">download</span>
                    Download PDF
                </a>
            </div>
        </div>
    </main>
    
    <script>
        function toggleLike() {
            <?php if (!$isLoggedIn): ?>
                alert('Please login to like resources');
                window.location.href = 'login.php';
                return;
            <?php endif; ?>
            
            const type = '<?= $type ?>';
            const id = <?= $id ?>;
            
            fetch(`${type == 'note' ? 'notes' : 'questions'}.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=toggle_like&resource_id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById('likeBtn');
                    const icon = btn.querySelector('.material-icons-round');
                    const count = document.getElementById('likeCount');
                    
                    if (data.liked) {
                        icon.textContent = 'favorite';
                        btn.classList.add('text-red-500');
                        btn.classList.remove('text-gray-400');
                    } else {
                        icon.textContent = 'favorite_border';
                        btn.classList.remove('text-red-500');
                        btn.classList.add('text-gray-400');
                    }
                    
                    count.textContent = data.count;
                }
            });
        }
        
        // Track download
        async function trackDownload(resourceId, resourceType, element) {
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
                    const countElement = document.getElementById('downloadCount');
                    if (countElement) {
                        countElement.textContent = result.download_count;
                    }
                }
            } catch (error) {
                console.error('Download tracking error:', error);
            }
        }
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
                <textarea id="geminiInput" rows="1" class="flex-1 resize-none bg-gray-800 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Ask about this content or any topic..."></textarea>
                <button type="submit" id="geminiSendBtn" class="h-10 w-10 rounded-full bg-purple-600 hover:bg-purple-700 flex items-center justify-center text-white shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-icons-round text-lg">send</span>
                </button>
            </div>
            <p id="geminiStatus" class="mt-2 text-xs text-gray-500"></p>
        </form>
    </aside>

    <!-- Gemini AI Script -->
    <script src="js/gemini.js"></script>

</body>
</html>

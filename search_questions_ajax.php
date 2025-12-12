<?php
session_start();
require 'config.php';
require 'functions.php';

$isLoggedIn = isLoggedIn();

// Get filter parameters
$search = $_GET['search'] ?? '';
$exam_type = $_GET['exam_type'] ?? '';
$semester = $_GET['semester'] ?? '';
$year = $_GET['year'] ?? '';

// Build query
$sql = "SELECT * FROM questions WHERE is_public = 1";
$params = [];

if ($search) {
    $sql .= " AND (title LIKE ? OR course_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($exam_type) {
    $sql .= " AND exam_type = ?";
    $params[] = $exam_type;
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

// Return only the cards HTML
if (empty($questions)):
?>
    <div class="col-span-full text-center py-16">
        <span class="material-icons-round text-6xl text-gray-600 mb-4 block">folder_open</span>
        <p class="text-xl text-gray-400">No questions found. Try adjusting your filters.</p>
    </div>
<?php else: ?>
    <?php foreach ($questions as $question): ?>
        <?php 
            $uploaderName = getUploaderName($question['user_id']);
            $likeCount = getLikeCount($question['id'], 'question');
            $isLiked = $isLoggedIn && hasLiked($_SESSION['user_id'], $question['id'], 'question');
            $isBookmarked = $isLoggedIn && hasBookmarked($_SESSION['user_id'], $question['id'], 'question');
        ?>
        <div onclick="window.location.href='view.php?type=question&id=<?= $question['id'] ?>'" class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-blue-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-blue-500/20 cursor-pointer">
            <!-- Card Header with Course Code -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                <h3 class="text-lg font-bold text-white line-clamp-1"><?= htmlspecialchars($question['title']) ?></h3>
                <p class="text-blue-200 text-sm font-medium mt-1"><?= htmlspecialchars($question['course_code']) ?></p>
            </div>
            
            <!-- Card Body -->
            <div class="p-5">
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
                        <span class="text-sm font-medium">18</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2 items-center">
                    <button onclick="event.stopPropagation(); toggleBookmark(<?= $question['id'] ?>, 'question', this)" 
                            class="p-2 rounded-full transition-colors duration-200 <?= $isBookmarked ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500' ?>">
                        <span class="material-icons-round text-2xl"><?= $isBookmarked ? 'bookmark' : 'bookmark_border' ?></span>
                    </button>
                    <a href="view.php?type=question&id=<?= $question['id'] ?>" onclick="event.stopPropagation()" 
                       class="flex-1 bg-blue-600 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                        View
                    </a>
                    <a href="<?= htmlspecialchars($question['file_path']) ?>" download onclick="event.stopPropagation()"
                       class="flex-1 bg-gray-700 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                        Download
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

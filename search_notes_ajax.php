<?php
session_start();
require 'config.php';
require 'functions.php';

$isLoggedIn = isLoggedIn();

// Get filter parameters
$search = $_GET['search'] ?? '';
$semester = $_GET['semester'] ?? '';
$year = $_GET['year'] ?? '';

// Build query
$sql = "SELECT * FROM notes WHERE is_public = 1";
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
$notes = $stmt->fetchAll();

// Return only the cards HTML
if (empty($notes)):
?>
    <div class="col-span-full text-center py-16">
        <span class="material-icons-round text-6xl text-gray-600 mb-4 block">folder_open</span>
        <p class="text-xl text-gray-400">No notes found. Try adjusting your filters.</p>
    </div>
<?php else: ?>
    <?php foreach ($notes as $note): ?>
        <?php 
            $uploaderName = getUploaderName($note['user_id']);
            $likeCount = getLikeCount($note['id'], 'note');
            $isLiked = $isLoggedIn && hasLiked($_SESSION['user_id'], $note['id'], 'note');
            $isBookmarked = $isLoggedIn && hasBookmarked($_SESSION['user_id'], $note['id'], 'note');
        ?>
        <div onclick="window.location.href='view.php?type=note&id=<?= $note['id'] ?>'" class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl border border-gray-700 hover:border-purple-500 transition-all duration-300 overflow-hidden shadow-lg hover:shadow-purple-500/20 cursor-pointer">
            <!-- Card Header with Course Code -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-3">
                <h3 class="text-lg font-bold text-white line-clamp-1"><?= htmlspecialchars($note['title']) ?></h3>
                <p class="text-purple-200 text-sm font-medium mt-1"><?= htmlspecialchars($note['course_code']) ?></p>
            </div>
            
            <!-- Card Body -->
            <div class="p-5">
                <!-- Semester/Year with Calendar Icon -->
                <div class="flex items-center gap-2 mb-3 text-gray-300">
                    <span class="material-icons-round text-lg">calendar_today</span>
                    <span class="text-sm font-medium">
                        <?= htmlspecialchars($note['semester'] . ' ' . $note['year']) ?>
                    </span>
                </div>
                
                <!-- Course Teacher -->
                <div class="flex items-center gap-2 mb-3 text-gray-300">
                    <span class="material-icons-round text-lg">school</span>
                    <span class="text-sm"><?= htmlspecialchars($note['teacher'] ?? 'Course Teacher') ?></span>
                </div>
                
                <!-- Uploader Info -->
                <div class="flex items-center gap-2 mb-4 text-gray-300">
                    <span class="material-icons-round text-lg">person</span>
                    <span class="text-sm">By <a href="profile.php?user_id=<?= $note['user_id'] ?>" onclick="event.stopPropagation()" class="text-purple-400 hover:text-purple-500 underline transition"><?= htmlspecialchars($uploaderName) ?></a></span>
                </div>
                
                <!-- Like and Download Count -->
                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-700">
                    <button onclick="event.stopPropagation(); toggleLike(<?= $note['id'] ?>, this)" 
                            class="like-btn flex items-center gap-1 <?= $isLiked ? 'text-red-500' : 'text-gray-400' ?> hover:text-red-500 transition"
                            data-note-id="<?= $note['id'] ?>">
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
                    <button onclick="event.stopPropagation(); toggleBookmark(<?= $note['id'] ?>, 'note', this)" 
                            class="p-2 rounded-full transition-colors duration-200 <?= $isBookmarked ? 'text-purple-500' : 'text-gray-400 hover:text-purple-500' ?>">
                        <span class="material-icons-round text-2xl"><?= $isBookmarked ? 'bookmark' : 'bookmark_border' ?></span>
                    </button>
                    <a href="view.php?type=note&id=<?= $note['id'] ?>" onclick="event.stopPropagation()" 
                       class="flex-1 bg-purple-600 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                        View
                    </a>
                    <a href="<?= htmlspecialchars($note['file_path']) ?>" download onclick="event.stopPropagation()"
                       class="flex-1 bg-gray-700 hover:bg-[#0a0a2e] text-white px-4 py-2.5 rounded-lg text-center transition-all duration-200 text-sm font-medium border border-transparent hover:border-white">
                        Download
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// article.php
session_start();

// Include all classes manually
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Article.php';
require_once 'classes/Category.php';
require_once 'classes/Comment.php';

// Helper function
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Create class objects
$article = new Article();
$comment = new Comment();

// Get article ID
$article_id = $_GET['id'] ?? 0;

// Get article
$art = $article->getById($article_id);

if (!$art) {
    header('Location: index.php');
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $content = $_POST['content'] ?? '';
    
    // Get the username if user is logged in, otherwise set to NULL for guest
    if (User::isLoggedIn()) {
        $author_username = User::getCurrentUsername(); // This gets the actual username
    } else {
        $author_username = null; // NULL for guest comments
    }
    
    if (!empty($content)) {
        $comment->add($content, $author_username, $article_id);
        header("Location: article.php?id=$article_id&commented=1");
        exit;
    }
}

// Handle comment deletion
if (isset($_GET['delete_comment']) && User::isAdmin()) {
    $comment_id = $_GET['delete_comment'];
    $comment->delete($comment_id);
    header("Location: article.php?id=$article_id&deleted_comment=1");
    exit;
}

// Check if current user is the author of this article
$current_username = User::getCurrentUsername();
$is_author = User::isLoggedIn() && ($current_username === $art['author_username']);

// Get comments
$comments = $comment->getByArticle($article_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($art['title']); ?> - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<!-- Navigation Bar -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">
            <a href="index.php">BlogCMS</a>
        </h1>
        <div class="flex items-center gap-4">
            <?php if (!User::isLoggedIn()): ?>
                <a href="login.php"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    Login
                </a>
            <?php else: ?>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">
                        Welcome, <span class="font-semibold"><?php echo escape(User::currentUser()['name']); ?></span>
                    </span>
                    <?php if (User::isAuthor()): ?>
                        <a href="my_articles.php"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            My Articles
                        </a>
                    <?php endif; ?>
                    <?php if (User::isAdmin()): ?>
                        <a href="admin.php"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                            Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="logout.php"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                            Logout
                        </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Success Messages -->
    <?php if (isset($_GET['commented'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Comment added successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted_comment'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Comment deleted successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Article updated successfully!
        </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo escape($art['title']); ?></h1>
                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                    <?php echo escape($art['category_name']); ?>
                </span>
            </div>
            
            <!-- Article Actions -->
            <?php if (User::isAdmin() || $is_author): ?>
                <div class="flex gap-4">
                    <?php if ($is_author || User::isAdmin()): ?>
                        <!-- Edit button -->
                        <a href="edit_article.php?id=<?php echo $art['article_id']; ?>" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                            Edit
                        </a>
                        
                        <!-- Delete button -->
                        <a href="my_articles.php?delete_article=<?php echo $art['article_id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this article?')"
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                            Delete
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4 flex items-center gap-4 text-sm text-gray-600">
            <span>By <span class="font-semibold"><?php echo escape($art['author_name'] ?? $art['author_username']); ?></span></span>
            <span><?php echo date('Y-m-d', strtotime($art['create_date'])); ?></span>
            <?php if ($art['modify_date']): ?>
                <span class="text-xs text-gray-500">(Updated: <?php echo date('Y-m-d', strtotime($art['modify_date'])); ?>)</span>
            <?php endif; ?>
        </div>

        <!-- Article Status Badge (if not published) -->
        <?php if ($art['article_status'] !== 'Published'): ?>
            <div class="mb-4">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                    <?php echo ucfirst($art['article_status']); ?>
                </span>
                <?php if ($is_author): ?>
                    <span class="text-sm text-gray-500 ml-2">(Only you can see this)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="text-gray-700 mb-8 leading-relaxed whitespace-pre-line">
            <?php echo nl2br(escape($art['content'])); ?>
        </div>

        <div class="flex justify-between items-center">
            <a href="index.php"
                class="inline-block text-blue-600 hover:text-blue-800 font-semibold">
                ← Back to Articles
            </a>
            
            <!-- Article stats -->
            <div class="text-sm text-gray-500">
                <?php echo count($comments); ?> comment(s)
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Comments (<?php echo count($comments); ?>)</h2>

        <!-- Add Comment Form -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-3">Add a Comment</h3>
            <form method="POST">
                <?php if (User::isLoggedIn()): ?>
                    <!-- For logged-in users, show their username as info -->
                    <div class="mb-3 p-2 bg-blue-50 rounded">
                        <p class="text-sm text-gray-700">
                            Commenting as: <span class="font-semibold"><?php echo escape(User::getCurrentUsername()); ?></span>
                        </p>
                    </div>
                <?php else: ?>
                    <!-- For non-logged in users, show they'll comment as Guest -->
                    <div class="mb-3 p-2 bg-gray-50 rounded">
                        <p class="text-sm text-gray-700">
                            Commenting as: <span class="font-semibold">Guest</span>
                        </p>
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <textarea name="content" rows="3" required
                        placeholder="Write your comment..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" name="add_comment"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    Post Comment
                </button>
            </form>
            
            <?php if (!User::isLoggedIn()): ?>
                <p class="text-xs text-gray-500 mt-2">
                    Commenting as a guest. 
                    <a href="login.php" class="text-blue-600 hover:underline">Login</a> to comment with your username.
                </p>
            <?php endif; ?>
        </div>

        <!-- Comments List -->
        <?php if (empty($comments)): ?>
            <div class="text-center py-6 text-gray-500">
                No comments yet. Be the first to comment!
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($comments as $com): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <!-- Show display_name -->
                                <span class="font-semibold text-gray-800">
                                    <?php echo escape($com['display_name']); ?>
                                </span>
                                <?php if ($com['type'] === 'spam'): ?>
                                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Spam</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500"><?php echo date('Y-m-d', strtotime($com['create_date'])); ?></span>
                                <?php if (User::isAdmin()): ?>
                                    <a href="article.php?id=<?php echo $article_id; ?>&delete_comment=<?php echo $com['comment_id']; ?>"
                                       onclick="return confirm('Delete this comment?')"
                                       class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="text-gray-700"><?php echo nl2br(escape($com['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white mt-12 py-6 border-t">
    <div class="container mx-auto px-4 text-center text-gray-600">
        <p>BlogCMS &copy; <?php echo date('Y'); ?></p>
    </div>
</footer>
</body>
</html>
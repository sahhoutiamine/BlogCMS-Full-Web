<?php
// index.php
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
$user = new User();
$article = new Article();
$category = new Category();
$comment = new Comment();

// Handle article deletion
if (isset($_GET['delete_article']) && User::isAdmin()) {
    $article_id = $_GET['delete_article'];
    // Delete comments first
    $comment->deleteByArticle($article_id);
    // Then delete article
    if ($article->delete($article_id)) {
        header('Location: index.php?deleted=1');
        exit;
    }
}

// Get category filter
$category_filter = $_GET['category'] ?? '';

// Get articles
if ($category_filter) {
    $articles = $article->getAll($category_filter);
} else {
    $articles = $article->getAll();
}

// Get categories
$categories = $category->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogCMS - Blog Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
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
                <div class="flex gap-3">
                    <a href="login.php"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        Login
                    </a>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">
                        Welcome, <span class="font-semibold"><?php echo escape(User::currentUser()['name']); ?></span>
                    </span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                        <?php echo strtoupper(User::currentUser()['role']); ?>
                    </span>
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
    <?php if (isset($_GET['created'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Article created successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Article deleted successfully!
        </div>
    <?php endif; ?>

    <!-- Create Article Button -->
    <?php if (User::isAuthor()): ?>
        <div class="mb-6">
            <a href="create_article.php"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                + Create New Article
            </a>
        </div>
    <?php endif; ?>

    <!-- Category Filter -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-md">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Filter by Category:</label>
        <form method="GET" class="flex gap-2">
            <select name="category" onchange="this.form.submit()"
                class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>" 
                        <?php echo ($category_filter == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo escape($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($category_filter): ?>
                <a href="index.php"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                    Clear Filter
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Articles List -->
    <div id="articlesList" class="space-y-6">
        <?php if (empty($articles)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow-md">
                <p class="text-gray-500 text-lg">No articles found in this category.</p>
            </div>
        <?php else: ?>
            <?php foreach ($articles as $art): ?>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                <a href="article.php?id=<?php echo $art['article_id']; ?>" class="hover:text-blue-600">
                                    <?php echo escape($art['title']); ?>
                                </a>
                            </h3>
                            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                <?php echo escape($art['category_name']); ?>
                            </span>
                        </div>
                        <?php if (User::isAdmin()): ?>
                            <a href="index.php?delete_article=<?php echo $art['article_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this article?')"
                               class="text-red-600 hover:text-red-800 font-semibold">
                                Delete
                            </a>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-600 mb-4">
                        <?php echo escape(substr($art['content'], 0, 150)); ?>
                        <?php if (strlen($art['content']) > 150): ?>...<?php endif; ?>
                    </p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span>
                            By <span class="font-semibold"><?php echo escape($art['author_name'] ?? $art['author_username']); ?></span> • 
                            <?php echo date('Y-m-d', strtotime($art['create_date'])); ?>
                        </span>
                        <span>
                            <?php echo $comment->countByArticle($art['article_id']); ?> comment(s)
                        </span>
                    </div>
                    <a href="article.php?id=<?php echo $art['article_id']; ?>"
                        class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-semibold">
                        Read More →
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white mt-12 py-6 border-t">
    <div class="container mx-auto px-4 text-center text-gray-600">
        <p>BlogCMS - Blog Management System &copy; <?php echo date('Y'); ?></p>
        <p class="text-sm mt-2">Developed with PHP, MySQL, and OOP</p>
    </div>
</footer>
</body>
</html>
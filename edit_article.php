<?php
// edit_article.php - Edit article page
session_start();

// Include all classes
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Article.php';
require_once 'classes/Category.php';

// Check if user is logged in and is author or admin
if (!User::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Helper function
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Create class objects
$article = new Article();
$category = new Category();

// Get article ID
$article_id = $_GET['id'] ?? 0;

// Get article
$art = $article->getById($article_id);

// Check if article exists
if (!$art) {
    header('Location: index.php');
    exit;
}

// Check if user is the author or admin
$current_username = User::getCurrentUsername();
if (!User::isAdmin() && $art['author_username'] !== $current_username) {
    header('Location: article.php?id=' . $article_id);
    exit;
}

// Get categories
$categories = $category->getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_article'])) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $status = $_POST['status'] ?? 'Published';
    
    if (!empty($title) && !empty($content)) {
        $article->update($article_id, $title, $content, $category_id, $status);
        header("Location: article.php?id=$article_id&updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article - BlogCMS</title>
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
            <div class="flex items-center gap-4">
                <span class="text-gray-700">
                    Welcome, <span class="font-semibold"><?php echo escape(User::currentUser()['name']); ?></span>
                </span>
                <a href="my_articles.php"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    My Articles
                </a>
                <?php if (User::isAdmin()): ?>
                    <a href="admin.php"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-200">
                        Admin Panel
                    </a>
                <?php endif; ?>
                <a href="logout.php"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Article</h1>
        <p class="text-gray-600 mb-8">Edit your article below.</p>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST">
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Article Title *
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo escape($art['title']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Content *
                    </label>
                    <textarea id="content" name="content" rows="10" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 whitespace-pre-line"><?php echo escape($art['content']); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category *
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>"
                                    <?php echo $art['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                    <?php echo escape($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status" name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Published" <?php echo $art['article_status'] === 'Published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $art['article_status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="article.php?id=<?php echo $article_id; ?>"
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" name="update_article"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Article
                    </button>
                </div>
            </form>
        </div>
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
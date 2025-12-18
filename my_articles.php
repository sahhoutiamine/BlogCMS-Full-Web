<?php
// my_articles.php - Article management page for authors
session_start();

// Include all classes
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Article.php';
require_once 'classes/Category.php';

// Check if user is logged in and is author or admin
if (!User::isLoggedIn() || !User::isAuthor()) {
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

// Get current user
$current_username = User::getCurrentUsername();

// Handle article deletion
if (isset($_GET['delete_article'])) {
    $id = $_GET['delete_article'];
    
    // Check if user is allowed to delete this article
    if ($article->isAuthor($id, $current_username) || User::isAdmin()) {
        $article->delete($id);
        header('Location: my_articles.php?deleted=1');
        exit;
    } else {
        header('Location: my_articles.php?error=1');
        exit;
    }
}

// Handle article status update (publish/unpublish)
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    
    if ($article->isAuthor($id, $current_username) || User::isAdmin()) {
        // Get current status
        $art = $article->getById($id);
        $new_status = $art['article_status'] === 'Published' ? 'draft' : 'Published';
        
        $sql = "UPDATE articles SET article_status = ?, modify_date = NOW() WHERE article_id = ?";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute([$new_status, $id]);
        
        header("Location: my_articles.php?updated=1");
        exit;
    }
}

// Get user's articles
$articles = $article->getByAuthor($current_username, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Articles - BlogCMS</title>
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
                <a href="create_article.php"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                    + New Article
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
    <!-- Messages -->
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Article deleted successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Article status updated!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            You don't have permission to perform this action!
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">My Articles</h1>
        <p class="text-gray-600">Manage your articles here. You have <?php echo count($articles); ?> article(s).</p>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (empty($articles)): ?>
            <div class="p-8 text-center text-gray-500">
                <p class="mb-4">You haven't written any articles yet.</p>
                <a href="create_article.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                    Write your first article →
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left">Title</th>
                            <th class="p-4 text-left">Category</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-left">Created</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $art): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-4">
                                    <a href="article.php?id=<?php echo $art['article_id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <?php echo escape($art['title']); ?>
                                    </a>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                        <?php echo escape($art['category_name']); ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if ($art['article_status'] === 'Published'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-sm text-gray-600">
                                    <?php echo date('Y-m-d', strtotime($art['create_date'])); ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2">
                                        <!-- Edit button -->
                                        <a href="edit_article.php?id=<?php echo $art['article_id']; ?>"
                                           class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded hover:bg-blue-200">
                                            Edit
                                        </a>
                                        
                                        <!-- Publish/Unpublish button -->
                                        <a href="my_articles.php?toggle_status=<?php echo $art['article_id']; ?>"
                                           class="px-3 py-1 <?php echo $art['article_status'] === 'Published' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?> text-sm rounded">
                                            <?php echo $art['article_status'] === 'Published' ? 'Unpublish' : 'Publish'; ?>
                                        </a>
                                        
                                        <!-- Delete button -->
                                        <a href="my_articles.php?delete_article=<?php echo $art['article_id']; ?>"
                                           onclick="return confirm('Are you sure you want to delete this article?')"
                                           class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded hover:bg-red-200">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
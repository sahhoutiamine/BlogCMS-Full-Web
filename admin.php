<?php
// admin.php
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

// Check admin permissions
if (!User::isAdmin()) {
    header('Location: index.php');
    exit;
}

// Create class objects
$user = new User();
$article = new Article();
$category = new Category();
$comment = new Comment();

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['category_name'] ?? '';
    if (!empty($name)) {
        $category->add($name);
        header('Location: admin.php?added=1');
        exit;
    }
}

// Handle category deletion
if (isset($_GET['delete_category'])) {
    $id = $_GET['delete_category'];
    $category->delete($id);
    header('Location: admin.php?deleted=1');
    exit;
}

// Get statistics
$total_articles = $article->count();
$total_comments = $comment->count();
$total_users = $user->countUsers();
$total_categories = $category->count();

// Get data for display
$users = $user->getAllUsers();
$categories = $category->getAll();
$recent_comments = $comment->getAll(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<!-- Navigation -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-600">
            <a href="index.php">BlogCMS Admin Panel</a>
        </h1>
        <div class="flex items-center gap-4">
            <a href="index.php"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                View Site
            </a>
            <a href="logout.php"
                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 py-8">
    <!-- Success Messages -->
    <?php if (isset($_GET['added'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Category added successfully!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Category deleted successfully!
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Articles</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $total_articles; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Comments</h3>
            <p class="text-3xl font-bold text-green-600"><?php echo $total_comments; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Users</h3>
            <p class="text-3xl font-bold text-purple-600"><?php echo $total_users; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Categories</h3>
            <p class="text-3xl font-bold text-yellow-600"><?php echo $total_categories; ?></p>
        </div>
    </div>

    <!-- Category and User Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Category Management -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Category Management</h2>
            
            <!-- Add Category Form -->
            <form method="POST" class="mb-6">
                <div class="flex gap-2">
                    <input type="text" name="category_name" required
                        placeholder="New category name"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    <button type="submit" name="add_category"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Add
                    </button>
                </div>
            </form>

            <!-- Categories List -->
            <div class="space-y-2">
                <?php foreach ($categories as $cat): ?>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span><?php echo escape($cat['category_name']); ?></span>
                        <a href="admin.php?delete_category=<?php echo $cat['category_id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this category?')"
                           class="text-red-600 hover:text-red-800 text-sm">
                            Delete
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- User Management -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">User Management</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-3 text-left">Username</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Role</th>
                            <th class="p-3 text-left">Join Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $usr): ?>
                            <tr class="border-t">
                                <td class="p-3"><?php echo escape($usr['username']); ?></td>
                                <td class="p-3"><?php echo escape($usr['email']); ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php echo escape($usr['role']); ?>
                                    </span>
                                </td>
                                <td class="p-3"><?php echo date('Y-m-d', strtotime($usr['create_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Comments -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Recent Comments</h2>
        <div class="space-y-4">
            <?php foreach ($recent_comments as $com): ?>
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-semibold"><?php echo escape($com['author_username']); ?></span>
                            <span class="text-sm text-gray-600">on "<?php echo escape($com['article_title']); ?>"</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500"><?php echo date('Y-m-d', strtotime($com['create_date'])); ?></span>
                            <?php if ($com['type'] === 'spam'): ?>
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Spam</span>
                            <?php endif; ?>
                            <a href="article.php?id=<?php echo $com['article_id']; ?>&delete_comment=<?php echo $com['comment_id']; ?>"
                               onclick="return confirm('Delete this comment?')"
                               class="text-red-600 hover:text-red-800 text-sm">
                                Delete
                            </a>
                        </div>
                    </div>
                    <p class="text-gray-700"><?php echo nl2br(escape($com['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white mt-12 py-6 border-t">
    <div class="container mx-auto px-4 text-center text-gray-600">
        <p>BlogCMS Admin Panel &copy; <?php echo date('Y'); ?></p>
    </div>
</footer>
</body>
</html>
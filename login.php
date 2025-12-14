<?php
// login.php
session_start();

// Include all classes manually
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Helper function
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Redirect if already logged in
if (User::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = null;
$user = new User();

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($user->login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BlogCMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-2">BlogCMS</h1>
        <p class="text-center text-gray-600 mb-6">Blog Management System</p>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo escape($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                Login
            </button>
        </form>

        <a href="index.php"
            class="block w-full mt-4 bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            Back to Articles
        </a>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 font-semibold mb-2">Demo Accounts:</p>
            <p class="text-xs text-gray-600">Admin: admin / admin123</p>
            <p class="text-xs text-gray-600">Author: author1 / author123</p>
            <p class="text-xs text-gray-600 mt-2">From Database:</p>
            <p class="text-xs text-gray-600">Admin: admin_blog / password</p>
            <p class="text-xs text-gray-600">Author: jean_dupont / password</p>
        </div>
    </div>
</div>
</body>
</html>
<?php
session_start();

// Check if already logged in
if (isset($_SESSION['photo_admin_logged_in']) && $_SESSION['photo_admin_logged_in'] === true) {
    header('Location: photo-admin-dashboard.php');
    exit;
}

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // TODO: Replace with database check
    // For now, using hardcoded credentials (CHANGE IN PRODUCTION)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['photo_admin_logged_in'] = true;
        $_SESSION['photo_admin_username'] = $username;
        header('Location: photo-admin-dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
</head>
<body class="bg-gradient-to-br from-orange-100 to-green-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="images/bjplogo.png" alt="BJP Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-orange-600">Photo Gallery Admin</h1>
            <p class="text-gray-600 mt-2">Login to manage photos</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter username">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Enter password">
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-orange-600 to-green-600 text-white py-3 rounded-lg font-semibold hover:from-orange-700 hover:to-green-700 transition shadow-lg">
                Login
            </button>
        </form>

        <!-- Back to Website -->
        <div class="mt-6 text-center">
            <a href="index.php" class="text-orange-600 hover:underline">← Back to Website</a>
        </div>
    </div>

</body>
</html>

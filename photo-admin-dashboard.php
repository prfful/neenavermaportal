<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    header('Location: photo-admin-login.php');
    exit;
}

require_once 'includes/photo-gallery-db.php';

// Fetch statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM download_gallery_photos WHERE is_moved_to_main = 0) as total_photos,
    (SELECT COUNT(*) FROM download_gallery_events WHERE is_active = 1 AND delete_date >= CURDATE()) as active_events,
    (SELECT COUNT(*) FROM download_gallery_photos WHERE is_moved_to_main = 0) as pending_approval";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

$total_photos = $stats['total_photos'] ?? 0;
$active_events = $stats['active_events'] ?? 0;
$pending_approval = $stats['pending_approval'] ?? 0;

$success_msg = $_GET['success'] ?? '';
$error_msg = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="images/bjplogo.png" alt="BJP Logo" class="h-10">
                <div>
                    <h1 class="text-xl font-bold text-orange-600">Photo Gallery Admin</h1>
                    <p class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['photo_admin_username']); ?></p>
                </div>
            </div>
            <a href="photo-admin-logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Logout
            </a>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Success/Error Messages -->
        <?php if ($success_msg): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
            <?php echo htmlspecialchars($success_msg); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6">
            <?php echo htmlspecialchars($error_msg); ?>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100">Total Photos</p>
                        <p class="text-4xl font-bold"><?php echo $total_photos; ?></p>
                    </div>
                    <div class="text-5xl opacity-50">📸</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100">Active Events</p>
                        <p class="text-4xl font-bold"><?php echo $active_events; ?></p>
                    </div>
                    <div class="text-5xl opacity-50">📅</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100">Pending Approval</p>
                        <p class="text-4xl font-bold"><?php echo $pending_approval; ?></p>
                    </div>
                    <div class="text-5xl opacity-50">⏳</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="photo-admin-upload.php" 
               class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition text-center group">
                <div class="text-4xl mb-3 group-hover:scale-110 transition">📤</div>
                <h3 class="font-bold text-lg text-gray-800">Upload Photos</h3>
                <p class="text-sm text-gray-600 mt-1">Add new event photos</p>
            </a>

            <a href="photo-admin-manage.php" 
               class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition text-center group">
                <div class="text-4xl mb-3 group-hover:scale-110 transition">📋</div>
                <h3 class="font-bold text-lg text-gray-800">Manage Photos</h3>
                <p class="text-sm text-gray-600 mt-1">View & edit all photos</p>
            </a>

            <a href="photo-admin-move.php" 
               class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition text-center group">
                <div class="text-4xl mb-3 group-hover:scale-110 transition">🔄</div>
                <h3 class="font-bold text-lg text-gray-800">Move to Main Gallery</h3>
                <p class="text-sm text-gray-600 mt-1">Select & transfer photos</p>
            </a>

            <a href="photo-download-gallery.php" 
               class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition text-center group" target="_blank">
                <div class="text-4xl mb-3 group-hover:scale-110 transition">👁️</div>
                <h3 class="font-bold text-lg text-gray-800">View Public Gallery</h3>
                <p class="text-sm text-gray-600 mt-1">See public view</p>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Activity</h2>
            <div class="space-y-3">
                <p class="text-gray-600 text-center py-8">No recent activity</p>
                <!-- TODO: Add recent activity list from database -->
            </div>
        </div>

    </div>

</body>
</html>

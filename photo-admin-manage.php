<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    header('Location: photo-admin-login.php');
    exit;
}

require_once 'includes/photo-gallery-db.php';

// Handle delete requests
if (isset($_GET['delete_photo'])) {
    $photo_id = (int)$_GET['delete_photo'];
    $photo_query = $conn->query("SELECT file_path FROM download_gallery_photos WHERE id = $photo_id");
    if ($photo_query && $row = $photo_query->fetch_assoc()) {
        $file_path = '.' . $row['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $conn->query("DELETE FROM download_gallery_photos WHERE id = $photo_id");
    header('Location: photo-admin-manage.php?msg=Photo deleted');
    exit;
}

if (isset($_GET['delete_event'])) {
    $event_id = (int)$_GET['delete_event'];
    // Delete all photos in the event
    $photos = $conn->query("SELECT file_path FROM download_gallery_photos WHERE event_id = $event_id");
    while ($photo = $photos->fetch_assoc()) {
        $file_path = '.' . $photo['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $conn->query("DELETE FROM download_gallery_photos WHERE event_id = $event_id");
    $conn->query("DELETE FROM download_gallery_events WHERE id = $event_id");
    header('Location: photo-admin-manage.php?msg=Event deleted');
    exit;
}

// Fetch all events with photos
$query = "SELECT dge.id, dge.event_name, dge.event_date, dge.program_type, dge.event_location, dge.is_active, dge.created_at,
          COUNT(dgp.id) as photo_count,
          COALESCE(SUM(dgp.file_size), 0) as total_size
          FROM download_gallery_events dge
          LEFT JOIN download_gallery_photos dgp ON dge.id = dgp.event_id AND dgp.is_moved_to_main = 0
          WHERE dge.is_active = 1
          GROUP BY dge.id, dge.event_name, dge.event_date, dge.program_type, dge.event_location, dge.is_active, dge.created_at
          ORDER BY dge.event_date DESC";
$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get photos
        $photo_query = "SELECT * FROM download_gallery_photos WHERE event_id = ? AND is_moved_to_main = 0";
        $stmt = $conn->prepare($photo_query);
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $photo_result = $stmt->get_result();
        $photos = [];
        while ($photo = $photo_result->fetch_assoc()) {
            $photos[] = $photo;
        }
        $stmt->close();
        $row['photos'] = $photos;
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Photos - Photo Gallery Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="images/bjplogo.png" alt="BJP Logo" class="h-10">
                <h1 class="text-xl font-bold text-orange-600">Manage Photos</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="photo-admin-dashboard.php" class="text-gray-600 hover:text-orange-600">← Dashboard</a>
                <a href="photo-admin-logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">

        <?php if (isset($_GET['msg'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            ✓ <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid md:grid-cols-4 gap-4">
                <input type="text" placeholder="Search event..." 
                       class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All Program Types</option>
                    <option value="जन समारोह">जन समारोह</option>
                    <option value="शिक्षा">शिक्षा</option>
                    <option value="स्वास्थ्य">स्वास्थ्य</option>
                </select>
                <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="date_desc">Latest First</option>
                    <option value="date_asc">Oldest First</option>
                </select>
                <button class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                    🔍 Search
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-blue-50 border border-blue-300 rounded-lg p-4 mb-6 flex justify-between items-center">
            <div>
                <input type="checkbox" id="selectAll" class="mr-2">
                <label for="selectAll" class="font-semibold">Select All</label>
                <span class="ml-4 text-gray-600">(<span id="selectedCount">0</span> selected)</span>
            </div>
            <div class="space-x-2">
                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    🗑️ Delete Selected
                </button>
            </div>
        </div>

        <!-- Photos List -->
        <div class="space-y-8">
            
            <?php if (empty($events)): ?>
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-xl">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-bold text-gray-700">No Photos Found</h3>
                <p class="text-gray-600 mt-2">Upload some photos to get started</p>
                <a href="photo-admin-upload.php" class="inline-block mt-4 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                    📤 Upload Photos
                </a>
            </div>
            <?php else: ?>

            <?php foreach ($events as $event): ?>
            <!-- Event Group -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-green-500 text-white p-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p class="text-sm text-orange-100">
                            📅 <?php echo date('d M Y', strtotime($event['event_date'])); ?> | 
                            <?php if ($event['event_location']): ?>📍 <?php echo htmlspecialchars($event['event_location']); ?> | <?php endif; ?>
                            🏷️ <?php echo htmlspecialchars($event['program_type']); ?>
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editEvent(<?php echo $event['id']; ?>)" class="bg-white text-orange-600 px-3 py-1 rounded text-sm font-semibold hover:bg-orange-50">
                            ✏️ Edit
                        </button>
                        <button onclick="deleteEvent(<?php echo $event['id']; ?>)" class="bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-red-700">
                            🗑️ Delete Event
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        
                        <?php foreach ($event['photos'] as $photo): ?>
                        <div class="relative group">
                            <input type="checkbox" class="photo-checkbox absolute top-2 left-2 z-10 w-5 h-5" 
                                   data-photo-id="<?php echo $photo['id']; ?>" onchange="updateSelectedCount()">
                            <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($photo['original_filename']); ?>" 
                                 class="w-full h-32 object-cover rounded-lg shadow">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition rounded-lg flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition space-x-2">
                                    <button onclick="viewPhoto('<?php echo htmlspecialchars($photo['file_path']); ?>')" class="bg-white text-blue-600 p-2 rounded-full" title="View">
                                        👁️
                                    </button>
                                    <button onclick="deletePhoto(<?php echo $photo['id']; ?>)" class="bg-white text-red-600 p-2 rounded-full" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-center mt-1 text-gray-600"><?php echo number_format($photo['file_size'] / 1024, 0); ?> KB</p>
                        </div>
                        <?php endforeach; ?>

                    </div>

                    <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                        <span>Total: <?php echo $event['photo_count']; ?> photos | Size: <?php echo number_format($event['total_size'] / 1024 / 1024, 1); ?> MB</span>
                        <span class="text-yellow-700">⏳ Expires: <?php echo date('d M Y', strtotime($event['delete_date'])); ?></span>
                    </div>.photo-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = document.querySelectorAll('.photo-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function viewPhoto(photoPath) {
            window.open(photoPath, '_blank');
        }

        function deletePhoto(photoId) {
            if (confirm('Are you sure you want to delete this photo?')) {
                // TODO: Implement delete via AJAX
                alert('Delete photo #' + photoId);
                location.reload();
            }
        }

        function editEvent(eventId) {
            // TODO: Implement edit modal or redirect
            alert('Edit event #' + eventId);
        }

        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this entire event and all its photos?')) {
                // TODO: Implement delete via AJAX
                alert('Delete event #' + eventId);
                location.reload();
            }

    </div>

    <script>
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#selectAll)');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });

        function updateSelectedCount() {
            const count = document.querySelectorAll('input[type="checkbox"]:checked:not(#selectAll)').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function deletePhoto(photoId) {
            if (confirm('Delete this photo?')) {
                window.location.href = 'photo-admin-manage.php?delete_photo=' + photoId;
            }
        }

        function deleteEvent(eventId) {
            if (confirm('Delete entire event and all photos? This cannot be undone.')) {
                window.location.href = 'photo-admin-manage.php?delete_event=' + eventId;
            }
        }

        function viewPhoto(photoPath) {
            window.open(photoPath, '_blank');
        }

        function editEvent(eventId) {
            alert('Edit functionality coming soon');
        }
    </script>

</body>
</html>

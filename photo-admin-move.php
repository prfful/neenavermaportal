<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    header('Location: photo-admin-login.php');
    exit;
}

require_once 'includes/photo-gallery-db.php';

// Fetch events with photos
$query = "SELECT dge.*, COUNT(dgp.id) as photo_count
          FROM download_gallery_events dge
          LEFT JOIN download_gallery_photos dgp ON dge.id = dgp.event_id AND dgp.is_moved_to_main = 0
          WHERE dge.is_active = 1 AND dge.delete_date >= CURDATE()
          GROUP BY dge.id
          HAVING photo_count > 0
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
    <title>Move to Main Gallery - Photo Gallery Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="images/bjplogo.png" alt="BJP Logo" class="h-10">
                <h1 class="text-xl font-bold text-orange-600">Move to Main Gallery</h1>
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

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-300 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-900 mb-2">📸 Select Photos to Move</h2>
            <p class="text-blue-800">
                Choose photos from the download gallery to permanently move them to the main website gallery. 
                Selected photos will be removed from the download gallery and added to gallery.php.
            </p>
        </div>

        <!-- Selection Panel -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll" class="font-semibold">Select All Photos</label>
                    <span class="ml-4 text-gray-600">(<span id="selectedCount">0</span> selected)</span>
                </div>
                <button onclick="moveSelectedPhotos()" id="moveBtn" disabled
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    🔄 Move Selected to Main Gallery
                </button>
            </div>
        </div>

        <!-- Photos Grid by Event -->
        <div class="space-y-8">
            
            <?php if (empty($events)): ?>
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-xl">
                <div class="text-6xl mb-4">📦</div>
                <h3 class="text-xl font-bold text-gray-700">No Photos in Download Gallery</h3>
                <p class="text-gray-600 mt-2">Upload photos to the download gallery first</p>
                <a href="photo-admin-upload.php" class="inline-block mt-4 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                    📤 Upload Photos
                </a>
            </div>
            <?php else: ?>

            <?php foreach ($events as $event): ?>
            <!-- Event Group -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                            <p class="text-sm text-purple-100">
                                📅 <?php echo date('d M Y', strtotime($event['event_date'])); ?> | 
                                <?php if ($event['event_location']): ?>📍 <?php echo htmlspecialchars($event['event_location']); ?><?php endif; ?>
                            </p>
                        </div>
                        <label class="flex items-center space-x-2 cursor-pointer bg-white text-purple-600 px-4 py-2 rounded-lg">
                            <input type="checkbox" class="select-event-all" data-event="event_<?php echo $event['id']; ?>" onchange="selectEventAll(this)">
                            <span class="font-semibold">Select All</span>
                        </label>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        
                        <?php foreach ($event['photos'] as $photo): ?>
                        <div class="relative group">
                            <input type="checkbox" 
                                   class="photo-checkbox absolute top-2 left-2 z-10 w-6 h-6 cursor-pointer" 
                                   data-event="event_<?php echo $event['id']; ?>" 
                                   data-photo="<?php echo $photo['id']; ?>" 
                                   onchange="updateSelection()">
                            <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($photo['original_filename']); ?>" 
                                 class="w-full h-32 object-cover rounded-lg shadow-lg">
                            <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-70 transition rounded-lg"></div>
                            <div class="absolute bottom-2 left-2 right-2 text-white text-xs opacity-0 group-hover:opacity-100 transition">
                                <p class="font-semibold truncate"><?php echo htmlspecialchars($photo['original_filename']); ?></p>
                                <p><?php echo number_format($photo['file_size'] / 1024, 0); ?> KB</p>
                            </div>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <!-- Preview of Main Gallery -->
        <div class="mt-12 bg-gray-100 rounded-xl p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">🖼️ Current Main Gallery</h3>
            <div class="grid grid-cols-4 md:grid-cols-8 gap-2">
                <img src="images/g3.jpg" class="w-full h-20 object-cover rounded shadow">
                <img src="images/g4.jpg" class="w-full h-20 object-cover rounded shadow">
                <img src="images/g5.jpg" class="w-full h-20 object-cover rounded shadow">
                <div class="w-full h-20 bg-gray-300 rounded flex items-center justify-center text-gray-600 text-sm">
                    +45 more
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-3">Photos moved here will appear in gallery.php</p>
        </div>

    </div>

    <script>
        // Select all photos
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.photo-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        function selectEventAll(checkbox) {
            const eventId = checkbox.dataset.event;
            const eventCheckboxes = document.querySelectorAll(`.photo-checkbox[data-event="${eventId}"]`);
            eventCheckboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelection();
        }     const eventCheckboxes = document.querySelectorAll(`.photo-checkbox[data-event="${eventId}"]`);
                eventCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelection();
            });
        });

        function updateSelection() {
            const selectedCount = document.querySelectorAll('.photo-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = selectedCount;
            document.getElementById('moveBtn').disabled = selectedCount === 0;
        }

        function moveSelectedPhotos() {
            const selected = document.querySelectorAll('.photo-checkbox:checked');
            if (selected.length === 0) {
                alert('Please select at least one photo');
                return;
            }

            if (confirm(`Move ${selected.length} photo(s) to main gallery?\n\nThis action will:\n- Remove them from download gallery\n- Add them to gallery.php\n- Make them permanent (no auto-delete)`)) {
                const photoIds = Array.from(selected).map(cb => cb.dataset.photo);
                
                // Send to backend
                const formData = new FormData();
                photoIds.forEach(id => formData.append('photo_ids[]', id));
                
                fetch('backend/move-photos-handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✓ ' + data.message);
                        location.reload();
                    } else {
                        alert('✗ Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('✗ Network error: ' + error);
                });
            }
        }
    </script>

</body>
</html>

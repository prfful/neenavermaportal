<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    header('Location: photo-admin-login.php');
    exit;
}

$success = '';
$error = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photos'])) {
    // TODO: Implement file upload logic
    $success = 'Photos uploaded successfully!';
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photos - Photo Gallery Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="images/bjplogo.png" alt="BJP Logo" class="h-10">
                <h1 class="text-xl font-bold text-orange-600">Upload Photos</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="photo-admin-dashboard.php" class="text-gray-600 hover:text-orange-600">← Dashboard</a>
                <a href="photo-admin-logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8 max-w-4xl">

        <!-- Messages -->
        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            ✓ <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            ✗ <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="backend/upload-photos-handler.php" enctype="multipart/form-data" id="uploadForm" class="space-y-6">
                
                <!-- Event Details -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Event Name *</label>
                    <input type="text" name="event_name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                           placeholder="e.g., Jan Sampark Abhiyan">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Event Date *</label>
                        <input type="date" name="event_date" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Program Type *</label>
                        <select name="program_type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Type</option>
                            <option value="जन समारोह">जन समारोह</option>
                            <option value="शिक्षा">शिक्षा</option>
                            <option value="स्वास्थ्य">स्वास्थ्य</option>
                            <option value="विकास कार्य">विकास कार्य</option>
                            <option value="सामाजिक कार्यक्रम">सामाजिक कार्यक्रम</option>
                            <option value="राजनीतिक">राजनीतिक</option>
                            <option value="अन्य">अन्य</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Event Location</label>
                    <input type="text" name="event_location" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                           placeholder="e.g., Dhar, Pithampur">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                              placeholder="Brief description of the event"></textarea>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Upload Photos *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-orange-500 transition">
                        <input type="file" name="photos[]" id="photoInput" multiple accept="image/*" required
                               class="hidden" onchange="previewImages()">
                        <label for="photoInput" class="cursor-pointer">
                            <div class="text-6xl mb-4">📷</div>
                            <p class="text-lg font-semibold text-gray-700">Click to select photos</p>
                            <p class="text-sm text-gray-500 mt-2">or drag and drop here</p>
                            <p class="text-xs text-gray-400 mt-2">Supports: JPG, PNG, WEBP (Max 5MB each)</p>
                        </label>
                    </div>
                </div>

                <!-- Preview Area -->
                <div id="previewArea" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden"></div>

                <!-- Auto-Delete Notice -->
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>⚠️ Note:</strong> Photos will be automatically deleted after 5 days from the event date.
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-orange-600 to-green-600 text-white py-3 rounded-lg font-semibold hover:from-orange-700 hover:to-green-700 transition shadow-lg">
                        📤 Upload Photos
                    </button>
                    <a href="photo-admin-dashboard.php" 
                       class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

    <script>
        function previewImages() {
            const input = document.getElementById('photoInput');
            const previewArea = document.getElementById('previewArea');
            previewArea.innerHTML = '';
            previewArea.classList.remove('hidden');

            if (input.files) {
                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow">
                            <div class="absolute top-2 right-2 bg-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                <button type="button" onclick="removeImage(${index})" class="text-red-600 font-bold">✕</button>
                            </div>
                        `;
                        previewArea.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function removeImage(index) {
            // TODO: Implement remove functionality
            alert('Remove functionality: File #' + (index + 1));
        }

        // Drag and drop
        const dropZone = document.querySelector('[for="photoInput"]').closest('.border-dashed');
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-orange-500', 'bg-orange-50');
            document.getElementById('photoInput').files = e.dataTransfer.files;
            previewImages();
        });
    </script>

</body>
</html>

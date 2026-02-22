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

                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Uploading...</span>
                        <span id="progressText" class="text-sm font-semibold text-gray-700">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progressBar" class="bg-orange-600 h-3 rounded-full" style="width: 0%"></div>
                    </div>
                </div>

                <div id="uploadError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg"></div>

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

        <!-- Banner/Flex Photo Upload Form -->
        <div class="bg-white rounded-xl shadow-lg p-8 mt-8">
            <div class="mb-6 pb-6 border-b-2 border-orange-200">
                <h2 class="text-2xl font-bold text-orange-600">🖼️ बेनर एवं फलेक्स फोटो अपलोड</h2>
                <p class="text-gray-600 mt-2">उच्च रिजॉल्यूशन फोटो अपलोड करें जो बेनर और फलेक्स प्रिंटिंग के लिए उपयुक्त हैं</p>
            </div>

            <form method="POST" action="backend/upload-banner-photos-handler.php" enctype="multipart/form-data" id="bannerUploadForm" class="space-y-6">
                
                <!-- Banner Event Details -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">कार्यक्रम/इवेंट का नाम *</label>
                    <input type="text" name="banner_event_name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                           placeholder="e.g., जन समारोह, शिक्षा कार्यक्रम">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">कार्यक्रम की तारीख *</label>
                        <input type="date" name="banner_event_date" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">कार्यक्रम का स्थान</label>
                        <input type="text" name="banner_event_location" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g., धार, पीथमपुर">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">फोटो का विवरण</label>
                    <textarea name="banner_description" rows="2" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                              placeholder="बेनर/फलेक्स के लिए इस फोटो के उपयोग के बारे में संक्षिप्त विवरण"></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">फोटो का आयाम/प्रकार</label>
                    <select name="banner_dimensions" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">आयाम चुनें</option>
                        <option value="Landscape - High Definition">लैंडस्केप - उच्च परिभाषा (HD)</option>
                        <option value="Portrait - High Definition">पोर्ट्रेट - उच्च परिभाषा (HD)</option>
                        <option value="Banner - Wide Format">बेनर - चौड़े प्रारूप</option>
                        <option value="Flex - Full Size">फलेक्स - पूर्ण आकार</option>
                        <option value="Social Media - HD">सोशल मीडिया - उच्च परिभाषा</option>
                        <option value="Other - High Definition">अन्य - उच्च परिभाषा</option>
                    </select>
                </div>

                <!-- Banner File Upload -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">बेनर/फलेक्स फोटो अपलोड करें *</label>
                    <div class="border-2 border-dashed border-orange-300 rounded-lg p-8 text-center hover:border-orange-600 transition bg-orange-50">
                        <input type="file" name="banner_photos[]" id="bannerPhotoInput" multiple accept="image/*" required
                               class="hidden" onchange="previewBannerImages()">
                        <label for="bannerPhotoInput" class="cursor-pointer">
                            <div class="text-6xl mb-4">🖼️</div>
                            <p class="text-lg font-semibold text-gray-700">बेनर फोटो अपलोड करने के लिए यहाँ क्लिक करें</p>
                            <p class="text-sm text-gray-600 mt-2">या यहाँ ड्रैग और ड्रॉप करें</p>
                            <p class="text-xs text-gray-500 mt-2">समर्थित: JPG, PNG, WEBP (अधिकतम 10MB प्रत्येक)</p>
                        </label>
                    </div>
                </div>

                <!-- Preview Area for Banner Photos -->
                <div id="bannerPreviewArea" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden"></div>

                <!-- Banner Upload Progress -->
                <div id="bannerUploadProgress" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">अपलोड हो रहा है...</span>
                        <span id="bannerProgressText" class="text-sm font-semibold text-gray-700">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="bannerProgressBar" class="bg-orange-600 h-3 rounded-full" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Info Notice -->
                <div class="bg-blue-50 border border-blue-300 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <strong>ℹ️ सूचना:</strong> अपलोड किए गए फोटो तुरंत "बेनर फलेक्स फोटो" पृष्ठ में दिखाई देंगे।
                    </p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-orange-600 to-green-600 text-white py-3 rounded-lg font-semibold hover:from-orange-700 hover:to-green-700 transition shadow-lg">
                        🖼️ बेनर फोटो अपलोड करें
                    </button>
                    <button type="reset" 
                            class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-100 transition">
                        रीसेट करें
                    </button>
                </div>

            </form>
        </div>

    </div>

    <script>
        // ===== Regular Photo Upload Functionality =====
        const uploadForm = document.getElementById('uploadForm');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const uploadError = document.getElementById('uploadError');

        if (uploadForm) {
            uploadForm.addEventListener('submit', (e) => {
                e.preventDefault();
                uploadProgress.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = '0%';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadForm.action, true);

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percent + '%';
                        progressText.textContent = percent + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        // Success - redirect happens server-side
                        progressText.textContent = '100%';
                        setTimeout(() => {
                            window.location.href = 'photo-admin-dashboard.php';
                        }, 500);
                    } else {
                        let message = 'Upload failed. Please try again.';
                        try {
                            const data = JSON.parse(xhr.responseText || '{}');
                            if (data && data.message) {
                                message = data.message;
                            }
                        } catch (err) {
                            // Ignore JSON parse error and use default message.
                        }
                        if (uploadError) {
                            uploadError.textContent = message;
                            uploadError.classList.remove('hidden');
                        } else {
                            alert(message);
                        }
                        uploadProgress.classList.add('hidden');
                    }
                });

                xhr.addEventListener('error', () => {
                    alert('Network error during upload. Please try again.');
                    uploadProgress.classList.add('hidden');
                });

                const formData = new FormData(uploadForm);
                xhr.send(formData);
            });
        }

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

        // ===== Banner Photo Upload Functionality =====
        const bannerUploadForm = document.getElementById('bannerUploadForm');
        const bannerUploadProgress = document.getElementById('bannerUploadProgress');
        const bannerProgressBar = document.getElementById('bannerProgressBar');
        const bannerProgressText = document.getElementById('bannerProgressText');

        if (bannerUploadForm) {
            bannerUploadForm.addEventListener('submit', (e) => {
                e.preventDefault();
                bannerUploadProgress.classList.remove('hidden');
                bannerProgressBar.style.width = '0%';
                bannerProgressText.textContent = '0%';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', bannerUploadForm.action, true);

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        bannerProgressBar.style.width = percent + '%';
                        bannerProgressText.textContent = percent + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        // Success - redirect happens server-side
                        bannerProgressText.textContent = '100%';
                        setTimeout(() => {
                            window.location.href = 'photo-admin-dashboard.php';
                        }, 500);
                    } else {
                        alert('Upload failed. Please try again.');
                        bannerUploadProgress.classList.add('hidden');
                    }
                });

                xhr.addEventListener('error', () => {
                    alert('Network error during upload. Please try again.');
                    bannerUploadProgress.classList.add('hidden');
                });

                const formData = new FormData(bannerUploadForm);
                xhr.send(formData);
            });
        }

        function previewBannerImages() {
            const input = document.getElementById('bannerPhotoInput');
            const previewArea = document.getElementById('bannerPreviewArea');
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
                                <button type="button" onclick="removeBannerImage(${index})" class="text-red-600 font-bold">✕</button>
                            </div>
                        `;
                        previewArea.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function removeBannerImage(index) {
            alert('Remove functionality: File #' + (index + 1));
        }

        // Drag and drop for banner photos
        const bannerDropZone = document.querySelector('[for="bannerPhotoInput"]').closest('.border-dashed');
        
        bannerDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            bannerDropZone.classList.add('border-orange-600', 'bg-orange-100');
        });

        bannerDropZone.addEventListener('dragleave', () => {
            bannerDropZone.classList.remove('border-orange-600', 'bg-orange-100');
        });

        bannerDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            bannerDropZone.classList.remove('border-orange-600', 'bg-orange-100');
            document.getElementById('bannerPhotoInput').files = e.dataTransfer.files;
            previewBannerImages();
        });
    </script>

</body>
</html>

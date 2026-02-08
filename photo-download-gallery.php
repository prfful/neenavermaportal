<?php
require_once 'includes/photo-gallery-db.php';
// Load events from API
$events = [];
if (file_exists('backend/fetch-gallery-data.php')) {
    ob_start();
    $events = include 'backend/fetch-gallery-data.php';
    ob_end_clean();
    if (!is_array($events)) {
        $events = [];
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Download Gallery - नीना विक्रम वर्मा</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
    <style>
        .photo-card {
            transition: transform 0.3s ease;
        }
        .photo-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="images/bjplogo.png" alt="BJP Logo" class="h-12">
                    <div>
                        <h1 class="text-2xl font-bold text-orange-600">Photo Gallery</h1>
                        <p class="text-sm text-gray-600">Download Event Photos</p>
                    </div>
                </div>
                <a href="index.php" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                    ← Back to Website
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-orange-600 to-green-600 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-3">📸 कार्यक्रम फोटो गैलरी</h2>
            <p class="text-lg">नवीनतम कार्यक्रमों की फोटो देखें और डाउनलोड करें</p>
            <p class="text-sm mt-2 text-orange-100">
                <strong>नोट:</strong> फोटो कार्यक्रम की तारीख से 5 दिन तक उपलब्ध रहेंगी
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="bg-white shadow-md py-6 sticky top-20 z-40">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="🔍 Search by event name..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <select id="programFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All Program Types</option>
                    <option value="जन समारोह">जन समारोह</option>
                    <option value="शिक्षा">शिक्षा</option>
                    <option value="स्वास्थ्य">स्वास्थ्य</option>
                    <option value="विकास कार्य">विकास कार्य</option>
                    <option value="सामाजिक कार्यक्रम">सामाजिक कार्यक्रम</option>
                    <option value="राजनीतिक">राजनीतिक</option>
                </select>
                <select id="sortFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="date_desc">Latest First</option>
                    <option value="date_asc">Oldest First</option>
                    <option value="name_asc">Name A-Z</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-12">
        <div class="container mx-auto px-6">
            
            <?php if (empty($events)): ?>
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="text-8xl mb-4">📷</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">No Photos Available</h3>
                <p class="text-gray-600">कोई कार्यक्रम फोटो उपलब्ध नहीं है। जल्द ही नई फोटो जोड़ी जाएंगी।</p>
            </div>

            <?php else: ?>
            <!-- Events Grid -->
            <div id="eventsContainer" class="space-y-12">
                
                <?php foreach ($events as $event): ?>
                <div class="event-card bg-white rounded-xl shadow-lg overflow-hidden" data-event-name="<?php echo strtolower($event['event_name']); ?>" data-program-type="<?php echo $event['program_type']; ?>" data-event-date="<?php echo $event['event_date']; ?>">
                    <div class="bg-gradient-to-r from-orange-500 to-green-500 text-white p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <span>📅 <?php echo date('d M Y', strtotime($event['event_date'])); ?></span>
                                    <?php if ($event['event_location']): ?>
                                    <span>📍 <?php echo htmlspecialchars($event['event_location']); ?></span>
                                    <?php endif; ?>
                                    <span>🏷️ <?php echo htmlspecialchars($event['program_type']); ?></span>
                                </div>
                                <?php if ($event['description']): ?>
                                <p class="mt-3 text-orange-50"><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="bg-white text-orange-600 px-4 py-2 rounded-lg font-bold">
                                <?php echo count($event['photos']); ?> Photos
                            </div>
                        </div>
                    </div>

                    <!-- Photos Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            
                            <?php foreach ($event['photos'] as $photo): ?>
                            <div class="photo-card relative group cursor-pointer" onclick="openLightbox('<?php echo $photo['file_path']; ?>', '<?php echo htmlspecialchars($photo['original_filename']); ?>')">
                                <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" alt="<?php echo htmlspecialchars($photo['original_filename']); ?>" class="w-full h-48 object-cover rounded-lg shadow-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition rounded-lg flex items-center justify-center">
                                    <button class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold opacity-0 group-hover:opacity-100 transition">
                                        👁️ View
                                    </button>
                                </div>
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <a href="backend/download-photo.php?photo_id=<?php echo $photo['id']; ?>" 
                                       onclick="event.stopPropagation();" 
                                       class="bg-green-600 text-white p-2 rounded-full shadow-lg hover:bg-green-700 inline-block">
                                        ⬇️
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div>

                        <!-- Download All Button -->
                        <div class="mt-6 text-center">
                            <button onclick="downloadAllPhotos(<?php echo $event['id']; ?>)" 
                                    class="bg-gradient-to-r from-orange-600 to-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-orange-700 hover:to-green-700 transition shadow-lg">
                                ⬇️ Download All Photos (ZIP)
                            </button>
                        </div>
                    </div>

                    <!-- Expiry Notice -->
                    <div class="bg-yellow-50 border-t border-yellow-200 px-6 py-3 text-sm text-yellow-800">
                        ⏳ Photos will be deleted on: <strong><?php echo date('d M Y', strtotime($event['delete_date'])); ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
        <div class="relative max-w-5xl w-full">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-4xl font-bold hover:text-orange-500">
                ×
            </button>
            <img id="lightboxImg" src="" alt="Photo" class="w-full rounded-lg shadow-2xl">
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-4">
                <button onclick="downloadCurrentPhoto(); event.stopPropagation();" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    ⬇️ Download
                </button>
                <button onclick="closeLightbox(); event.stopPropagation();" 
                        class="bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">© 2026 श्रीमती नीना विक्रम वर्मा | विधायक धार विधानसभा</p>
            <p class="text-sm text-gray-500 mt-2">
                <a href="photo-admin-login.php" class="hover:text-orange-400">Admin Login</a>
            </p>
        </div>
    </footer>

    <script>
        let currentPhoto = '';
        let currentPhotoName = '';

        function openLightbox(photoPath, photoName) {
            document.getElementById('lightboxImg').src = photoPath;
            document.getElementById('lightbox').classList.remove('hidden');
            currentPhoto = photoPath;
            currentPhotoName = photoName;
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
        }

            function downloadCurrentPhoto() {
                // Download current photo
                const a = document.createElement('a');
                a.href = currentPhoto;
                a.download = currentPhotoName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }

            function downloadAllPhotos(eventId) {
                if (confirm('Download all photos as ZIP file?')) {
                    window.location.href = 'backend/download-all-photos.php?event_id=' + eventId;
                }
            }

            // Filter events
            if (document.getElementById('searchInput')) {
                document.getElementById('searchInput').addEventListener('change', filterEvents);
            }
            if (document.getElementById('programFilter')) {
                document.getElementById('programFilter').addEventListener('change', filterEvents);
            }
            if (document.getElementById('sortFilter')) {
                document.getElementById('sortFilter').addEventListener('change', filterEvents);
            }

            function filterEvents() {
                const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
                const programType = document.getElementById('programFilter')?.value || '';
                const sortBy = document.getElementById('sortFilter')?.value || '';

                // TODO: Implement filtering logic with database query
                console.log('Filtering:', { searchTerm, programType, sortBy });
            }

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>

</body>
</html>

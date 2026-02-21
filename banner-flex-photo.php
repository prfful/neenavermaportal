<?php
require_once 'includes/photo-gallery-db.php';

// Load banner photos from API
$banner_photos = [];
if (file_exists('backend/fetch-banner-data.php')) {
    ob_start();
    $banner_photos = include 'backend/fetch-banner-data.php';
    ob_end_clean();
    if (!is_array($banner_photos)) {
        $banner_photos = [];
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>बेनर फलेक्स फोटो गैलरी - नीना विक्रम वर्मा</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/bjplogo.png">
    <style>
        .photo-card {
            transition: transform 0.3s ease;
        }
        .photo-card:hover {
            transform: translateY(-5px);
        }
        .banner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .banner-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
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
                        <h1 class="text-2xl font-bold text-orange-600">बेनर फलेक्स फोटो</h1>
                        <p class="text-sm text-gray-600">उच्च रिजॉल्यूशन फोटो संग्रह</p>
                    </div>
                </div>
                <a href="index.php" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                    ← मुख्य पृष्ठ
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-orange-600 to-green-600 text-white py-12">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-3">🖼️ बेनर एवं फलेक्स फोटो संग्रह</h2>
            <p class="text-lg">उच्च परिभाषा फोटो जो बेनर और फलेक्स के लिए उपयुक्त हैं</p>
            <p class="text-sm mt-2 text-orange-100">
                <strong>सूचना:</strong> ये फोटो बेनर और फलेक्स प्रिंटिंग के लिए विशेष रूप से चुने गए हैं
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="bg-white shadow-md py-6 sticky top-20 z-40">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" id="searchInput" placeholder="🔍 ईवेंट नाम या स्थान से खोजें..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <select id="sortFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="date_desc">नवीनतम पहले</option>
                    <option value="date_asc">पुराने पहले</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-12">
        <div class="container mx-auto px-6">
            
            <?php if (empty($banner_photos)): ?>
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="text-8xl mb-4">🖼️</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">कोई बेनर फोटो उपलब्ध नहीं</h3>
                <p class="text-gray-600">बेनर और फलेक्स फोटो जल्द ही जोड़ी जाएंगी।</p>
            </div>

            <?php else: ?>
            <!-- Photos Grid -->
            <div id="bannersContainer" class="banner-grid">
                
                <?php foreach ($banner_photos as $photo): ?>
                <div class="photo-card bg-white rounded-xl shadow-lg overflow-hidden group" 
                     data-event-name="<?php echo strtolower($photo['event_name']); ?>" 
                     data-event-date="<?php echo $photo['uploaded_at']; ?>">
                    
                    <!-- Image Container -->
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" 
                             alt="<?php echo htmlspecialchars($photo['original_filename']); ?>" 
                             class="w-full h-full object-cover cursor-pointer"
                             onclick="openLightbox('<?php echo htmlspecialchars($photo['file_path']); ?>', '<?php echo htmlspecialchars($photo['original_filename']); ?>')">
                        
                        <!-- Overlay on Hover -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition rounded-lg flex items-center justify-center">
                            <button class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold opacity-0 group-hover:opacity-100 transition"
                                    onclick="openLightbox('<?php echo htmlspecialchars($photo['file_path']); ?>', '<?php echo htmlspecialchars($photo['original_filename']); ?>')">
                                👁️ देखें
                            </button>
                        </div>
                        
                        <!-- Download Button -->
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                            <a href="backend/download-photo.php?photo_id=<?php echo $photo['id']; ?>" 
                               class="bg-green-600 text-white p-2 rounded-full shadow-lg hover:bg-green-700 inline-block transition">
                                ⬇️
                            </a>
                        </div>
                    </div>

                    <!-- Photo Info -->
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($photo['event_name']); ?></h4>
                        <p class="text-sm text-gray-600 mt-1">
                            📅 <?php echo date('d M Y', strtotime($photo['uploaded_at'])); ?>
                        </p>
                        <?php if ($photo['event_location']): ?>
                        <p class="text-sm text-gray-600">
                            📍 <?php echo htmlspecialchars($photo['event_location']); ?>
                        </p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-500 mt-2">
                            📐 <?php echo htmlspecialchars($photo['width'] ?? 'N/A'); ?> × <?php echo htmlspecialchars($photo['height'] ?? 'N/A'); ?> px
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <button onclick="downloadPhoto(<?php echo $photo['id']; ?>, '<?php echo htmlspecialchars($photo['original_filename']); ?>')" 
                                class="w-full bg-orange-600 text-white py-2 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                            📥 डाउनलोड करें
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <!-- Summary -->
            <div class="mt-12 text-center">
                <p class="text-gray-600 text-lg">कुल बेनर फोटो: <strong><?php echo count($banner_photos); ?></strong></p>
            </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
        <div class="relative max-w-5xl w-full">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-4xl font-bold hover:text-orange-500 z-10">
                ×
            </button>
            <img id="lightboxImg" src="" alt="Photo" class="w-full rounded-lg shadow-2xl max-h-[80vh] object-contain">
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex flex-wrap justify-center gap-4">
                <button onclick="downloadCurrentPhoto(); event.stopPropagation();" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    ⬇️ डाउनलोड करें
                </button>
                <button onclick="closeLightbox(); event.stopPropagation();" 
                        class="bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    बंद करें
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
        let allPhotos = <?php echo json_encode($banner_photos); ?>;
        let filteredPhotos = [...allPhotos];
        let currentPhotoId = null;

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterPhotos();
        });

        // Sort functionality
        document.getElementById('sortFilter').addEventListener('change', function() {
            filterPhotos();
        });

        function filterPhotos() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const sort = document.getElementById('sortFilter').value;

            filteredPhotos = allPhotos.filter(photo => {
                const eventName = (photo.event_name || '').toLowerCase();
                const location = (photo.event_location || '').toLowerCase();
                return eventName.includes(search) || location.includes(search);
            });

            // Sort
            if (sort === 'date_asc') {
                filteredPhotos.sort((a, b) => new Date(a.uploaded_at) - new Date(b.uploaded_at));
            } else {
                filteredPhotos.sort((a, b) => new Date(b.uploaded_at) - new Date(a.uploaded_at));
            }

            renderPhotos();
        }

        function renderPhotos() {
            const container = document.getElementById('bannersContainer');
            if (filteredPhotos.length === 0) {
                container.innerHTML = '<div class="col-span-full text-center py-20"><p class="text-gray-600 text-lg">कोई फोटो नहीं मिली।</p></div>';
                return;
            }

            container.innerHTML = filteredPhotos.map(photo => `
                <div class="photo-card bg-white rounded-xl shadow-lg overflow-hidden group">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="${photo.file_path}" 
                             alt="${photo.original_filename}" 
                             class="w-full h-full object-cover cursor-pointer"
                             onclick="openLightbox('${photo.file_path}', '${photo.original_filename}')">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition rounded-lg flex items-center justify-center">
                            <button class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold opacity-0 group-hover:opacity-100 transition"
                                    onclick="openLightbox('${photo.file_path}', '${photo.original_filename}')">
                                👁️ देखें
                            </button>
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                            <a href="backend/download-photo.php?photo_id=${photo.id}" 
                               class="bg-green-600 text-white p-2 rounded-full shadow-lg hover:bg-green-700 inline-block transition">
                                ⬇️
                            </a>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-800 truncate">${photo.event_name}</h4>
                        <p class="text-sm text-gray-600 mt-1">📅 ${new Date(photo.uploaded_at).toLocaleDateString('hi-IN')}</p>
                        ${photo.event_location ? `<p class="text-sm text-gray-600">📍 ${photo.event_location}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-2">📐 ${photo.width ?? 'N/A'} × ${photo.height ?? 'N/A'} px</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <button onclick="downloadPhoto(${photo.id}, '${photo.original_filename}')" 
                                class="w-full bg-orange-600 text-white py-2 rounded-lg font-semibold hover:bg-orange-700 transition text-sm">
                            📥 डाउनलोड करें
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function openLightbox(imagePath, filename) {
            document.getElementById('lightbox').classList.remove('hidden');
            document.getElementById('lightboxImg').src = imagePath;
            currentPhotoId = filename;
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
        }

        function downloadPhoto(photoId, filename) {
            window.location.href = 'backend/download-photo.php?photo_id=' + photoId;
        }

        function downloadCurrentPhoto() {
            if (currentPhotoId) {
                window.location.href = 'backend/download-photo.php?photo_id=' + currentPhotoId;
            }
        }

        // Close lightbox on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>

</body>
</html>

<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    header('Location: photo-admin-login.php');
    exit;
}

require_once 'includes/photo-gallery-db.php';

/*
 IMPORTANT CHANGE:
 - Removed is_moved_to_main filter
 - Photos remain visible until delete_date
*/

// Fetch events with photos
$query = "
SELECT dge.*, COUNT(dgp.id) AS photo_count
FROM download_gallery_events dge
LEFT JOIN download_gallery_photos dgp 
    ON dge.id = dgp.event_id
WHERE dge.is_active = 1
  AND dge.delete_date >= CURDATE()
GROUP BY dge.id
HAVING photo_count > 0
ORDER BY dge.event_date DESC
";

$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Fetch ALL photos for event (no cut logic)
        $photo_query = "
            SELECT * FROM download_gallery_photos
            WHERE event_id = ?
        ";
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
    <title>Move to Main Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

<div class="container mx-auto px-6 py-8">

    <div class="bg-blue-50 border border-blue-300 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-blue-900">📸 Copy Photos to Main Gallery</h2>
        <p class="text-blue-800">
            Selected photos will be <b>copied</b> to main gallery.
            They will remain available in download gallery for 5 days.
        </p>
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <input type="checkbox" id="selectAll"> Select All
        (<span id="selectedCount">0</span> selected)

        <button id="moveBtn" onclick="moveSelectedPhotos()" disabled
            class="ml-4 bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50">
            Copy to Main Gallery
        </button>
    </div>

    <?php foreach ($events as $event): ?>
        <div class="bg-white rounded shadow mb-6">
            <div class="bg-gray-800 text-white p-3 flex justify-between">
                <div>
                    <b><?= htmlspecialchars($event['event_name']) ?></b>
                    (<?= date('d M Y', strtotime($event['event_date'])) ?>)
                </div>
                <label>
                    <input type="checkbox"
                        onchange="selectEventAll(this)"
                        data-event="event_<?= $event['id'] ?>">
                    Select Event
                </label>
            </div>

            <div class="p-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($event['photos'] as $photo): ?>
                    <div class="relative">
                        <input type="checkbox"
                            class="photo-checkbox absolute top-2 left-2 z-10 cursor-pointer"
                            data-event="event_<?= $event['id'] ?>"
                            data-photo="<?= $photo['id'] ?>"
                            onchange="updateSelection()">

                        <img src="<?= htmlspecialchars($photo['file_path']) ?>"
                             class="w-full h-32 object-cover rounded">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<script>
function updateSelection() {
    const count = document.querySelectorAll('.photo-checkbox:checked').length;
    document.getElementById('selectedCount').innerText = count;
    document.getElementById('moveBtn').disabled = count === 0;
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.photo-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelection();
    });

    document.querySelectorAll('.photo-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelection);
    });

    updateSelection();
});

function selectEventAll(el) {
    const eventId = el.dataset.event;
    document.querySelectorAll(`.photo-checkbox[data-event="${eventId}"]`)
        .forEach(cb => cb.checked = el.checked);
    updateSelection();
}

function moveSelectedPhotos() {
    const selected = document.querySelectorAll('.photo-checkbox:checked');
    if (!selected.length) {
        alert('Select photos first');
        return;
    }

    const formData = new FormData();
    selected.forEach(cb => formData.append('photo_ids[]', cb.dataset.photo));

    console.log('=== COPY TO MAIN GALLERY ===');
    console.log('Selected photos:', selected.length);

    fetch('backend/move-photos-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        // Log everything to console BEFORE showing alert
        console.log('=== RESPONSE ===');
        console.log('Full response:', res);
        console.log('Message:', res.message);
        console.log('Moved:', res.moved);
        console.log('Skipped:', res.skipped);
        console.log('Failed:', res.failed);
        
        if (res.debug && res.debug.length > 0) {
            console.log('=== DEBUG INFO ===');
            res.debug.forEach((msg, i) => console.log(`[${i}]`, msg));
        }
        
        console.log('=== END ===');
        
        // Now show alert (user can dismiss without losing console logs)
        alert(res.message);
        
        if (res.moved > 0) {
            location.reload();
        }
    })
    .catch(err => {
        // Log error details BEFORE showing alert
        console.error('=== FETCH ERROR ===');
        console.error('Error:', err);
        console.error('Stack:', err.stack);
        console.error('=== END ===');
        alert('Network error: ' + err.message);
    });
}
</script>

</body>
</html>

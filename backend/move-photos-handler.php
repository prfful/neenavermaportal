<?php
session_start();

/* =========================
   AUTH CHECK
   ========================= */
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

/* =========================
   PATHS
   ========================= */
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/photo-gallery-db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$photo_ids = $_POST['photo_ids'] ?? [];

if (empty($photo_ids) || !is_array($photo_ids)) {
    echo json_encode(['success' => false, 'message' => 'No photos selected']);
    exit;
}

$moved_count  = 0;
$failed_count = 0;

/* =========================
   MAIN GALLERY DIR
   ========================= */
$main_gallery_dir_fs  = $base_dir . '/uploads/gallery/';
$main_gallery_dir_web = '/uploads/gallery/';

if (!file_exists($main_gallery_dir_fs)) {
    mkdir($main_gallery_dir_fs, 0755, true);
}

foreach ($photo_ids as $photo_id) {

    $photo_id = (int)$photo_id;

    /* -------------------------
       FETCH PHOTO
       ------------------------- */
    $stmt = $conn->prepare("
        SELECT dgp.*, dge.program_type, dge.event_date
        FROM download_gallery_photos dgp
        JOIN download_gallery_events dge ON dgp.event_id = dge.id
        WHERE dgp.id = ? AND dgp.is_moved_to_main = 0
    ");
    $stmt->bind_param("i", $photo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        continue;
    }

    $photo = $result->fetch_assoc();
    $stmt->close();

    /* -------------------------
       FILE PATH FIX
       ------------------------- */
    $source_file_fs = $base_dir . $photo['file_path'];

    if (!file_exists($source_file_fs)) {
        $failed_count++;
        continue;
    }

    /* -------------------------
       UNIQUE DESTINATION NAME
       ------------------------- */
    $ext = pathinfo($photo['filename'], PATHINFO_EXTENSION);
    $new_filename = uniqid('gallery_', true) . '.' . $ext;

    $dest_file_fs  = $main_gallery_dir_fs . $new_filename;
    $dest_file_web = $main_gallery_dir_web . $new_filename;

    /* -------------------------
       COPY FILE
       ------------------------- */
    if (!copy($source_file_fs, $dest_file_fs)) {
        $failed_count++;
        continue;
    }

    /* -------------------------
       INSERT INTO MAIN GALLERY
       ------------------------- */
    $insert = $conn->prepare("
        INSERT INTO tbl_gallery (img, event_type, event_date)
        VALUES (?, ?, ?)
    ");
    $insert->bind_param(
        "sss",
        $dest_file_web,
        $photo['program_type'],
        $photo['event_date']
    );

    if (!$insert->execute()) {
        unlink($dest_file_fs);
        $failed_count++;
        $insert->close();
        continue;
    }
    $insert->close();

    /* -------------------------
       MARK AS MOVED
       ------------------------- */
    $update = $conn->prepare("
        UPDATE download_gallery_photos
        SET is_moved_to_main = 1, moved_at = NOW()
        WHERE id = ?
    ");
    $update->bind_param("i", $photo_id);
    $update->execute();
    $update->close();

    $moved_count++;
}

/* =========================
   LOG + RESPONSE
   ========================= */
log_activity(
    $_SESSION['photo_admin_id'] ?? 0,
    'move_to_main_gallery',
    "Moved: $moved_count, Failed: $failed_count"
);

echo json_encode([
    'success'       => true,
    'moved_count'  => $moved_count,
    'failed_count' => $failed_count,
    'message'      => "Moved $moved_count photo(s) to main gallery"
        . ($failed_count > 0 ? " ($failed_count failed)" : "")
]);

$conn->close();

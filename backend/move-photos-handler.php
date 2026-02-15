<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../includes/photo-gallery-db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$photo_ids = $_POST['photo_ids'] ?? [];

if (!is_array($photo_ids) || empty($photo_ids)) {
    echo json_encode(['success' => false, 'message' => 'No photos selected']);
    exit;
}

$copied = 0;
$skipped = 0;
$failed  = 0;

$main_dir = __DIR__ . '/../uploads/gallery/';
if (!is_dir($main_dir)) {
    mkdir($main_dir, 0755, true);
}

foreach ($photo_ids as $photo_id) {
    $photo_id = (int)$photo_id;

    // Fetch photo + event data
    $stmt = $conn->prepare("
        SELECT p.*, e.program_type, e.event_date
        FROM download_gallery_photos p
        JOIN download_gallery_events e ON p.event_id = e.id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $photo_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $failed++;
        continue;
    }

    $photo = $res->fetch_assoc();
    $stmt->close();

    $source = $photo['file_path'];
    if (!file_exists($source)) {
        $failed++;
        continue;
    }

    $filename = basename($source);
    $dest = $main_dir . $filename;
    $db_img_path = 'gallery/' . $filename;

    // 🔒 DUPLICATE PROTECTION
    $chk = $conn->prepare("SELECT id FROM tbl_gallery WHERE img = ?");
    $chk->bind_param("s", $db_img_path);
    $chk->execute();
    $chk->store_result();

    if ($chk->num_rows > 0) {
        $skipped++;
        $chk->close();
        continue;
    }
    $chk->close();

    // 📁 COPY FILE
    if (!copy($source, $dest)) {
        $failed++;
        continue;
    }

    // 🗄️ INSERT INTO MAIN GALLERY
    $ins = $conn->prepare("
        INSERT INTO tbl_gallery (img, event_type, event_date, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $ins->bind_param(
        "sss",
        $db_img_path,
        $photo['program_type'],
        $photo['event_date']
    );

    if ($ins->execute()) {
        $copied++;
    } else {
        unlink($dest); // rollback file
        $failed++;
    }
    $ins->close();
}

echo json_encode([
    'success' => true,
    'message' => "Copied: $copied | Skipped (already exists): $skipped | Failed: $failed",
    'copied'  => $copied,
    'skipped' => $skipped,
    'failed'  => $failed
]);

$conn->close();

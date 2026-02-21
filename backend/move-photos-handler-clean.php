<?php
session_start();
header('Content-Type: application/json');

// Minimal safe checks
if (empty($_SESSION['photo_admin_logged_in'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'POST only']));
}

// Get photo IDs from POST
$photo_ids = [];
if (isset($_POST['photo_ids']) && is_array($_POST['photo_ids'])) {
    foreach ($_POST['photo_ids'] as $id) {
        $photo_ids[] = (int)$id;
    }
}

if (empty($photo_ids)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'No IDs']));
}

// Load database
require_once __DIR__ . '/../includes/photo-gallery-db.php';

if (!$conn) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'DB error']));
}

// Prepare dirs
$root = dirname(dirname(__FILE__));
$gal_dir = $root . '/uploads/gallery/';
@mkdir($gal_dir, 0755, true);

$moved = 0;
$skip = 0;
$fail = 0;

// Process each photo
$debug = [];
foreach ($photo_ids as $pid) {
    // Get photo
    $stmt = $conn->prepare("SELECT p.id, p.file_path, e.program_type, e.event_date FROM download_gallery_photos p JOIN download_gallery_events e ON p.event_id = e.id WHERE p.id = ?");
    if (!$stmt) {
        $fail++;
        continue;
    }
    
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows < 1) {
        $stmt->close();
        $fail++;
        continue;
    }
    
    $row = $res->fetch_assoc();
    $stmt->close();
    
    // Build source path
    $fpath = $row['file_path'];
    $fpath = ltrim($fpath, '/');
    $src = $root . '/' . $fpath;
    
    // Check if file exists  
    if (!file_exists($src)) {
        $debug[] = "Missing: $fpath";
        $fail++;
        continue;
    }
    
    // Copy to gallery
    $fname = basename($src);
    $dst = $gal_dir . $fname;
    $dst_db = 'gallery/' . $fname;
    
    // Skip if already exists
    $chk = $conn->prepare("SELECT id FROM tbl_gallery WHERE img = ?");
    if (!$chk) {
        $fail++;
        continue;
    }
    
    $chk->bind_param("s", $dst_db);
    $chk->execute();
    $chk->store_result();
    
    if ($chk->num_rows > 0) {
        $chk->close();
        $skip++;
        continue;
    }
    $chk->close();
    
    // Copy file
    if (!copy($src, $dst)) {
        $fail++;
        continue;
    }
    
    // Insert into main gallery
    $ins = $conn->prepare("INSERT INTO tbl_gallery (img, event_type, event_date, created_at) VALUES (?, ?, ?, NOW())");
    if (!$ins) {
        unlink($dst);
        $fail++;
        continue;
    }
    
    $ins->bind_param("sss", $dst_db, $row['program_type'], $row['event_date']);
    $ins->execute();
    $ins->close();
    
    $moved++;
}

$conn->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Moved: $moved | Skipped: $skip | Failed: $fail",
    'moved' => $moved,
    'skipped' => $skip,
    'failed' => $fail,
    'debug' => $debug
]);
?>

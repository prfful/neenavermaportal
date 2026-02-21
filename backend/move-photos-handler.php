<?php
// OUTPUT BUFFERING - MUST BE FIRST
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');

$debug = [];
$moved = 0;
$skipped = 0;
$failed = 0;

// Auth check
if (empty($_SESSION['photo_admin_logged_in'])) {
    ob_end_clean();
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Not logged in', 'debug' => []]));
}

// POST check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'POST only', 'debug' => []]));
}

// Get photo IDs
$photo_ids = [];
if (isset($_POST['photo_ids']) && is_array($_POST['photo_ids'])) {
    foreach ($_POST['photo_ids'] as $id) {
        $photo_ids[] = (int)$id;
    }
}

if (empty($photo_ids)) {
    ob_end_clean();
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'No IDs', 'debug' => []]));
}

// Direct DB connection
$conn = new mysqli('localhost', 'u590837060_websitedharfc', 'Dharfc@232111#website1', 'u590837060_Mainsitedb');

if ($conn->connect_error) {
    ob_end_clean();
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'DB error', 'debug' => [$conn->connect_error]]));
}

$conn->set_charset('utf8mb4');

// Directories
$root = dirname(dirname(__FILE__));
$gal_dir = $root . '/uploads/gallery/';
@mkdir($gal_dir, 0755, true);

// Process photos
foreach ($photo_ids as $pid) {
    $stmt = $conn->prepare("SELECT p.id, p.event_id, p.file_path, e.program_type, e.event_date, e.event_name FROM download_gallery_photos p JOIN download_gallery_events e ON p.event_id = e.id WHERE p.id = ?");
    
    if (!$stmt) {
        $debug[] = "Query error: " . $conn->error;
        $failed++;
        continue;
    }
    
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows < 1) {
        $stmt->close();
        $debug[] = "Photo $pid not found";
        $failed++;
        continue;
    }
    
    $row = $res->fetch_assoc();
    $stmt->close();
    
    // Source path
    $fpath = $row['file_path'];
    $fpath = ltrim($fpath, '/');
    $src = $root . '/' . $fpath;
    
    if (!file_exists($src)) {
        $debug[] = "Missing: $fpath";
        $failed++;
        continue;
    }
    
    // Destination
    $fname = basename($src);
    $dst = $gal_dir . $fname;
    $dst_db = 'uploads/gallery/' . $fname;
    
    // Check duplicate
    $chk = $conn->prepare("SELECT id FROM gallery WHERE image_path = ?");
    if (!$chk) {
        $debug[] = "Duplicate check SQL error: " . $conn->error;
        $failed++;
        continue;
    }
    
    $chk->bind_param("s", $dst_db);
    if (!$chk->execute()) {
        $debug[] = "Duplicate check execute error: " . $chk->error;
        $chk->close();
        $failed++;
        continue;
    }
    $chk->store_result();
    
    if ($chk->num_rows > 0) {
        $chk->close();
        $debug[] = "Exists: $fname";
        $skipped++;
        continue;
    }
    $chk->close();
    
    // Copy file
    if (!copy($src, $dst)) {
        $debug[] = "Copy failed: $fname";
        $failed++;
        continue;
    }
    
    // Insert DB
    $ins = $conn->prepare("INSERT INTO gallery (event_name, event_date, image_path) VALUES (?, ?, ?)");
    if (!$ins) {
        @unlink($dst);
        $debug[] = "Insert error";
        $failed++;
        continue;
    }
    
    $ins->bind_param("sss", $row['event_name'], $row['event_date'], $dst_db);
    
    if ($ins->execute()) {
        $debug[] = "OK: $fname";
        $moved++;
    } else {
        @unlink($dst);
        $debug[] = "Insert failed: " . $conn->error;
        $failed++;
    }
    $ins->close();
}

$conn->close();

// Clean buffer and output JSON
ob_end_clean();
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Moved: $moved | Skipped: $skipped | Failed: $failed",
    'moved' => $moved,
    'skipped' => $skipped,
    'failed' => $failed,
    'debug' => $debug
]);
?>

<?php
require_once '../includes/photo-gallery-db.php';

if (!isset($_GET['event_id'])) {
    http_response_code(400);
    die('Invalid request');
}

$event_id = intval($_GET['event_id']);

// Get event details
$event_stmt = $conn->prepare("SELECT event_name, event_date FROM download_gallery_events WHERE id = ?");
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();

if ($event_result->num_rows === 0) {
    http_response_code(404);
    die('Event not found');
}

$event = $event_result->fetch_assoc();
$event_stmt->close();

// Get all photos for this event
$photo_stmt = $conn->prepare("SELECT * FROM download_gallery_photos WHERE event_id = ? AND is_moved_to_main = 0");
$photo_stmt->bind_param("i", $event_id);
$photo_stmt->execute();
$photo_result = $photo_stmt->get_result();

if ($photo_result->num_rows === 0) {
    http_response_code(404);
    die('No photos found');
}

// Create ZIP file
$zip_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event['event_name']) . '_' . date('Ymd') . '.zip';
$zip_path = sys_get_temp_dir() . '/' . $zip_filename;

$zip = new ZipArchive();
if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die('Failed to create ZIP file');
}

// Add photos to ZIP
$added_count = 0;
while ($photo = $photo_result->fetch_assoc()) {
    if (file_exists($photo['file_path'])) {
        $zip->addFile($photo['file_path'], $photo['original_filename']);
        $added_count++;
    }
}

$zip->close();
$photo_stmt->close();

if ($added_count === 0) {
    unlink($zip_path);
    die('No valid photos found');
}

// Log bulk download
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$log_stmt = $conn->prepare("INSERT INTO photo_download_log (photo_id, event_id, ip_address, user_agent) VALUES (0, ?, ?, ?)");
$log_stmt->bind_param("iss", $event_id, $ip_address, $user_agent);
$log_stmt->execute();
$log_stmt->close();

$conn->close();

// Send ZIP file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
header('Content-Length: ' . filesize($zip_path));
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate');

readfile($zip_path);
unlink($zip_path); // Delete temp file after sending

exit;
?>

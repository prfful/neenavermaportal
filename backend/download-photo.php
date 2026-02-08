<?php
require_once 'includes/photo-gallery-db.php';

if (!isset($_GET['photo_id'])) {
    http_response_code(400);
    die('Invalid request');
}

$photo_id = intval($_GET['photo_id']);

// Get photo details
$stmt = $conn->prepare("SELECT * FROM download_gallery_photos WHERE id = ? AND is_moved_to_main = 0");
$stmt->bind_param("i", $photo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die('Photo not found');
}

$photo = $result->fetch_assoc();
$file_path = $photo['file_path'];

// Check if file exists
if (!file_exists($file_path)) {
    http_response_code(404);
    die('File not found');
}

// Increment download counter
$update_stmt = $conn->prepare("UPDATE download_gallery_photos SET download_count = download_count + 1 WHERE id = ?");
$update_stmt->bind_param("i", $photo_id);
$update_stmt->execute();
$update_stmt->close();

// Log download
$log_stmt = $conn->prepare("INSERT INTO photo_download_log (photo_id, event_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
$event_id = $photo['event_id'];
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$log_stmt->bind_param("iiss", $photo_id, $event_id, $ip_address, $user_agent);
$log_stmt->execute();
$log_stmt->close();

$stmt->close();
$conn->close();

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: ' . $photo['mime_type']);
header('Content-Disposition: attachment; filename="' . $photo['original_filename'] . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Clear output buffer
ob_clean();
flush();

// Read file and output
readfile($file_path);
exit;
?>

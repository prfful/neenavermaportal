<?php
session_start();

/* =========================
   FORCE TIMEZONE (IMPORTANT)
   ========================= */
date_default_timezone_set('Asia/Kolkata');

/* =========================
   AUTH CHECK
   ========================= */
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/photo-gallery-db.php';

$response = ['success' => false, 'message' => ''];
$log_file = $base_dir . '/uploads/upload_debug.log';

function log_debug($msg) {
    global $log_file;
    file_put_contents(
        $log_file,
        "[" . date('Y-m-d H:i:s') . "] " . $msg . PHP_EOL,
        FILE_APPEND
    );
}

/* =========================
   POST REQUEST HANDLING
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -------------------------
       SANITIZE INPUTS
       ------------------------- */
    $event_name      = sanitize_input($_POST['event_name'] ?? '');
    $event_date_raw  = sanitize_input($_POST['event_date'] ?? '');
    $program_type    = sanitize_input($_POST['program_type'] ?? '');
    $event_location  = sanitize_input($_POST['event_location'] ?? '');
    $description     = sanitize_input($_POST['description'] ?? '');

    if (empty($event_name) || empty($event_date_raw) || empty($program_type)) {
        $msg = 'Please fill all required fields';
        header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
        exit;
    }

    /* -------------------------
       SAFE DATE HANDLING
       ------------------------- */
    try {
        // Expecting event_date in YYYY-MM-DD
        $eventDateObj = new DateTime($event_date_raw);
    } catch (Exception $e) {
        log_debug("INVALID event_date received: " . $event_date_raw);
        $msg = 'Invalid event date format';
        header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
        exit;
    }

    // Clone and add 5 days
    $deleteDateObj = clone $eventDateObj;
    $deleteDateObj->modify('+5 days');

    // Database-safe formats
    $event_date  = $eventDateObj->format('Y-m-d');
    $delete_date = $deleteDateObj->format('Y-m-d');

    // Optional ISO format (useful for frontend if needed)
    $delete_date_iso = $deleteDateObj->format('Y-m-d\T00:00:00P');

    log_debug("Event date: $event_date | Delete date: $delete_date");

    /* -------------------------
       CREATE UPLOAD DIRECTORY
       ------------------------- */
    $upload_base_dir_rel = 'uploads/download_gallery';
    $event_dir_rel = $upload_base_dir_rel . '/'
        . $eventDateObj->format('Y-m')
        . '/'
        . preg_replace('/[^a-zA-Z0-9_-]/', '_', $event_name);

    $event_dir_fs = $base_dir . '/' . $event_dir_rel . '/';

    if (!file_exists($event_dir_fs)) {
        if (!mkdir($event_dir_fs, 0755, true)) {
            $msg = 'Failed to create upload directory';
            header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
            exit;
        }
    }

    /* -------------------------
       INSERT EVENT
       ------------------------- */
    $stmt = $conn->prepare(
        "INSERT INTO download_gallery_events
        (event_name, event_date, program_type, event_location, description, delete_date, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $created_by = $_SESSION['photo_admin_username'];
    $stmt->bind_param(
        "sssssss",
        $event_name,
        $event_date,
        $program_type,
        $event_location,
        $description,
        $delete_date,
        $created_by
    );

    if (!$stmt->execute()) {
        $msg = 'Database error: ' . $stmt->error;
        header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
        exit;
    }

    $event_id = $stmt->insert_id;
    $stmt->close();

    /* -------------------------
       FILE UPLOADS
       ------------------------- */
    $uploaded_count = 0;
    $failed_count   = 0;

    if (!empty($_FILES['photos']['name'][0])) {

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $total_files  = count($_FILES['photos']['name']);

        for ($i = 0; $i < $total_files; $i++) {

            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                $failed_count++;
                continue;
            }

            $file_name = $_FILES['photos']['name'][$i];
            $file_tmp  = $_FILES['photos']['tmp_name'][$i];
            $file_size = $_FILES['photos']['size'][$i];
            $file_type = $_FILES['photos']['type'][$i];

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_exts)) {
                $failed_count++;
                continue;
            }

            if ($file_size > 5 * 1024 * 1024) {
                $failed_count++;
                continue;
            }

            $unique_filename = time() . '_' . uniqid('', true) . '.' . $file_ext;
            $target_file     = $event_dir_fs . $unique_filename;
            $web_file_path   = '/' . $event_dir_rel . '/' . $unique_filename;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                $failed_count++;
                continue;
            }

            $image_data = @getimagesize($target_file);
            if (!$image_data) {
                unlink($target_file);
                $failed_count++;
                continue;
            }

            [$width, $height] = $image_data;

            $stmt = $conn->prepare(
                "INSERT INTO download_gallery_photos
                (event_id, filename, original_filename, file_path, file_size, mime_type, width, height)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "isssisii",
                $event_id,
                $unique_filename,
                $file_name,
                $web_file_path,
                $file_size,
                $file_type,
                $width,
                $height
            );

            if ($stmt->execute()) {
                $uploaded_count++;
            } else {
                unlink($target_file);
                $failed_count++;
            }

            $stmt->close();
        }
    }

    /* -------------------------
       ACTIVITY LOG
       ------------------------- */
    log_activity(
        $_SESSION['photo_admin_id'] ?? 0,
        'upload_photos',
        "Event: $event_name | Uploaded: $uploaded_count | Failed: $failed_count"
    );

    /* -------------------------
       REDIRECT
       ------------------------- */
    $msg = "✓ Event created successfully! Uploaded: $uploaded_count";
    if ($failed_count > 0) {
        $msg .= " | Failed: $failed_count";
    }

    header('Location: /photo-admin-dashboard.php?success=' . urlencode($msg));
    exit;

}

/* =========================
   FALLBACK
   ========================= */
header('Location: /photo-admin-upload.php');
exit;

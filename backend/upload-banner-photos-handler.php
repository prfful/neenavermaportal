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
    // Use defaults for banner photos if details not provided
    $event_name      = sanitize_input($_POST['banner_event_name'] ?? '');
    $event_date_raw  = sanitize_input($_POST['banner_event_date'] ?? '');
    $event_location  = sanitize_input($_POST['banner_event_location'] ?? '');
    $description     = sanitize_input($_POST['banner_description'] ?? '');
    $dimensions      = sanitize_input($_POST['banner_dimensions'] ?? 'High-Definition');

    // Use defaults if fields are empty
    if (empty($event_name)) {
        $event_name = 'बेनर फोटो - ' . date('Y-m-d H:i');
    }
    if (empty($event_date_raw)) {
        $event_date_raw = date('Y-m-d');
    }
    if (empty($event_location)) {
        $event_location = 'विभिन्न स्थान';
    }

    /* -------------------------
       SAFE DATE HANDLING
       ------------------------- */
    try {
        // Expecting event_date in YYYY-MM-DD
        $eventDateObj = new DateTime($event_date_raw);
    } catch (Exception $e) {
        log_debug("INVALID event_date received: " . $event_date_raw);
        $msg = 'अमान्य घटना तारीख प्रारूप';
        header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
        exit;
    }

    // Clone and add 30 days for banner photos (can be longer)
    $deleteDateObj = clone $eventDateObj;
    $deleteDateObj->modify('+30 days');

    // Database-safe formats
    $event_date  = $eventDateObj->format('Y-m-d');
    $delete_date = $deleteDateObj->format('Y-m-d');

    log_debug("Banner event date: $event_date | Delete date: $delete_date");

    /* -------------------------
       CREATE UPLOAD DIRECTORY
       ------------------------- */
    $upload_base_dir_rel = 'uploads/banner_photos';
    $event_dir_rel = $upload_base_dir_rel . '/'
        . $eventDateObj->format('Y-m')
        . '/'
        . preg_replace('/[^a-zA-Z0-9_-]/', '_', $event_name);

    $event_dir_fs = $base_dir . '/' . $event_dir_rel . '/';

    if (!file_exists($event_dir_fs)) {
        if (!mkdir($event_dir_fs, 0755, true)) {
            $msg = 'अपलोड निर्देशिका बनाने में विफल';
            header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
            exit;
        }
    }

    /* -------------------------
       CHECK IF EVENT EXISTS
       IF NOT, CREATE ONE
       ------------------------- */
    $check_event = "SELECT id FROM download_gallery_events 
                   WHERE event_name = ? AND event_date = ? 
                   LIMIT 1";
    $stmt = $conn->prepare($check_event);
    $stmt->bind_param("ss", $event_name, $event_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $event_row = $result->fetch_assoc();
        $event_id = $event_row['id'];
    } else {
        // Create new event for banner photos
        $program_type = 'बेनर फोटो';
        
        $stmt = $conn->prepare(
            "INSERT INTO download_gallery_events
            (event_name, event_date, program_type, event_location, description, delete_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $created_by = $_SESSION['photo_admin_username'] ?? 'admin';
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
            log_debug("Database error: $msg");
            header('Location: /photo-admin-upload.php?error=' . urlencode($msg));
            exit;
        }

        $event_id = $stmt->insert_id;
        $stmt->close();
    }

    /* -------------------------
       FILE UPLOADS
       ------------------------- */
    $uploaded_count = 0;
    $failed_count   = 0;
    log_debug("EVENT_ID: $event_id | FILES: " . (isset($_FILES['banner_photos']) ? json_encode($_FILES['banner_photos']['name']) : 'NONE'));

    if (!empty($_FILES['banner_photos']['name'][0])) {
        log_debug("=== BANNER FILE UPLOAD START ===");

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        $total_files  = count($_FILES['banner_photos']['name']);
        $max_file_size = 10 * 1024 * 1024; // 10MB for banner photos
        log_debug("Total banner files: $total_files | Max size: " . ($max_file_size / 1024 / 1024) . "MB");

        for ($i = 0; $i < $total_files; $i++) {
            log_debug("Processing banner file $i");

            if ($_FILES['banner_photos']['error'][$i] !== UPLOAD_ERR_OK) {
                log_debug("File $i upload error: " . $_FILES['banner_photos']['error'][$i]);
                $failed_count++;
                continue;
            }

            $file_name = $_FILES['banner_photos']['name'][$i];
            $file_tmp  = $_FILES['banner_photos']['tmp_name'][$i];
            $file_size = $_FILES['banner_photos']['size'][$i];
            $file_type = $_FILES['banner_photos']['type'][$i];
            log_debug("File $i: name=$file_name | size=$file_size | type=$file_type");

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validation
            if (!in_array($file_ext, $allowed_exts)) {
                log_debug("Invalid file extension: $file_ext for file: $file_name");
                $failed_count++;
                continue;
            }

            if ($file_size > $max_file_size) {
                log_debug("File size exceeded: $file_size bytes for file: $file_name");
                $failed_count++;
                continue;
            }

            // Generate unique filename
            $unique_filename = time() . '_' . uniqid('', true) . '.' . $file_ext;
            $target_file     = $event_dir_fs . $unique_filename;
            $web_file_path   = '/' . $event_dir_rel . '/' . $unique_filename;

            // Move uploaded file
            if (!move_uploaded_file($file_tmp, $target_file)) {
                log_debug("Failed to move uploaded file: $file_name to $target_file");
                $failed_count++;
                continue;
            }

            // Get image dimensions
            $image_data = @getimagesize($target_file);
            if (!$image_data) {
                log_debug("Failed to get image size for: $target_file");
                unlink($target_file);
                $failed_count++;
                continue;
            }

            [$width, $height] = $image_data;

            // Insert into database with is_banner_photo = 1
            $stmt = $conn->prepare(
                "INSERT INTO download_gallery_photos
                (event_id, filename, original_filename, file_path, file_size, mime_type, width, height, is_banner_photo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
            );
            log_debug("DB prepare done for file $i");

            if (!$stmt) {
                log_debug("DB prepare FAILED for file $i: " . $conn->error);
                unlink($target_file);
                $failed_count++;
                continue;
            }

            $stmt->bind_param(
                "isssissi",
                $event_id,
                $unique_filename,
                $file_name,
                $web_file_path,
                $file_size,
                $file_type,
                $width,
                $height
            );
            log_debug("DB bind_param done for file $i");

            if ($stmt->execute()) {
                $photo_id = $stmt->insert_id;
                log_debug("File $i inserted with photo_id=$photo_id");
                $stmt->close();

                // Also insert into banner_photo_uploads tracking table
                $admin_id = $_SESSION['photo_admin_id'] ?? 0;
                $stmt = $conn->prepare(
                    "INSERT INTO banner_photo_uploads
                    (photo_id, admin_id, description, dimensions_desc)
                    VALUES (?, ?, ?, ?)"
                );
                log_debug("Preparing banner_photo_uploads insert for photo_id=$photo_id");
                
                if (!$stmt) {
                    log_debug("Banner tracking insert FAILED: " . $conn->error);
                } else {
                    $stmt->bind_param("iiss", $photo_id, $admin_id, $description, $dimensions);
                    if ($stmt->execute()) {
                        log_debug("Banner tracking inserted successfully");
                        $uploaded_count++;
                    } else {
                        log_debug("Banner tracking execute FAILED: " . $stmt->error);
                    }
                    $stmt->close();
                }
            } else {
                log_debug("Photo insert failed: " . $stmt->error);
                unlink($target_file);
                $failed_count++;
                $stmt->close();
            }
        }
        log_debug("=== BANNER FILE UPLOAD END | Uploaded: $uploaded_count | Failed: $failed_count ===");
    }

    /* -------------------------
       ACTIVITY LOG
       ------------------------- */
    log_activity(
        $_SESSION['photo_admin_id'] ?? 0,
        'upload_banner_photos',
        "Event: $event_name | Uploaded: $uploaded_count | Failed: $failed_count"
    );

    /* -------------------------
       REDIRECT
       ------------------------- */
    $msg = "✓ बेनर फोटो सफलतापूर्वक अपलोड किए गए! अपलोड किए गए: $uploaded_count";
    if ($failed_count > 0) {
        $msg .= " | विफल: $failed_count";
    }

    log_debug("Upload complete: $msg");
    header('Location: /photo-admin-dashboard.php?success=' . urlencode($msg));
    exit;

}

/* =========================
   FALLBACK
   ========================= */
header('Location: /photo-admin-upload.php');
exit;
?>

<?php
session_start();

// Check if logged in
if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/photo-gallery-db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate form data
    $event_name = sanitize_input($_POST['event_name'] ?? '');
    $event_date = sanitize_input($_POST['event_date'] ?? '');
    $program_type = sanitize_input($_POST['program_type'] ?? '');
    $event_location = sanitize_input($_POST['event_location'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    
    if (empty($event_name) || empty($event_date) || empty($program_type)) {
        $response['message'] = 'Please fill all required fields';
        header('Location: /photo-admin-upload.php?error=' . urlencode($response['message']));
        exit;
    }
    
    // Calculate delete date (5 days after event date)
    $delete_date = date('Y-m-d', strtotime($event_date . ' + 5 days'));
    
    // Create upload directory if not exists
    $upload_base_dir = 'uploads/download_gallery/';
    $event_dir = $upload_base_dir . date('Y-m', strtotime($event_date)) . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $event_name) . '/';
    
    if (!file_exists($event_dir)) {
        if (!mkdir($event_dir, 0755, true)) {
            $response['message'] = 'Failed to create upload directory';
            header('Location: /photo-admin-upload.php?error=' . urlencode($response['message']));
            exit;
        }
    }
    
    // Insert event into database
    $stmt = $conn->prepare("INSERT INTO download_gallery_events (event_name, event_date, program_type, event_location, description, delete_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $created_by = $_SESSION['photo_admin_username'];
    $stmt->bind_param("sssssss", $event_name, $event_date, $program_type, $event_location, $description, $delete_date, $created_by);
    
    if (!$stmt->execute()) {
        $response['message'] = 'Database error: ' . $stmt->error;
        header('Location: /photo-admin-upload.php?error=' . urlencode($response['message']));
        exit;
    }
    
    $event_id = $stmt->insert_id;
    $stmt->close();
    
    // Process uploaded files
    $uploaded_count = 0;
    $failed_count = 0;
    
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $total_files = count($_FILES['photos']['name']);
        
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['photos']['name'][$i];
            $file_tmp = $_FILES['photos']['tmp_name'][$i];
            $file_size = $_FILES['photos']['size'][$i];
            $file_error = $_FILES['photos']['error'][$i];
            $file_type = $_FILES['photos']['type'][$i];
            
            // Skip if error
            if ($file_error !== UPLOAD_ERR_OK) {
                $failed_count++;
                continue;
            }
            
            // Validate file type by extension (browser MIME type can be unreliable)
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_exts)) {
                $failed_count++;
                error_log("Upload rejected: Invalid extension: $file_ext");
                continue;
            }
            
            // Validate file size (max 5MB)
            if ($file_size > 5 * 1024 * 1024) {
                $failed_count++;
                error_log("Upload rejected: File too large: $file_size bytes");
                continue;
            }
            
            // Generate unique filename
            $unique_filename = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $event_dir . $unique_filename;
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Verify it's actually an image
                $image_data = @getimagesize($target_file);
                if (!$image_data) {
                    error_log("Upload rejected: Not a valid image: $target_file");
                    unlink($target_file);
                    $failed_count++;
                    continue;
                }
                list($width, $height) = $image_data;
                
                // Insert photo into database
                $stmt = $conn->prepare("INSERT INTO download_gallery_photos (event_id, filename, original_filename, file_path, file_size, mime_type, width, height) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssissii", $event_id, $unique_filename, $file_name, $target_file, $file_size, $file_type, $width, $height);
                
                if ($stmt->execute()) {
                    $uploaded_count++;
                } else {
                    $failed_count++;
                    error_log("DB insert failed: " . $stmt->error);
                    unlink($target_file);
                }
                $stmt->close();
            } else {
                $failed_count++;
                error_log("move_uploaded_file failed for: $file_name to: $target_file");
            }
        }
    }
    
    // Log activity
    log_activity($_SESSION['photo_admin_id'] ?? 0, 'upload_photos', "Event: $event_name, Uploaded: $uploaded_count, Failed: $failed_count");
    
    // Redirect with success message
    $message = "✓ Event created successfully! Uploaded: $uploaded_count photos";
    if ($failed_count > 0) {
        $message .= " | Failed: $failed_count photos";
    }
    
    header('Location: /photo-admin-dashboard.php?success=' . urlencode($message));
    exit;

} else {
    header('Location: /photo-admin-upload.php');
    exit;
}
?>

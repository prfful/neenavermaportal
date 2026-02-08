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
    
    $photo_ids = $_POST['photo_ids'] ?? [];
    
    if (empty($photo_ids) || !is_array($photo_ids)) {
        echo json_encode(['success' => false, 'message' => 'No photos selected']);
        exit;
    }
    
    $moved_count = 0;
    $failed_count = 0;
    
    foreach ($photo_ids as $photo_id) {
        $photo_id = intval($photo_id);
        
        // Get photo details
        $stmt = $conn->prepare("SELECT dgp.*, dge.event_name, dge.event_date, dge.program_type 
                               FROM download_gallery_photos dgp 
                               JOIN download_gallery_events dge ON dgp.event_id = dge.id 
                               WHERE dgp.id = ? AND dgp.is_moved_to_main = 0");
        $stmt->bind_param("i", $photo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $photo = $result->fetch_assoc();
            
            // Copy file to main gallery uploads folder
            $main_gallery_dir = 'uploads/gallery/';
            if (!file_exists($main_gallery_dir)) {
                mkdir($main_gallery_dir, 0755, true);
            }
            
            $source_file = $photo['file_path'];
            $dest_file = $main_gallery_dir . $photo['filename'];
            
            if (file_exists($source_file) && copy($source_file, $dest_file)) {
                
                // Insert into main gallery table (assuming your existing gallery table structure)
                // TODO: Adjust this query based on your actual gallery table structure
                $insert_stmt = $conn->prepare("INSERT INTO tbl_gallery (img, event_type, event_date) VALUES (?, ?, ?)");
                $img_path = 'gallery/' . $photo['filename'];
                $insert_stmt->bind_param("sss", $img_path, $photo['program_type'], $photo['event_date']);
                
                if ($insert_stmt->execute()) {
                    // Mark as moved in download gallery
                    $update_stmt = $conn->prepare("UPDATE download_gallery_photos SET is_moved_to_main = 1, moved_at = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $photo_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    $moved_count++;
                } else {
                    $failed_count++;
                    // Delete copied file if database insert fails
                    unlink($dest_file);
                }
                $insert_stmt->close();
                
            } else {
                $failed_count++;
            }
        }
        $stmt->close();
    }
    
    // Log activity
    log_activity($_SESSION['photo_admin_id'] ?? 0, 'move_to_main_gallery', "Moved: $moved_count, Failed: $failed_count");
    
    echo json_encode([
        'success' => true,
        'moved_count' => $moved_count,
        'failed_count' => $failed_count,
        'message' => "Successfully moved $moved_count photo(s) to main gallery" . ($failed_count > 0 ? " ($failed_count failed)" : "")
    ]);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>

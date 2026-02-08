<?php
/**
 * Cron Job: Auto-delete expired photos
 * Run this script daily via cron job
 * 
 * Example crontab entry:
 * 0 2 * * * php /path/to/cron-cleanup.php
 * 
 * This deletes photos after 5 days from event date
 */

require_once __DIR__ . '/includes/photo-gallery-db.php';

$log_file = __DIR__ . '/logs/cleanup.log';
$log_dir = dirname($log_file);

// Create logs directory if doesn't exist
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Log function
function write_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

write_log("=== Cleanup Script Started ===");

try {
    // Find expired events
    $query = "SELECT dge.id as event_id, dge.event_name, dge.delete_date, 
              COUNT(dgp.id) as photo_count
              FROM download_gallery_events dge
              LEFT JOIN download_gallery_photos dgp ON dge.id = dgp.event_id AND dgp.is_moved_to_main = 0
              WHERE dge.delete_date < CURDATE()
              GROUP BY dge.id";
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $total_deleted_photos = 0;
        $total_deleted_events = 0;
        
        while ($row = $result->fetch_assoc()) {
            $event_id = $row['event_id'];
            $event_name = $row['event_name'];
            $photo_count = $row['photo_count'];
            
            write_log("Processing Event ID: $event_id - $event_name ($photo_count photos)");
            
            // Get all photos for this event
            $photo_query = "SELECT id, file_path FROM download_gallery_photos 
                           WHERE event_id = ? AND is_moved_to_main = 0";
            $stmt = $conn->prepare($photo_query);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $photo_result = $stmt->get_result();
            
            $deleted_files = 0;
            while ($photo = $photo_result->fetch_assoc()) {
                // Delete physical file
                if (file_exists($photo['file_path'])) {
                    if (unlink($photo['file_path'])) {
                        $deleted_files++;
                        write_log("  - Deleted file: {$photo['file_path']}");
                    } else {
                        write_log("  ! Failed to delete file: {$photo['file_path']}");
                    }
                }
                
                // Delete from database
                $delete_stmt = $conn->prepare("DELETE FROM download_gallery_photos WHERE id = ?");
                $delete_stmt->bind_param("i", $photo['id']);
                $delete_stmt->execute();
                $delete_stmt->close();
            }
            $stmt->close();
            
            // Delete event from database
            $delete_event_stmt = $conn->prepare("DELETE FROM download_gallery_events WHERE id = ?");
            $delete_event_stmt->bind_param("i", $event_id);
            
            if ($delete_event_stmt->execute()) {
                $total_deleted_events++;
                write_log("  ✓ Event deleted: $event_name");
            } else {
                write_log("  ! Failed to delete event: $event_name");
            }
            $delete_event_stmt->close();
            
            $total_deleted_photos += $deleted_files;
            
            // Try to remove empty directory
            $event_dir = dirname($photo['file_path'] ?? '');
            if ($event_dir && is_dir($event_dir)) {
                $files = scandir($event_dir);
                if (count($files) <= 2) { // Only . and ..
                    rmdir($event_dir);
                    write_log("  - Removed empty directory: $event_dir");
                }
            }
        }
        
        write_log("Summary: Deleted $total_deleted_events events, $total_deleted_photos photos");
        
    } else {
        write_log("No expired events found");
    }
    
} catch (Exception $e) {
    write_log("ERROR: " . $e->getMessage());
}

write_log("=== Cleanup Script Finished ===\n");

$conn->close();
?>

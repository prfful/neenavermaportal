<?php
/**
 * Cron Job: Auto-delete expired gallery events & photos
 * Runs daily via cron
 */

/* =========================
   SECURITY: CLI ONLY
   ========================= */
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

/* =========================
   TIMEZONE
   ========================= */
date_default_timezone_set('Asia/Kolkata');

/* =========================
   PATHS
   ========================= */
$base_dir = dirname(__DIR__); // project root
require_once $base_dir . '/includes/photo-gallery-db.php';

$log_file = $base_dir . '/logs/cleanup.log';
$log_dir  = dirname($log_file);

if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

/* =========================
   LOG FUNCTION
   ========================= */
function write_log($msg) {
    global $log_file;
    file_put_contents(
        $log_file,
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
}

write_log("=== Cleanup Script Started ===");

$today = date('Y-m-d');

try {

    /* =========================
       FIND EXPIRED EVENTS
       ========================= */
    $sql = "
        SELECT dge.id, dge.event_name
        FROM download_gallery_events dge
        WHERE dge.delete_date < ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $events = $stmt->get_result();

    if ($events->num_rows === 0) {
        write_log("No expired events found");
    }

    while ($event = $events->fetch_assoc()) {

        $event_id   = $event['id'];
        $event_name = $event['event_name'];

        write_log("Processing Event: [$event_id] $event_name");

        /* =========================
           FETCH PHOTOS
           ========================= */
        $photoStmt = $conn->prepare(
            "SELECT id, file_path FROM download_gallery_photos WHERE event_id = ?"
        );
        $photoStmt->bind_param("i", $event_id);
        $photoStmt->execute();
        $photos = $photoStmt->get_result();

        $event_dirs = [];

        while ($photo = $photos->fetch_assoc()) {

            // Convert web path → filesystem path
            $fs_path = $base_dir . $photo['file_path'];

            if (file_exists($fs_path)) {
                if (unlink($fs_path)) {
                    write_log("  ✓ Deleted file: $fs_path");
                } else {
                    write_log("  ! Failed to delete file: $fs_path");
                }
            } else {
                write_log("  ! File not found: $fs_path");
            }

            $event_dirs[] = dirname($fs_path);

            // Delete photo record
            $delPhoto = $conn->prepare(
                "DELETE FROM download_gallery_photos WHERE id = ?"
            );
            $delPhoto->bind_param("i", $photo['id']);
            $delPhoto->execute();
            $delPhoto->close();
        }

        $photoStmt->close();

        /* =========================
           DELETE EVENT
           ========================= */
        $delEvent = $conn->prepare(
            "DELETE FROM download_gallery_events WHERE id = ?"
        );
        $delEvent->bind_param("i", $event_id);
        $delEvent->execute();
        $delEvent->close();

        write_log("  ✓ Event deleted: $event_name");

        /* =========================
           CLEAN EMPTY DIRECTORIES
           ========================= */
        foreach (array_unique($event_dirs) as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                if (count($files) <= 2) {
                    rmdir($dir);
                    write_log("  ✓ Removed empty directory: $dir");
                }
            }
        }
    }

} catch (Throwable $e) {
    write_log("ERROR: " . $e->getMessage());
}

write_log("=== Cleanup Script Finished ===\n");

$conn->close();

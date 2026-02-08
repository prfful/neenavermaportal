<?php
/**
 * Database connection for Photo Download Gallery
 * Uses existing db_connect.php or creates new connection
 */

// Check if main database connection exists
if (!isset($conn) || $conn === null) {
    require_once __DIR__ . '/../db_connect.php';
}

// Verify connection
if (!isset($conn) || $conn->connect_error) {
    die("Database Connection Error: " . ($conn->connect_error ?? "Connection not available"));
}

// Ensure charset is set for proper Hindi support
if (!$conn->get_charset()) {
    $conn->set_charset("utf8mb4");
}

/**
 * Helper function to sanitize input
 */
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Helper function to log activity
 */
function log_activity($admin_id, $action, $details = null) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $stmt = $conn->prepare("INSERT INTO photo_gallery_activity_log (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $admin_id, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

/**
 * Helper function to check if admin is logged in
 */
function check_admin_login() {
    session_start();
    if (!isset($_SESSION['photo_admin_logged_in']) || $_SESSION['photo_admin_logged_in'] !== true) {
        header('Location: photo-admin-login.php');
        exit;
    }
}
?>

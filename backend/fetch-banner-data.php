<?php
/**
 * Fetch banner/flex photos from database
 */

// Support both direct include from root and AJAX calls from backend
$base_dir = dirname(__DIR__);
require_once $base_dir . '/includes/photo-gallery-db.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

// Build query for banner photos only
$query = "SELECT dgp.*, dge.event_name, dge.event_date, dge.event_location 
          FROM download_gallery_photos dgp
          INNER JOIN download_gallery_events dge ON dgp.event_id = dge.id
          WHERE dgp.is_banner_photo = 1 AND dge.is_active = 1";

if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (dge.event_name LIKE '%$search_term%' OR dge.event_location LIKE '%$search_term%')";
}

// Sorting (latest first by default)
switch ($sort) {
    case 'date_asc':
        $query .= " ORDER BY dgp.uploaded_at ASC, dgp.id ASC";
        break;
    case 'name_asc':
        $query .= " ORDER BY dge.event_name ASC";
        break;
    case 'date_desc':
    default:
        $query .= " ORDER BY dgp.uploaded_at DESC, dgp.id DESC";
        break;
}

$result = $conn->query($query);
$banner_photos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $banner_photos[] = $row;
    }
}

// Return as JSON for AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'photos' => $banner_photos]);
    exit;
}

// Return data for PHP include
return $banner_photos;
?>

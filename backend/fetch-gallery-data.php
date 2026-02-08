<?php
require_once 'includes/photo-gallery-db.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$program_type = $_GET['program_type'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

// Build query
$query = "SELECT dge.*, COUNT(dgp.id) as photo_count 
          FROM download_gallery_events dge 
          LEFT JOIN download_gallery_photos dgp ON dge.id = dgp.event_id AND dgp.is_moved_to_main = 0
          WHERE dge.is_active = 1 AND dge.delete_date >= CURDATE()";

if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (dge.event_name LIKE '%$search_term%' OR dge.event_location LIKE '%$search_term%')";
}

if (!empty($program_type)) {
    $program_type_safe = $conn->real_escape_string($program_type);
    $query .= " AND dge.program_type = '$program_type_safe'";
}

$query .= " GROUP BY dge.id HAVING photo_count > 0";

// Sorting
switch ($sort) {
    case 'date_asc':
        $query .= " ORDER BY dge.event_date ASC";
        break;
    case 'name_asc':
        $query .= " ORDER BY dge.event_name ASC";
        break;
    case 'date_desc':
    default:
        $query .= " ORDER BY dge.event_date DESC";
        break;
}

$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get photos for this event
        $event_id = $row['id'];
        $photo_query = "SELECT * FROM download_gallery_photos 
                       WHERE event_id = ? AND is_moved_to_main = 0 
                       ORDER BY uploaded_at DESC";
        $stmt = $conn->prepare($photo_query);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $photo_result = $stmt->get_result();
        
        $photos = [];
        while ($photo = $photo_result->fetch_assoc()) {
            $photos[] = $photo;
        }
        $stmt->close();
        
        $row['photos'] = $photos;
        $events[] = $row;
    }
}

// Return as JSON for AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'events' => $events]);
    exit;
}

// Return data for PHP include
return $events;
?>

<?php
// Simple test to verify move handler works
header('Content-Type: application/json');

$root_dir = dirname(__FILE__);
echo json_encode([
    'test' => 'ok',
    'root_dir' => $root_dir,
    'file' => __FILE__,
    'test_file' => $root_dir . '/backend/move-photos-handler.php',
    'exists' => file_exists($root_dir . '/backend/move-photos-handler.php')
]);
?>

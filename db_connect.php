<?php
// Database connection details
//server
 $host = "localhost";       // Database host
 $user = "u590837060_websitedharfc";            // Database username
 $pass = "Dharfc@232111#website1";                // Database password
 $dbname = "u590837060_Mainsitedb";       // Database name (change as needed)

//local
//$host = "localhost";       // Database host
//$user = "root";            // Database username
//$pass = "";                // Database password
//$dbname = "neenaverma";       // Database name (change as needed)

//$conn = new mysqli("localhost", "u590837060_websitedharfc", "Dharfc@232111", "u590837060_Mainsitedb");
// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// Optional: set character encoding
$conn->set_charset("utf8mb4");
?>

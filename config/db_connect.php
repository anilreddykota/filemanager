<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "filemanager";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");

// Function to close the database connection when needed
function closeConnection($conn) {
    $conn->close();
}
?>

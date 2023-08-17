<?php

$host = "localhost";  // Replace with your database host
$username = "root";  // Replace with your database username
$password = "";  // Replace with your database password
$database = "screw";  // Replace with your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>

<?php
// Database connection settings
$servername = "localhost"; // Change this if your MySQL is hosted elsewhere
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password
$dbname = "otp_registration"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

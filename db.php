<?php
// Database connection details
$servername = "127.0.0.1";
$username = "root"; // Your MySQL username
$password = "";     // Your MySQL password
$dbname = "cake"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can include this file in your login or register scripts like this:
// include 'db.php';
?>

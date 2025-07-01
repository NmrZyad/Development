<?php
$servername = "localhost";
$username = "root";
$password = ""; // ⚠️ XAMPP default
$dbname = "hw2_212018535_212016406"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

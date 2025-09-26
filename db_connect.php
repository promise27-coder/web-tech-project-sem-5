<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stockbuddy_db";

// Nava XAMPP mate default connection (pachhal thi $port kadhi nakhyo)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>  
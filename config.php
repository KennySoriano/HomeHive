<?php
$servername = "127.0.0.1";
$username = "u503094516_homehive";
$password = "HomeHive2025!";
$dbname = "u503094516_homehivedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
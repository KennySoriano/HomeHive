<?php
session_start();
require_once 'config.php'; // your DB connection setup file

// Check user logged in
if (!isset($_SESSION['user_idnumber'])) {
    http_response_code(401);
    exit("User not logged in.");
}

$sender_id = $_SESSION['user_idnumber'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$sender_id || !$receiver_id || !$property_id || empty($message)) {
    http_response_code(400);
    exit("Required data is missing.");
}

// Prepare and execute insert
$stmt = $conn->prepare("INSERT INTO rentalmessages (sender_id, receiver_id, property_id, message, sent_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
if (!$stmt) {
    http_response_code(500);
    exit("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);

if ($stmt->execute()) {
    echo "Message sent successfully!";
} else {
    http_response_code(500);
    echo "Error inserting message: " . $stmt->error;
}

$stmt->close();
$conn->close();

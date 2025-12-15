<?php
session_start();
require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Read POST data (JSON or form-encoded)
$message = trim($_POST['message'] ?? '');
$property_id = (int) ($_POST['property_id'] ?? 0);
$receiver_id = (int) ($_POST['receiver_id'] ?? 0);
$sender_id = $_SESSION['user_idnumber'] ?? null;

header('Content-Type: application/json');

if (!$sender_id) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}
if (empty($message)) {
    echo json_encode(['error' => 'Message cannot be empty.']);
    exit;
}
if (!$property_id || !$receiver_id) {
    echo json_encode(['error' => 'Invalid property or receiver ID.']);
    exit;
}

// Check receiver exists
$stmt = $conn->prepare("SELECT userID FROM accountsdb WHERE userID = ?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(['error' => 'Receiver does not exist.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Insert message
$insert = $conn->prepare("INSERT INTO rentalmessages (sender_id, receiver_id, property_id, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
$insert->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);
if ($insert->execute()) {
    echo json_encode(['success' => 'Message sent successfully!']);
} else {
    echo json_encode(['error' => 'Error saving message: ' . $insert->error]);
}
$insert->close();
$conn->close();
?>

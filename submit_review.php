<?php
session_start();
require_once 'config.php'; // $conn available
date_default_timezone_set('Asia/Manila'); // PH time

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0; // tenant currently logged in
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$message = trim($_POST['message'] ?? '');
$rating = (int)($_POST['rating'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in']);
    exit;
}

// ✅ Verify user has completed rental application for this property
// ✅ NOTE: using receiver_id instead of sender_id now
$stmt = $conn->prepare("SELECT id FROM rentalapplications 
                        WHERE property_id = ? AND receiver_id = ? AND status = 'completed' LIMIT 1");
$stmt->bind_param("ii", $property_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'You are not eligible to write a review for this property.']);
    exit;
}
$stmt->close();

// ✅ Generate review_id: 6000 + 6 random digits
$review_id = 6000000000 + random_int(100000, 999999);

// ✅ Insert review
$stmt = $conn->prepare("INSERT INTO reviews (review_id, property_id, user_id, message, rating, posted_at)
                        VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iiisi", $review_id, $property_id, $user_id, $message, $rating);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Review posted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not save your review']);
}
$stmt->close();

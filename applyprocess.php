<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HomeHiveDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "You must be logged in to apply."]));
}

$user_id = $_SESSION['user_id'];
$property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($property_id === 0 || empty($message)) {
    die(json_encode(["status" => "error", "message" => "Invalid input."]));
}

// Check if user has already submitted an application
$check_sql = "SELECT id FROM RentalApplications WHERE user_id = ? AND property_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $property_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "You have already submitted an application for this property."]);
    exit;
}

// Insert new application
$sql = "INSERT INTO RentalApplications (user_id, property_id, message, status) VALUES (?, ?, ?, 'Pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $property_id, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Application submitted successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to submit application."]);
}

$stmt->close();
$conn->close();
?>

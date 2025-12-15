<?php
header('Content-Type: application/json');

$servername = "127.0.0.1:3306";
$username = "u503094516_homehive";
$password = "HomeHive2025!";
$dbname = "u503094516_homehivedb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT id FROM accountsdb WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    echo json_encode([
        "status" => "success",
        "exists" => $stmt->num_rows > 0
    ]);

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No email provided"]);
}

$conn->close();
?>

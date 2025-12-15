<?php
session_start();
require_once 'config.php'; // make sure $conn is included

// Capture user ID before ending session
$user_id = $_SESSION['user_idnumber'] ?? null;

// Manila timestamp
date_default_timezone_set('Asia/Manila');
$ph_time = date('Y-m-d H:i:s');

// ✅ Insert logout system log
if ($user_id) {
    $stmt = $conn->prepare("
        INSERT INTO system_logs (action_type, userID, description, created_at)
        VALUES (?, ?, ?, ?)
    ");
    $action_type = 'Logged Out'; // or 'Logged Out' if you prefer
    $description = "User logged out of the system.";
    $stmt->bind_param("siss", $action_type, $user_id, $description, $ph_time);
    $stmt->execute();
    $stmt->close();
}

// Destroy session
session_unset();
session_destroy();

if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

header("Location: loginuser.php"); 
exit;
?>

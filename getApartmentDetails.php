<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HomeHiveDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$apartment_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($apartment_id > 0) {
    $stmt = $conn->prepare("SELECT name, price, bedrooms, email, address, parking, floors, bathrooms FROM ApartmentListTable WHERE id = ?");
    $stmt->bind_param("i", $apartment_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name, $price, $bedrooms, $email, $address, $parking, $floors, $bathrooms);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        echo json_encode([
            'name' => $name,
            'price' => $price,
            'bedrooms' => $bedrooms,
            'email' => $email,
            'address' => $address,
            'parking' => $parking,
            'floors' => $floors,
            'bathrooms' => $bathrooms
        ]);
    } else {
        echo json_encode([]);
    }
    $stmt->close();
}

$conn->close();
?>

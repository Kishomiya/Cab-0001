<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Retrieve pickup location from query parameter
$pickup_location = $_GET['pickup_location'] ?? '';

// Fetch drivers based on pickup location
$sql = "SELECT * FROM drivers WHERE Location = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $pickup_location);
$stmt->execute();
$result = $stmt->get_result();
$drivers = $result->fetch_all(MYSQLI_ASSOC);

// Return drivers as JSON
echo json_encode($drivers);
?>

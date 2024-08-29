<?php
header('Content-Type: application/json');
require_once '../db_connect.php';

// Retrieve POST data
$input = json_decode(file_get_contents('php://input'), true);

$driver_id = $input['driver_id'];
$passenger_id = $input['passenger_id'];
$pickup_location = $input['pickup_location'];
$destination = $input['destination'];
$pickup_time = $input['pickup_time'];
$pickup_date = $input['pickup_date'];

// Insert request into the database or perform necessary actions
// This is a placeholder. Replace with actual logic to notify the driver

// Example response
echo json_encode(['status' => 'success']);
?>

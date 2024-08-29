<?php
session_start();
require_once '../db_connect.php'; // Ensure this file contains correct database connection details

// Check if passenger ID is set in session
if (!isset($_SESSION['passenger_id'])) {
    echo "Passenger ID not set in session. Please log in.";
    exit;
}

$pickup_location = $_POST['pickup_location'] ?? '';

if ($pickup_location) {
    $conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT DID, Name, Location FROM drivers WHERE Location = ?");
    $stmt->bind_param("s", $pickup_location);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="form-check">';
            echo '<input type="radio" id="driver_' . $row['DID'] . '" name="driver_id" value="' . $row['DID'] . '" class="form-check-input" required>';
            echo '<label class="form-check-label" for="driver_' . $row['DID'] . '">' . $row['Name'] . ' (Location: ' . $row['Location'] . ')</label>';
            echo '</div>';
        }
    } else {
        echo 'No drivers available for this location.';
    }

    $stmt->close();
    $conn->close();
}
?>

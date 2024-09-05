<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    $driver_id = $_SESSION['driver_id'];

    // Update the driver status to "Busy"
    $updateStatusQuery = "UPDATE drivers SET Status = 'Busy' WHERE DID = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param("i", $driver_id);
    if ($stmt->execute()) {
        // Success
        echo json_encode(['status' => 'success']);
    } else {
        // Error
        echo json_encode(['status' => 'error']);
    }
    $stmt->close();
}
$conn->close();
?>

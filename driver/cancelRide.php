<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id']) && isset($_POST['cancel_ride'])) {
    $job_id = $_POST['job_id'];

    // Update job status to "Canceled"
    $updateJobQuery = "UPDATE job_assignments SET Status = 'Canceled' WHERE JobID = ?";
    $stmt = $conn->prepare($updateJobQuery);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->close();

    // Update driver status to "Available"
    $updateDriverQuery = "UPDATE drivers SET Status = 'Available' WHERE DID = ?";
    $stmt = $conn->prepare($updateDriverQuery);
    $stmt->bind_param("i", $_SESSION['driver_id']);
    $stmt->execute();
    $stmt->close();

    header('Location: job_assignments.php');
    exit();
} else {
    echo "Invalid access.";
    exit;
}
?>

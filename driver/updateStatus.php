<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id']) && isset($_POST['status'])) {
    $job_id = $_POST['job_id'];
    $status = $_POST['status'];

    // Update the job status
    $sql = "UPDATE job_assignments SET status = ? WHERE JobID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $job_id);
    $stmt->execute();

    // If the status is accepted, also update the driver's status to 'Unavailable'
    if ($status === 'accepted') {
        $driverSql = "UPDATE drivers SET Status = 'Unavailable' WHERE DID = (SELECT DriverID FROM job_assignments WHERE JobID = ?)";
        $stmt = $conn->prepare($driverSql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
    } elseif ($status === 'canceled') {
        $driverSql = "UPDATE drivers SET Status = 'Available' WHERE DID = (SELECT DriverID FROM job_assignments WHERE JobID = ?)";
        $stmt = $conn->prepare($driverSql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
    echo 'Success';
}
?>

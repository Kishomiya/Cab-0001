<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tid = $_POST['tid'];
    $rating = $_POST['rating'];

    // Update the rating in trip_history
    $stmt = $conn->prepare("UPDATE trip_history SET Rating = ? WHERE TID = ?");
    $stmt->bind_param('si', $rating, $tid);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Rating updated successfully!'); window.location.href='trip_history.php';</script>";
}

$conn->close();
?>

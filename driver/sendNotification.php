<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'taxi_reservation');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to send a notification
function sendNotification($driverId, $message) {
    global $conn;

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO notifications (driver_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $driverId, $message);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "Notification sent successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Example usage of the function (this would be called based on your logic)
if (isset($_POST['send_notification'])) {
    $driverId = $_POST['driver_id']; // This should come from your form or session
    $message = $_POST['message']; // This should come from your form input

    sendNotification($driverId, $message);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
</head>
<body>
    <form method="post" action="sendNotification.php">
        <input type="hidden" name="driver_id" value="1"> <!-- Example driver ID -->
        <input type="text" name="message" placeholder="Enter your message">
        <button type="submit" name="send_notification">Send Notification</button>
    </form>
</body>
</html>

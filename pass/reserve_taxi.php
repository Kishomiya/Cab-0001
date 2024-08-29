<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pickup_location = $_POST['pickup_location'];
    $destination = $_POST['destination'];
    $pickup_time = $_POST['pickup_time'];
    $passenger_username = $_SESSION['username'];

    // Fetch passenger ID
    $pid_query = "SELECT PID FROM passengers WHERE Username = ?";
    $stmt = $conn->prepare($pid_query);
    $stmt->bind_param('s', $passenger_username);
    $stmt->execute();
    $stmt->bind_result($passenger_id);
    $stmt->fetch();
    $stmt->close();

    // Fetch a driver and vehicle details
    // Fetch a driver and vehicle details
$driver_query = "SELECT drivers.Name AS driver_name, drivers.Contact AS driver_contact, vehicles.RegistrationNo AS vehicle_registration FROM drivers JOIN vehicles ON drivers.DID = vehicles.DID ORDER BY RAND() LIMIT 1";
$driver_result = $conn->query($driver_query);

if ($driver_result->num_rows > 0) {
    $driver_info = $driver_result->fetch_assoc();

    // System notification instead of SMS
    $notification_message = "Reservation Confirmed! Driver: " . $driver_info['driver_name'] . ", Contact: " . $driver_info['driver_contact'] . ", Vehicle Registration: " . $driver_info['vehicle_registration'];

    // Fetch passenger's contact info from database
    $passenger_query = "SELECT contact FROM passengers WHERE username = '$passenger_username'";
    $passenger_result = $conn->query($passenger_query);
    if ($passenger_result->num_rows > 0) {
        $passenger_info = $passenger_result->fetch_assoc();
        $passenger_contact = $passenger_info['contact'];

        // Instead of SMS, display a system notification or alert
        echo "<script>alert('$notification_message');</script>";
    } else {
        echo "<script>alert('Unable to retrieve passenger contact information.');</script>";
    }
} else {
    echo "<script>alert('No drivers available at the moment. Please try again later.');</script>";
}


    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Taxi - Taxi Reservation System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Reserve a Taxi</h2>
        <form action="reserve_taxi.php" method="POST">
            <div class="form-group">
                <label for="pickup_location">Pickup Location:</label>
                <input type="text" id="pickup_location" name="pickup_location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="destination">Destination:</label>
                <input type="text" id="destination" name="destination" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="pickup_time">Pickup Time:</label>
                <input type="datetime-local" id="pickup_time" name="pickup_time" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Reserve Taxi</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
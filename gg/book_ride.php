<?php
session_start();
require_once '../db_connect.php';  

// Check if passenger ID is set in session
if (!isset($_SESSION['passenger_id'])) {
    echo "Passenger ID not set in session. Please log in.";
    exit;
}

$passenger_id = $_SESSION['passenger_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'book_ride') {
    $pickup_location = $_POST['pickup_location'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $pickup_time = $_POST['pickup_time'] ?? '';
    $pickup_date = $_POST['pickup_date'] ?? '';
    $driver_id = $_POST['driver_id'] ?? '';

    if (!empty($pickup_location) && !empty($destination) && !empty($pickup_time) && !empty($pickup_date) && !empty($driver_id)) {
        // Fetch passenger details
        $passengerQuery = "SELECT Name, Contact FROM passengers WHERE PID = ?";
        $stmt = $conn->prepare($passengerQuery);
        $stmt->bind_param("i", $passenger_id);
        $stmt->execute();
        $passengerResult = $stmt->get_result();
        $passenger = $passengerResult->fetch_assoc();
        $stmt->close();

        // Insert into job_assignments table
        $insertQuery = "INSERT INTO job_assignments (DriverID, PassengerID, PickupLocation, Destination, PickupTime, PickupDate, PassengerName, PassengerPhone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iissssss", $driver_id, $passenger_id, $pickup_location, $destination, $pickup_time, $pickup_date, $passenger['Name'], $passenger['Contact']);
        $stmt->execute();
        $stmt->close();

        // Send response back to the client
        echo json_encode(['status' => 'success', 'message' => 'Request sent to the driver']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all fields and select a driver']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ride</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .success-popup {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Book a Ride</h2>
        <form id="booking-form" method="POST">
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
                <input type="time" id="pickup_time" name="pickup_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pickup_date">Pickup Date:</label>
                <input type="date" id="pickup_date" name="pickup_date" class="form-control" required>
            </div>

            <button type="button" class="btn btn-primary" id="show-drivers-btn">Show Available Drivers</button>

            <div id="driver-list" class="mt-3" style="display: none;">
                <h4>Select Driver</h4>
                <div id="drivers-container"></div>
                <button type="button" class="btn btn-success mt-3" id="book-ride-btn">Book Ride</button>
            </div>
        </form>
    </div>

    <div class="success-popup" id="success-popup">Request sent to the driver</div>

    <script>
        // Fetch available drivers
        document.getElementById('show-drivers-btn').addEventListener('click', function() {
            var pickupLocation = document.getElementById('pickup_location').value;

            if (pickupLocation) {
                $.ajax({
                    url: 'fetch_drivers.php',
                    type: 'POST',
                    data: { pickup_location: pickupLocation },
                    success: function(data) {
                        $('#drivers-container').html(data);
                        $('#driver-list').show();
                    },
                    error: function() {
                        alert('Failed to fetch drivers.');
                    }
                });
            } else {
                alert('Please enter the pickup location.');
            }
        });

        // Handle the Book Ride button click
        document.getElementById('book-ride-btn').addEventListener('click', function() {
            var pickupLocation = document.getElementById('pickup_location').value;
            var destination = document.getElementById('destination').value;
            var pickupTime = document.getElementById('pickup_time').value;
            var pickupDate = document.getElementById('pickup_date').value;
            var driverId = $('input[name="driver_id"]:checked').val(); // Get selected driver ID

            if (pickupLocation && destination && pickupTime && pickupDate && driverId) {
                $.ajax({
                    url: 'book_ride.php',
                    type: 'POST',
                    data: {
                        action: 'book_ride',
                        pickup_location: pickupLocation,
                        destination: destination,
                        pickup_time: pickupTime,
                        pickup_date: pickupDate,
                        driver_id: driverId
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#success-popup').fadeIn().delay(2000).fadeOut();
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function() {
                        alert('Failed to send request to the driver.');
                    }
                });
            } else {
                alert('Please fill all fields and select a driver.');
            }
        });
    </script>
</body>
</html>

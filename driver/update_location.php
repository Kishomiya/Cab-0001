<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = $_POST['location'];
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if (empty($location) && (is_null($latitude) || is_null($longitude))) {
        echo '<script>alert("Please provide a location.");</script>';
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the driver's username from session
        $username = $_SESSION['username'];

        // Fetch driver's DID
        $sql = "SELECT DID FROM drivers WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $DID = $row['DID'];

            // Update driver's location
            $sql = "UPDATE drivers SET Location = ?, Latitude = ?, Longitude = ? WHERE DID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sddi', $location, $latitude, $longitude, $DID);

            if ($stmt->execute()) {
                echo '<script>alert("Location updated successfully.");</script>';
            } else {
                echo '<script>alert("Error updating location: ' . $stmt->error . '");</script>';
            }

            $stmt->close();
        } else {
            echo '<script>alert("Driver not found.");</script>';
        }

        $conn->close();
    }
}

// Fetch current location for form pre-fill
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the driver's username from session
$username = $_SESSION['username'];

// Fetch driver's current location
$sql = "SELECT Location, Latitude, Longitude FROM drivers WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentLocation = $row['Location'];
    $currentLatitude = $row['Latitude'];
    $currentLongitude = $row['Longitude'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Location - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function updateLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('location').value = 'Current GPS Location';

                    alert('Location updated from GPS.');
                }, function(error) {
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            alert('User denied the request for Geolocation.');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert('Location information is unavailable.');
                            break;
                        case error.TIMEOUT:
                            alert('The request to get user location timed out.');
                            break;
                        case error.UNKNOWN_ERROR:
                            alert('An unknown error occurred.');
                            break;
                    }
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
    </script>

</head>

<body>

    <div class="container mt-5">
        <h2>Update Location</h2>
        <form id="locationForm" action="update_location.php" method="POST">
            <input type="text" id="location" class="form-control mb-3" name="location" placeholder="Enter your location" required>
            <input type="hidden" class="form-control" id="latitude" name="latitude">
            <input type="hidden" class="form-control" id="longitude" name="longitude">
            <button type="button" class="btn btn-secondary" onclick="updateLocation()">Use Current Location</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

</body>

</html>
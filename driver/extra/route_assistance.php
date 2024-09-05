<?php
session_start();

// Check if driver ID is set in the session
if (!isset($_SESSION['driver_id'])) {
    echo '<script>alert("Driver ID not found in session. Please log in again."); window.location.href = "../login.php";</script>';
    exit();
}

$driver_id = $_SESSION['driver_id']; // Driver ID should be stored in session after login

// Database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch route assistance information for the logged-in driver
$sql = "SELECT Location FROM drivers WHERE DID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $did);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $location = $row['Location'];
} else {
    $location = null;
}

$stmt->close();
$conn->close();

if ($location) {
    list($latitude, $longitude) = explode(', ', $location);
} else {
    $latitude = 51.505;
    $longitude = -0.09;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Assistance</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Route Assistance</h2>
        <p>View and manage your route assistance information here.</p>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map);
        map.setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 13);

        // Optionally add route or directions functionality here if needed
    </script>
</body>

</html>

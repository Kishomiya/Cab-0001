<?php
session_start();
require_once '../db_connect.php';

// Check if job_id is set in the URL
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    echo "Invalid access.";
    exit;
}

$job_id = $_GET['job_id'];

// Fetch ride and driver details
$sql = "SELECT ja.PickupLocation, ja.Destination, d.Location AS DriverLocation 
        FROM job_assignments ja
        JOIN drivers d ON ja.DriverID = d.DID
        WHERE ja.JobID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a matching job was found
if ($result->num_rows === 0) {
    echo "Invalid job ID.";
    exit;
}

$ride = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Route</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <script>
        function initMap() {
            var driverLocation = "<?php echo $ride['DriverLocation']; ?>";
            var pickupLocation = "<?php echo $ride['PickupLocation']; ?>";
            var destination = "<?php echo $ride['Destination']; ?>";

            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer();
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: {lat: -34.397, lng: 150.644} // default center, will be adjusted
            });
            directionsRenderer.setMap(map);

            var request = {
                origin: driverLocation,
                destination: pickupLocation,
                travelMode: 'DRIVING'
            };

            directionsService.route(request, function(result, status) {
                if (status == 'OK') {
                    directionsRenderer.setDirections(result);
                }
            });
        }
    </script>
</head>
<body onload="initMap()">
    <div id="map" style="height: 500px; width: 100%;"></div>
</body>
</html>

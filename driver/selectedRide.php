<?php
session_start();
require_once '../db_connect.php';


if (!isset($_SESSION['ride_details'])) {
    echo "Ride details not found. Please start a ride first.";
    exit;
}


$ride = $_SESSION['ride_details'];
$pickup_location = $ride['pickup_location'];
$destination = $ride['destination'];
$pickup_time = $ride['pickup_time'];
$pickup_date = $ride['pickup_date'];
$driver_id = $ride['driver_id'];


if (!isset($pdo)) {
    echo "Database connection not established.";
    exit;
}


$driver_query = $pdo->prepare("SELECT Latitude, Longitude FROM drivers WHERE DID = ?");
$driver_query->execute([$driver_id]);
$driver = $driver_query->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    echo "Driver not found.";
    exit;
}

$driverLat = $driver['Latitude'];
$driverLng = $driver['Longitude'];

$distance = '10 km';
$duration = '20 minutes';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        #output {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Ride Details</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>Ride Information</h4>
                <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($pickup_location); ?></p>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($destination); ?></p>
                <p><strong>Pickup Time:</strong> <?php echo htmlspecialchars($pickup_time); ?></p>
                <p><strong>Pickup Date:</strong> <?php echo htmlspecialchars($pickup_date); ?></p>
                
                
                <button id="journeyStartBtn" class="btn btn-primary">Journey Start</button>
                <button id="nextBtn" class="btn btn-secondary">Next</button>
            </div>
            <div class="col-md-6">
                <h4>Route Map</h4>
                <div id="map"></div>
                <div id="output"></div>
            </div>
        </div>
    </div>

    <script>
        function initMap() {
            var mapOptions = {
                zoom: 14,
                center: { lat: <?php echo json_encode($driverLat); ?>, lng: <?php echo json_encode($driverLng); ?> }
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);

            var request = {
                origin: { lat: <?php echo json_encode($driverLat); ?>, lng: <?php echo json_encode($driverLng); ?> },
                destination: '<?php echo htmlspecialchars($pickup_location); ?>',
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, function(result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                    var distance = result.routes[0].legs[0].distance.text;
                    var duration = result.routes[0].legs[0].duration.text;
                    document.getElementById('output').innerHTML =
                        `<div class='result-table'> 
                            Driving distance: ${distance}.<br />
                            Duration: ${duration}.
                        </div>`;
                } else {
                    alert("Couldn't calculate route.");
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });

        document.getElementById('journeyStartBtn').addEventListener('click', function() {
            alert('Journey Start button clicked. Implement notification logic here.');
            
        });

        document.getElementById('nextBtn').addEventListener('click', function() {
            window.location.href = 'show_route.php';
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>




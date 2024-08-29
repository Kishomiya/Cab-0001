<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Validate the input to ensure it's in the correct format
    if (!preg_match('/^-?\d+\.\d+,-?\d+\.\d+$/', $start) || !preg_match('/^-?\d+\.\d+,-?\d+\.\d+$/', $end)) {
        $error = "Please enter valid coordinates in the format 'latitude,longitude'.";
    } else {
        // URL for the OSRM API to get the route, distance, and duration
        $url = "http://router.project-osrm.org/route/v1/driving/$start;$end?overview=full&geometries=geojson";

        // Use file_get_contents to fetch the data
        $response = @file_get_contents($url);

        if ($response === FALSE) {
            $error = "Failed to retrieve data from the routing service. Please check your inputs or try again later.";
        } else {
            // Decode the JSON response
            $data = json_decode($response, true);

            if (isset($data['routes'][0])) {
                // Extract the distance, duration, and route geometry
                $distance = $data['routes'][0]['distance'] / 1000; // Convert meters to kilometers
                $duration = $data['routes'][0]['duration'] / 60; // Convert seconds to minutes
                $geometry = $data['routes'][0]['geometry'];

                // Pass data to JavaScript
                $routeData = json_encode($geometry);
            } else {
                $error = "No route found. Please check your input coordinates.";
            }
        }
    }
} else {
    $distance = null;
    $duration = null;
    $routeData = null;
    $error = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Planner</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Route Planner</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="start">Start Point</label>
                <input type="text" class="form-control" id="start" name="start" placeholder="Enter start point (latitude,longitude)" required>
            </div>
            <div class="form-group">
                <label for="end">End Point</label>
                <input type="text" class="form-control" id="end" name="end" placeholder="Enter end point (latitude,longitude)" required>
            </div>
            <button type="submit" class="btn btn-primary">Show Route</button>
        </form>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-4">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if ($distance && $duration): ?>
        <div class="mt-4">
            <h4>Route Details</h4>
            <p><strong>Distance:</strong> <?php echo round($distance, 2); ?> km</p>
            <p><strong>Duration:</strong> <?php echo round($duration, 2); ?> minutes</p>
        </div>
        <?php endif; ?>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([51.505, -0.09], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        <?php if ($routeData): ?>
        var route = <?php echo $routeData; ?>;
        var routeCoordinates = L.polyline(route.coordinates, { color: 'blue' }).addTo(map);
        map.fitBounds(routeCoordinates.getBounds());
        <?php endif; ?>
    </script>
</body>
</html>

<?php
session_start();
require_once '../db_connect.php'; // Ensure this file contains correct database connection details

// Check if booking details are available in session
if (!isset($_SESSION['booking_details'])) {
    echo "Booking details not found. Please book a ride first.";
    exit;
}

$booking = $_SESSION['booking_details'];
$pickup_location = $booking['pickup_location'];
$destination = $booking['destination'];
$pickup_time = $booking['pickup_time'];
$pickup_date = $booking['pickup_date'];
$driver_id = $booking['driver_id'];

// Example distance and duration calculation (replace with actual API call or logic)
$distance = '10 km';
$duration = '20 minutes';
$additional_charges = '0'; // Replace with actual charges calculation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fare Estimation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Fare Estimation</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h4>Ride Details</h4>
                    <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($pickup_location); ?></p>
                    <p><strong>Destination:</strong> <?php echo htmlspecialchars($destination); ?></p>
                    <p><strong>Distance:</strong> <?php echo $distance; ?></p>
                    <p><strong>Duration:</strong> <?php echo $duration; ?></p>
                    <p><strong>Additional Charges:</strong> <?php echo $additional_charges; ?></p>
                    
                    <!-- Button to open payment modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal">Make Payment</button>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Route Map</h4>
                <div id="google-map" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="payment-form" action="process_payment.php" method="POST">
                        <div class="form-group">
                            <label for="card_number">Card Number:</label>
                            <input type="text" id="card_number" name="card_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date:</label>
                            <input type="text" id="expiry_date" name="expiry_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Pay</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var pickupLocation = "<?php echo htmlspecialchars($pickup_location); ?>";
        var destination = "<?php echo htmlspecialchars($destination); ?>";

        function initMap() {
            var mapOptions = {
                zoom: 7,
                center: { lat: 7.8731, lng: 80.7718 }, // Default to Sri Lanka
            };

            var map = new google.maps.Map(document.getElementById('google-map'), mapOptions);
            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer();

            directionsRenderer.setMap(map);

            var request = {
                origin: pickupLocation,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, function(result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                } else {
                    alert("Couldn't calculate route.");
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCg87_OKWTFIRQK-QgT6192iAyV1KYeRzA&libraries=places"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
   
</body>
</html>

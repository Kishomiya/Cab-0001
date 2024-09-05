<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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
    $did = $row['DID'];

    // Fetch driver's earnings and completed trips
    $sql = "
        SELECT 
            t.TripID, 
            t.PickupLocation, 
            t.DropoffLocation, 
            t.Fare, 
            t.Date, 
            s.SettlementAmount, 
            s.SettlementDate 
        FROM trips t
        LEFT JOIN settlements s ON t.TripID = s.TripID
        WHERE t.DriverID = ?
        ORDER BY t.Date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $did);
    $stmt->execute();
    $tripsResult = $stmt->get_result();
} else {
    echo '<script>alert("Driver not found.");</script>';
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings and Settlements - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Earnings and Settlements</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Pickup Location</th>
                    <th>Dropoff Location</th>
                    <th>Fare</th>
                    <th>Date</th>
                    <th>Settlement Amount</th>
                    <th>Settlement Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($tripsResult->num_rows > 0) {
                    while ($trip = $tripsResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($trip['TripID']) . '</td>';
                        echo '<td>' . htmlspecialchars($trip['PickupLocation']) . '</td>';
                        echo '<td>' . htmlspecialchars($trip['DropoffLocation']) . '</td>';
                        echo '<td>$' . htmlspecialchars(number_format($trip['Fare'], 2)) . '</td>';
                        echo '<td>' . htmlspecialchars(date('Y-m-d', strtotime($trip['Date']))) . '</td>';
                        echo '<td>$' . htmlspecialchars(number_format($trip['SettlementAmount'], 2)) . '</td>';
                        echo '<td>' . htmlspecialchars(date('Y-m-d', strtotime($trip['SettlementDate']))) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7">No earnings or settlements found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>

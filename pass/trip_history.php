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

// Retrieve passenger ID
$passenger_username = $_SESSION['username'];
$pid_query = "SELECT PID FROM passengers WHERE Username = ?";
$stmt = $conn->prepare($pid_query);
$stmt->bind_param('s', $passenger_username);
$stmt->execute();
$stmt->bind_result($passenger_id);
$stmt->fetch();
$stmt->close();

// Retrieve trip history for the logged-in passenger
$history_query = "SELECT trip_history.TID, taxi_reservations.StartPlace, taxi_reservations.EndPlace, trip_history.Fare, trip_history.Rating, trip_history.TripDate
FROM trip_history
INNER JOIN taxi_reservations ON trip_history.TID = taxi_reservations.TID
WHERE trip_history.PID = ?
ORDER BY trip_history.TripDate DESC";
$stmt = $conn->prepare($history_query);
$stmt->bind_param('i', $passenger_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip History</title>
</head>
<body>
    <h1>Your Trip History</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Trip ID</th>
                <th>Start Place</th>
                <th>End Place</th>
                <th>Fare</th>
                <th>Rating</th>
                <th>Trip Date</th>
                <th>Rate Driver</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['TID']; ?></td>
                    <td><?php echo $row['StartPlace']; ?></td>
                    <td><?php echo $row['EndPlace']; ?></td>
                    <td><?php echo $row['Fare']; ?></td>
                    <td><?php echo $row['Rating']; ?></td>
                    <td><?php echo $row['TripDate']; ?></td>
                    <td>
                        <form action="rate_driver.php" method="POST">
                            <input type="hidden" name="tid" value="<?php echo $row['TID']; ?>">
                            <select name="rating">
                                <option value="Excellent">Excellent</option>
                                <option value="Good">Good</option>
                                <option value="Average">Average</option>
                                <option value="Bad">Bad</option>
                            </select>
                            <button type="submit">Rate</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

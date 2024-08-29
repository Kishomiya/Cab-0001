<?php
session_start();
require_once '../db_connect.php'; // Ensure this file contains correct database connection details

// Check if driver is logged in
if (!isset($_SESSION['driver_id'])) {
    echo "Driver ID not set in session. Please log in.";
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Fetch job assignments for this driver
$sql = "SELECT ja.JobID, p.Name AS PassengerName, p.Contact AS PassengerContact, 
        ja.PickupLocation, ja.Destination, ja.PickupTime, ja.PickupDate
        FROM job_assignments ja
        JOIN passengers p ON ja.PassengerID = p.PID
        WHERE ja.DriverID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Assignments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Job Assignments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Passenger Name</th>
                    <th>Passenger Contact</th>
                    <th>Pickup Location</th>
                    <th>Destination</th>
                    <th>Pickup Time</th>
                    <th>Pickup Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['JobID']; ?></td>
                    <td><?php echo $row['PassengerName']; ?></td>
                    <td><?php echo $row['PassengerContact']; ?></td>
                    <td><?php echo $row['PickupLocation']; ?></td>
                    <td><?php echo $row['Destination']; ?></td>
                    <td><?php echo $row['PickupTime']; ?></td>
                    <td><?php echo $row['PickupDate']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

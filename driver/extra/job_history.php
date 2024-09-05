<?php
session_start();
require_once '../db_connect.php'; // Ensure this file contains the correct database connection details

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'driver') {
    header('Location: ../login.php');
    exit();
}

$driver_id = $_SESSION['driver_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch job history
$sql = "SELECT jh.JID, ja.RequestDate, p.Name AS PassengerName, jh.Status
        FROM job_history jh
        JOIN job_assignments ja ON jh.JID = ja.JID
        JOIN passengers p ON ja.PID = p.PID
        WHERE jh.DID = ?";
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
    <title>Job History - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    

    <div class="container mt-5">
        <h2>Job History</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Passenger Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['JID']); ?></td>
                            <td><?php echo htmlspecialchars($row['PassengerName']); ?></td>
                            <td><?php echo htmlspecialchars($row['RequestDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['Status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No job history available.</p>
        <?php endif; ?>
    </div>

    
</body>
</html>

<?php
$conn->close();
?>

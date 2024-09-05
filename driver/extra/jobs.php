<?php
session_start();
require_once '../db_connect.php'; // Ensure this file contains the correct database connection details

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'driver') {
    header('Location: ../login.php');
    exit();
}

$driver_id = $_SESSION['driver_id'];
$job_id = $_POST['jid'] ?? '';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle job acceptance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($job_id)) {
    // Update job status
    $sql = "UPDATE job_assignments SET DID=?, Status='Assigned' WHERE JID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $driver_id, $job_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Insert into job history
        $sql = "INSERT INTO job_history (JID, DID, Status) VALUES (?, ?, 'Assigned')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $job_id, $driver_id);
        $stmt->execute();

        echo '<script>alert("Job accepted successfully."); window.location.href = "jobs.php";</script>';
    } else {
        echo '<script>alert("Failed to accept job."); window.location.href = "jobs.php";</script>';
    }

    $stmt->close();
}

// Fetch available jobs
$sql = "SELECT ja.JID, p.Name AS PassengerName, ja.RequestDate 
        FROM job_assignments ja 
        JOIN passengers p ON ja.PID = p.PID 
        WHERE ja.DID IS NULL AND ja.Status = 'Available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    

    <div class="container mt-5">
        <h2>Available Jobs</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Passenger Name</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['JID']); ?></td>
                            <td><?php echo htmlspecialchars($row['PassengerName']); ?></td>
                            <td><?php echo htmlspecialchars($row['RequestDate']); ?></td>
                            <td>
                                <form action="jobs.php" method="POST">
                                    <input type="hidden" name="jid" value="<?php echo htmlspecialchars($row['JID']); ?>">
                                    <button type="submit" class="btn btn-primary">Accept Job</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No available jobs at the moment.</p>
        <?php endif; ?>
    </div>

    
</body>
</html>

<?php
$conn->close();
?>

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

$currentStatus = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    if ($status !== 'Available' && $status !== 'Busy') {
        echo '<script>alert("Invalid status.");</script>';
    } else {
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
            $DID = $row['DID'];

            // Update driver's status
            $sql = "UPDATE drivers SET Status = ? WHERE DID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $status, $DID);

            if ($stmt->execute()) {
                echo '<script>alert("Status updated successfully.");</script>';
            } else {
                echo '<script>alert("Error updating status: ' . $stmt->error . '");</script>';
            }

            $stmt->close();
        } else {
            echo '<script>alert("Driver not found.");</script>';
        }

        $conn->close();
    }
}

// Fetch current status for form pre-fill
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the driver's username from session
$username = $_SESSION['username'];

// Fetch driver's current status
$sql = "SELECT Status FROM drivers WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentStatus = $row['Status'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h2>Update Availability Status</h2>
        <form action="update_status.php" method="POST">
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="" disabled>Select Status</option>
                    <option value="Available" <?php echo ($currentStatus === 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Busy" <?php echo ($currentStatus === 'Busy') ? 'selected' : ''; ?>>Busy</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>

</body>
</html>

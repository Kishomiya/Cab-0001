<?php
session_start();

// Ensure the session has the correct username
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debugging: Check if session username is correct
echo "<script>console.log('Username in session: " . $username . "');</script>";

// Fetch current profile data
$sql = "SELECT * FROM drivers WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

// Debugging: Check if the query returned data
if (!$driver) {
    echo "<script>alert('Error: No data found for the user.');</script>";
    echo "<script>console.log('No data found for username: " . $username . "');</script>";
} else {
    echo "<script>console.log('Driver data found: " . json_encode($driver) . "');</script>";

    // Assign fetched data to variables
    $name = $driver['name'] ?? '';
    $address = $driver['address'] ?? '';
    $contact = $driver['contact'] ?? '';
    $email = $driver['email'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Update profile
    $sql = "UPDATE drivers SET name=?, address=?, contact=?, email=? WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $address, $contact, $email, $username);
    if ($stmt->execute()) {
        echo '<script>alert("Profile updated successfully.");</script>';
    } else {
        echo '<script>alert("Error updating profile.");</script>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Update Profile</h2>
        <?php if ($driver): ?>
            <form action="update_profile.php" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($driver['Name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($driver['Address'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($driver['Contact'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($driver['Email'] ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        <?php else: ?>
            <p>No profile data found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
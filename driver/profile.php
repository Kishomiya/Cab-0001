<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php'); 
    exit();
}

$username = $_SESSION['username']; 

// Database connection
$conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';

    // Prepare the SQL statement
    $sql = "UPDATE drivers SET Name=?, Address=?, Contact=?, Email=? WHERE Name=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute
    $stmt->bind_param("sssss", $name, $address, $contact, $email, $username);
    if ($stmt->execute()) {
        echo '<script>alert("Profile updated successfully.");</script>';
    } else {
        echo '<script>alert("Profile update failed: ' . $stmt->error . '");</script>';
    }

    $stmt->close();
}

// Handle password change via AJAX
if (isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Verify the current password
    $sql = "SELECT Password FROM drivers WHERE Name=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($result->num_rows > 0 && password_verify($current_password, $row['Password'])) {
        // Update with new password
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE drivers SET Password=? WHERE Name=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $hashed_new_password, $username);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password change failed: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        // Password does not match
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
    }

    $conn->close();
    exit(); // Stop further execution after handling AJAX request
}

// Fetch driver data for displaying in the form
$sql = "SELECT * FROM drivers WHERE Name=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#changePasswordModal').on('shown.bs.modal', function () {
                $('#current_password').focus();
            });

            $('#changePasswordForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'profile.php',
                    method: 'POST',
                    data: $(this).serialize() + '&action=change_password',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#changePasswordModal').modal('hide'); // Close the modal on success
                        } else {
                            alert(response.message); // Show the error message
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown);
                        alert("An error occurred while processing your request.");
                    }
                });
            });
        });
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Profile</h2>

        <!-- Profile Update Form -->
        <form action="profile.php" method="POST">
            <h3>Update Profile</h3>
            <?php if ($driver): ?>
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
            <?php else: ?>
                <p>No profile data found.</p>
            <?php endif; ?>
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
        </form>

        <hr>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#changePasswordModal">
            Change Password
        </button>

        <!-- Modal -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="changePasswordForm">
                            <div class="form-group">
                                <label for="current_password">Current Password:</label>
                                <input type="password" id="current_password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include '../includes/footer.php'; ?>  
</body>
</html>

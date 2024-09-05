<?php
session_start();
include('../db_connect.php');


// Check if the passenger is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// Get passenger details
$username = $_SESSION['username'];
$query = "SELECT * FROM passengers WHERE Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$passenger = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile
        $name = $_POST['name'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];

        $updateQuery = "UPDATE passengers SET Name = ?, Address = ?, Contact = ?, Email = ? WHERE Username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssss', $name, $address, $contact, $email, $username);
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Failed to update profile.";
        }
    }

    if (isset($_POST['change_password'])) {
        // Change password
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $passenger['Password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE passengers SET Password = ? WHERE Username = ?";
                $stmt = $conn->prepare($updatePasswordQuery);
                $stmt->bind_param('ss', $hashed_password, $username);
                if ($stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Failed to change password.";
                }
            } else {
                $error_message = "New password and confirm password do not match.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="font-Nunito">
    <div class="justify-content-center align-item-center p-5 bg-light">
        <div class="d-flex  bg-white justify-content-center align-item-center">
            <div class="p-4 bg-white " style="width: 400px;">
                <h2 class="text-center font-weight-bolder text-primary">Manage Profile</h2>
                <?php if (isset($success_message)) {
                    echo "<p style='color:green;'>$success_message</p>";
                } ?>
                <?php if (isset($error_message)) {
                    echo "<p style='color:red;'>$error_message</p>";
                } ?>
                <hr>

                <form method="post" action="profile.php" class="modal-body ">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($passenger['Name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" class="form-control" required><?php echo htmlspecialchars($passenger['Address']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($passenger['Contact']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($passenger['Email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary justify-content mt-3">Update Profile</button>
                </form>
            </div>
            <div class="border-left mx-2"></div>
            <div class=" p-4 bg-white" style="width: 400px;">
                <h2 class="text-center font-weight-bolder text-primary">Change Password</h2>
                <hr>

                <form method="post" action="profile.php" class=" modal-body">
                    <label for="current_password">Current Password:</label><br>
                    <input type="password" id="current_password" name="current_password" class="form-control" required><br>

                    <label for="new_password">New Password:</label><br>
                    <input type="password" id="new_password" name="new_password" class="form-control" required><br>

                    <label for="confirm_password">Confirm New Password:</label><br>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required><br>

                    <button type="submit" name="change_password" class="btn btn-primary justify-content-end mt-3">Change Password</button>
                </form>
            </div>
        </div>
    </div>


    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</body>

</html>
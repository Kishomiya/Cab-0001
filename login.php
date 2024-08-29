<?php
session_start();
require_once 'db_connect.php'; // Ensure this file contains correct database connection details

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo '<script>alert("Username and Password are required.");</script>';
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the user is an admin
        $sql = "SELECT * FROM admins WHERE Username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['Password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin'; // Set user role
                header('Location: admin_dashboard.php');
                exit();
            } else {
                echo '<script>alert("Invalid password.");</script>';
            }
        } else {
            // Check if the user is a passenger
            $sql = "SELECT * FROM passengers WHERE Username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['Password'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'passenger'; // Set user role
                    $_SESSION['passenger_id'] = $row['PID']; // Set passenger ID in session
                    header('Location: pass/passenger_dashboard.php');
                    exit();
                } else {
                    echo '<script>alert("Invalid password.");</script>';
                }
            } else {
                // Check if the user is a driver
                $sql = "SELECT * FROM drivers WHERE Username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    if (password_verify($password, $row['Password'])) {
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = 'driver'; // Set user role
                        $_SESSION['driver_id'] = $row['DID']; // Set driver ID in session
                        header('Location: ./driver/dashboard.php');
                        exit();
                    } else {
                        echo '<script>alert("Invalid password.");</script>';
                    }
                } else {
                    echo '<script>alert("No user found.");</script>';
                }
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Taxi Reservation System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

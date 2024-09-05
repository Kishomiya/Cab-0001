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
                header('Location: admin_dashboard.html');
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
    <link rel="stylesheet" href="assets/css/style.css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body class="login-Body align-items-center justify-content-center d-flex p-0 m-0 ">


    <div class="login-main bg-white shadow-lg p-4">
        <h2 class="text-center fs-3 text-primary font-weight-bolder">Login</h2>
        <form action="login.php" method="POST" class="p-4">
            <div class="form-group">
                <!-- <span class="input-group-text"><i class="fas fa-user"></i></span> -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <hr class="">

            <button type="submit" class="btn btn-primary log-button ">Login</button>
        </form>
    </div>


</body>

</html>
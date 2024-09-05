<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PID = uniqid('P'); // Generate a unique PID
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'taxi_reservation');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email or username already exists
    $checkQuery = $conn->prepare("SELECT PID FROM passengers WHERE email = ? OR username = ?");
    $checkQuery->bind_param('ss', $email, $username);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows > 0) {
        echo "Error: Email or username already exists. Please use different values.";
        $checkQuery->close();
    } else {
        $checkQuery->close();

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO passengers (PID, name, address, contact, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $PID, $name, $address, $contact, $email, $username, $password);

        if ($stmt->execute()) {
            // Send email with username and password
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'dastanviswa00@gmail.com'; // SMTP username
                $mail->Password = 'jgiz ouqh wbxn ziby'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('your_email@example.com', 'Taxi Reservation System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Registration Successful';
                $mail->Body    = "Dear $name,<br><br>Your registration is successful.<br><br>Username: $username<br>Password: {Your chosen password}<br><br>Please keep this information safe.<br><br>Best Regards,<br>Taxi Reservation System";

                $mail->send();
                echo '<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function(){
                    $("#successModal").modal("show");
                });
              </script>';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Taxi Reservation System</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<body class="login-Body align-items-center justify-content-center d-flex p-0 m-0 ">

    <div class="register-main bg-white shadow-lg p-4">
        <h2 class="font-weight-bolder text-center text-primary mb-3">Register</h2>
        <form action="register.php" method="POST" class="modal-body ">
            <div class="row">
                <div class="form-group col">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group col">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
            </div>
            <hr>

            <button type="submit" class="btn btn-primary re-button">Register</button>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    You have successfully registered! Please check your email for your username and password.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='login.php';">OK</button>
                </div>
            </div>
        </div>
    </div>

</body>


</html>
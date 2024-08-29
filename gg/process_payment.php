<?php
// Payment processing logic (Mock example)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // Mock successful payment
    $payment_successful = true;

    if ($payment_successful) {
        // Clear booking details from session
        unset($_SESSION['booking_details']);
        
        // Notify the driver (This is a placeholder for actual notification logic)
        echo "<script>alert('Payment successful! Notification sent to the driver.');</script>";
        echo "<script>window.location.href = 'thank_you.php';</script>";
    } else {
        echo "<script>alert('Payment failed! Please try again.');</script>";
        echo "<script>window.location.href = 'fare_estimation.php';</script>";
    }
}
?>

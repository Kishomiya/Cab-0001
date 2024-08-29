<?php
require_once '../db_connect.php'; // Include your database connection file

if (isset($_POST['vid'])) {
    $vid = $_POST['vid'];

    $sql = "SELECT InsuranceInfo, RegistrationInfo FROM vehicle_documentation WHERE VID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vid);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    echo json_encode($data);
}
?>

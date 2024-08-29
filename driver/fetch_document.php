<?php
require_once '../db_connect.php';

$response = ['success' => false, 'data' => []];

if (isset($_POST['vid'])) {
    $vid = $_POST['vid'];
    $sql = "SELECT * FROM vehicle_documents WHERE VID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $vid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_assoc();
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();

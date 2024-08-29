<?php
session_start();
require_once '../db_connect.php'; // Include your database connection file

// Check if the driver_id is set in the session
if (!isset($_SESSION['driver_id'])) {
    // Redirect to login page or show an error message
    header('Location: ../login.php');
    exit();
}

$driver_id = $_SESSION['driver_id'];
// Handle Add Vehicle
if (isset($_POST['add_vehicle'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $color = $_POST['color'];
    $registration_no = $_POST['registration_no'];

    $sql = "INSERT INTO vehicles (DID, Brand, Model, Color, RegistrationNo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $driver_id, $brand, $model, $color, $registration_no);
    $stmt->execute();
    $stmt->close();
}

// Handle Edit Vehicle
if (isset($_POST['edit_vehicle'])) {
    $vid = $_POST['vid'];
    $brand = $_POST['edit_brand'];
    $model = $_POST['edit_model'];
    $color = $_POST['edit_color'];
    $registration_no = $_POST['edit_registration_no'];

    $sql = "UPDATE vehicles SET Brand=?, Model=?, Color=?, RegistrationNo=? WHERE VID=? AND DID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $brand, $model, $color, $registration_no, $vid, $driver_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Remove Vehicle
if (isset($_POST['remove_vehicle'])) {
    $vid = $_POST['vid'];

    $sql = "DELETE FROM vehicles WHERE VID=? AND DID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $vid, $driver_id);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for adding/editing vehicle documentation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_document'])) {
    $vid = $_POST['vid'] ?? '';
    $insurance = $_POST['insurance'] ?? '';
    $registration = $_POST['registration'] ?? '';

    // Debugging: Print received data
    error_log("Received data - VID: $vid, Insurance: $insurance, Registration: $registration");

    if (!empty($vid) && !empty($insurance) && !empty($registration)) {
        // Prepare and execute the SQL query
        $sql = "REPLACE INTO vehicle_documents (VID, Insurance, Registration) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $vid, $insurance, $registration);

        if ($stmt->execute()) {
            echo '<script>alert("Vehicle documentation saved successfully.");</script>';
        } else {
            echo '<script>alert("Failed to save vehicle documentation.");</script>';
        }

        $stmt->close();
    } else {
        error_log("Validation failed - All fields are required.");
        echo '<script>alert("All fields are required.");</script>';
    }
}

// Fetch vehicles for display
$sql = "SELECT VID, Brand, Model, Color, RegistrationNo FROM vehicles WHERE DID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$vehicles = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Vehicle Management</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addVehicleModal">Add Vehicle</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>VID</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Registration No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vehicle['VID']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['Brand']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['Model']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['Color']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['RegistrationNo']); ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentModal" onclick="loadDocumentDetails('<?php echo $row['VID']; ?>')">Document</button>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editVehicleModal" onclick="editVehicle('<?php echo $row['VID']; ?>')">Edit</button>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#removeVehicleModal" data-id="<?php echo $vehicle['VID']; ?>">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVehicleModalLabel">Add Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="vehicle_management.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" required>
                        </div>
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" class="form-control" id="color" name="color" required>
                        </div>
                        <div class="form-group">
                            <label for="registration_no">Registration No</label>
                            <input type="text" class="form-control" id="registration_no" name="registration_no" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_vehicle">Add Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1" role="dialog" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleModalLabel">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="vehicle_management.php">
                    <input type="hidden" id="edit_vid" name="vid">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_brand">Brand</label>
                            <input type="text" class="form-control" id="edit_brand" name="edit_brand" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_model">Model</label>
                            <input type="text" class="form-control" id="edit_model" name="edit_model" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_color">Color</label>
                            <input type="text" class="form-control" id="edit_color" name="edit_color" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_registration_no">Registration No</label>
                            <input type="text" class="form-control" id="edit_registration_no" name="edit_registration_no" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="edit_vehicle">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Vehicle Modal -->
    <div class="modal fade" id="removeVehicleModal" tabindex="-1" role="dialog" aria-labelledby="removeVehicleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeVehicleModalLabel">Remove Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="vehicle_management.php">
                    <input type="hidden" id="remove_vid" name="vid">
                    <div class="modal-body">
                        <p>Are you sure you want to remove this vehicle?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="remove_vehicle">Remove Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Document Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Manage Vehicle Documentation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="documentForm" method="POST">
                        <input type="hidden" name="vid" id="doc_vid">
                        <div class="form-group">
                            <label for="insurance">Insurance:</label>
                            <input type="text" class="form-control" id="insurance" name="insurance" required>
                        </div>
                        <div class="form-group">
                            <label for="registration">Registration:</label>
                            <input type="text" class="form-control" id="registration" name="registration" required>
                        </div>
                        <button type="submit" name="save_document" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Populate Edit Modal with Vehicle Data
        $('#editVehicleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var vid = button.data('id');
            var brand = button.data('brand');
            var model = button.data('model');
            var color = button.data('color');
            var registration = button.data('registration');

            var modal = $(this);
            modal.find('#edit_vid').val(vid);
            modal.find('#edit_brand').val(brand);
            modal.find('#edit_model').val(model);
            modal.find('#edit_color').val(color);
            modal.find('#edit_registration_no').val(registration);
        });

        // Set Vehicle ID for Remove Modal
        $('#removeVehicleModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var vid = button.data('id');

            var modal = $(this);
            modal.find('#remove_vid').val(vid);
        });

        // Set Vehicle ID for Document Modal
        // $('#vehicleDocsModal').on('show.bs.modal', function (event) {
        //     var button = $(event.relatedTarget);
        //     var vid = button.data('id');

        //     var modal = $(this);
        //     modal.find('#doc_vid').val(vid);

        //     // Fetch existing documentation details
        //     $.ajax({
        //         url: 'get_vehicle_docs.php',
        //         method: 'POST',
        //         data: { vid: vid },
        //         success: function(response) {
        //             var data = JSON.parse(response);
        //             modal.find('#insurance_info').val(data.insurance_info);
        //             modal.find('#registration_info').val(data.registration_info);
        //         }
        //     });
        // });
        function loadDocumentDetails(vid) {
            $.ajax({
                url: 'fetch_document.php',
                method: 'POST',
                data: {
                    vid: vid
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#doc_vid').val(response.data.VID);
                        $('#insurance').val(response.data.Insurance);
                        $('#registration').val(response.data.Registration);
                    } else {
                        alert('Failed to load document details.');
                    }
                }
            });
        }
    </script>
</body>

</html>
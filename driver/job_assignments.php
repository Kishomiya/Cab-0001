<?php
session_start();
require_once '../db_connect.php';

// Check if driver is logged in
if (!isset($_SESSION['driver_id'])) {
    echo "Driver ID not set in session. Please log in.";
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Fetch job assignments for this driver
$sql = "SELECT ja.JobID, p.Name AS PassengerName, p.Contact AS PassengerContact, 
        ja.PickupLocation, ja.Destination, ja.PickupTime, ja.PickupDate, ja.status
        FROM job_assignments ja
        JOIN passengers p ON ja.PassengerID = p.PID
        WHERE ja.DriverID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Assignments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</head>
<style>
    .disabled-row {
        text-decoration: line-through;
        background-color: #f8d7da;
    }

    .alert-success {
        position: fixed;
        top: 0;
        right: 0;
        margin: 1rem;
        z-index: 1050;
    }
</style>

<body>
    <div class="container mt-5">
        <h2>Job Assignments</h2>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Passenger Name</th>
                    <th>Passenger Contact</th>
                    <th>Pickup Location</th>
                    <th>Destination</th>
                    <th>Pickup Time</th>
                    <th>Pickup Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="job-row <?php echo $row['status'] === 'canceled' ? 'disabled-row' : ''; ?>" data-job-id="<?php echo $row['JobID']; ?>">
                        <td><?php echo $row['JobID']; ?></td>
                        <td><?php echo $row['PassengerName']; ?></td>
                        <td><?php echo $row['PassengerContact']; ?></td>
                        <td><?php echo $row['PickupLocation']; ?></td>
                        <td><?php echo $row['Destination']; ?></td>
                        <td><?php echo $row['PickupTime']; ?></td>
                        <td><?php echo $row['PickupDate']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'Pending'): ?>
                                <button class="btn btn-success btn-accept" data-job-id="<?php echo $row['JobID']; ?>">Accept</button>
                            <?php elseif ($row['status'] === 'Accepted'): ?>
                                <button class="btn btn-danger btn-cancel" data-job-id="<?php echo $row['JobID']; ?>">Cancel</button>
                            <?php else: ?>
                                <button class="btn btn-danger" disabled>Canceled</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Navigate to the selected ride's detail page
            $('.job-row').on('click', function(e) {
                // Prevent navigation if the click originated from an action button
                if ($(e.target).hasClass('btn-accept') || $(e.target).hasClass('btn-cancel')) {
                    return;
                }

                var jobId = $(this).data('job-id');
                if (jobId) {
                    window.location.href = 'selectedRide.php?job_id=' + jobId;
                }
            });

            // Handle the accept button click
            $(document).on('click', '.btn-accept', function(e) {
                e.stopPropagation(); // Prevents the row click event
                var jobId = $(this).data('job-id');

                $.post('updateStatus.php', {
                    job_id: jobId,
                    status: 'accepted'
                }, function(response) {
                    // Handle the response (updating the UI)
                    $('tr[data-job-id="' + jobId + '"]').removeClass('disabled-row')
                        .find('td:last-child')
                        .html('<button class="btn btn-danger btn-cancel" data-job-id="' + jobId + '">Cancel</button>');
                    $('tr[data-job-id="' + jobId + '"]').find('td:nth-last-child(2)').text('Accepted');
                    $('.alert-success').text('Ride accepted successfully!').fadeIn().delay(3000).fadeOut();
                });
            });

            // Handle the cancel button click
            $(document).on('click', '.btn-cancel', function(e) {
                e.stopPropagation(); // Prevents the row click event
                var jobId = $(this).data('job-id');
                $.post('updateStatus.php', {
                    job_id: jobId,
                    status: 'canceled'
                }, function(response) {
                    // Handle the response (updating the UI)
                    $('tr[data-job-id="' + jobId + '"]').addClass('disabled-row')
                        .find('td:last-child')
                        .html('<button class="btn btn-danger" disabled>Canceled</button>');
                    $('tr[data-job-id="' + jobId + '"]').find('td:nth-last-child(2)').text('Canceled');
                    $('.alert-success').text('Ride canceled successfully!').fadeIn().delay(3000).fadeOut();
                });
            });
        });
    </script>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
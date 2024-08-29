<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
</head>
<style>
    /* Custom styles for the sidebar */
    #wrapper {
        display: flex;
        align-items: stretch;
    }

    #sidebar-wrapper {
        width: 250px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        overflow-y: auto;
        overflow-x: hidden;
        background: #343a40;
        transition: all 0.3s;
    }

    #page-content-wrapper {
        width: 100%;
        padding: 20px;
        position: relative;
        transition: all 0.3s;
        margin-left: 230px;
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -250px;
    }

    .sidebar-heading {
        padding: 20px;
        font-size: 1.5em;
        text-align: center;
    }

    .list-group-item {
        border: none;
        padding: 15px 20px;
    }

    .list-group-item-action:hover {
        background-color: #007bff;
        color: #fff;
    }
</style>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-white">Driver Dashboard</div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                <a href="#profileSubmenu" class="list-group-item list-group-item-action bg-dark text-white" data-toggle="collapse">Profile</a>
                <div id="profileSubmenu" class="collapse">
                    <a href="update_profile.php" class="list-group-item list-group-item-action bg-dark text-white pl-5">Update Profile</a>
                    <a href="change_password.php" class="list-group-item list-group-item-action bg-dark text-white pl-5">Change Password</a>
                </div>
                <a href="vehicle_management.php" class="list-group-item list-group-item-action bg-dark text-white">Vehicle Management</a>
                <!-- <a href="update_status.php" class="list-group-item list-group-item-action bg-dark text-white">Availability Status</a> -->
                <!-- <a href="update_location.php" class="list-group-item list-group-item-action bg-dark text-white">Location</a> -->
                <!-- <a href="route_assistance.php" class="list-group-item list-group-item-action bg-dark text-white">Job Assignments</a> -->
                <!-- <a href="#jobAssignmentsSubmenu" class="list-group-item list-group-item-action bg-dark text-white" data-toggle="collapse">Job Assignments</a> -->
                <!-- <div id="jobAssignmentsSubmenu" class="collapse">
                    <a href="jobs.php" class="list-group-item list-group-item-action bg-dark text-white pl-5">Jobs</a>
                    <a href="job_history.php" class="list-group-item list-group-item-action bg-dark text-white pl-5">Job History</a>
                </div> -->
                <a href="job_assignments.php" class="list-group-item list-group-item-action bg-dark text-white">Job Assignments</a>
                <a href="earnings_settlements.php" class="list-group-item list-group-item-action bg-dark text-white">Earnings & Settlements</a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">Payment Confirmation</a>
                <a href="route_assistance.php" class="list-group-item list-group-item-action bg-dark text-white">Navigation Assistance</a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">SMS Notifications</a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>
            </nav>
            <div class="container-fluid">
                <h1 class="mt-4">Welcome to Driver Dashboard</h1>
                <p>Content goes here...</p>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>
</body>

</html>
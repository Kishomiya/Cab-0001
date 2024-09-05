<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your custom CSS -->
</head>

<body>
    <div class="d-flex bg-Blue" id="wrapper" >
        <!-- Sidebar -->
        <div class=" border-right" id="sidebar-wrapper">
            <div class="sidebar-heading d-flex justify-content-center mt-4">
                <img src="../assets/img/logo1.png" alt="Background Image">
            </div>
            <hr class="bg-white  mb-4 mr-1 ml-1">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item1  text-white"><i class="fas fa-home mr-3 text-white"></i>Dashboard</a>
                <a href="#profileSubmenu" class="list-group-item  text-white" data-toggle="collapse"><i class="fa-solid fa-user mr-3 text-primary"></i>Profile</a>
                <div id="profileSubmenu" class="collapse">
                    <a href="update_profile.php" class="list-group-item  text-white pl-5"><i class="fa-solid fa-pen mr-3 text-primary"></i>Update Profile</a>
                    <a href="change_password.php" class="list-group-item  text-white pl-5"><i class="fa-solid fa-key mr-3 text-primary"></i>Change Password</a>
                </div>
                <a href="vehicle_management.php" class="list-group-item  text-white"><i class="fa-solid fa-car mr-3 text-primary"></i>Vehicle Management</a>
                <a href="job_assignments.php" class="list-group-item  text-white"><i class="fa fa-user-plus mr-3 text-primary"></i>Job Assignments</a>
                <a href="earnings_settlements.php" class="list-group-item  text-white"><i class="fa-solid fa-sack-dollar mr-3 text-primary"></i>Earnings & Settlements</a>
                <a href="#" class="list-group-item  text-white"><i class="fa-solid fa-wallet mr-3 text-primary"></i>Payment Confirmation</a>
                <a href="#" class="list-group-item  text-white"><i class="fa-solid fa-right-from-bracket mr-3 text-primary"></i>Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class=" navbar-expand-lg bg-white shadow ml-3 p-2">
                <ul class="navbar-nav ml-auto navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $_SESSION['username']; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right text-dark" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item text-dark" href="profile.php">Profile</a>
                            <a class="dropdown-item text-dark" href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid bg-Blue ">
                <div class=" ml-4 mt-3">
                    <div class="tab-name bg-white rounded p-2 d-flex align-items-center">
                        <i class="fas fa-home text-primary ml-2"></i>
                        <div class="border-left mx-2" style="height: 24px;"></div> <!-- Vertical line -->
                        <span>Driver Dashboard</span>
                    </div>
                    <div class="d-flex justify-content-center ">
                        <img src="../assets/img/background1.png" class="img-fluid m-4" alt="Background Image">
                    </div>
                </div>
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


    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="dashboard-link">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="profile-link">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="vehicle-link">Vehicle Management</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="status-link">Availability Status</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="location-link">Location</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="job-link">Job Assignments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="earnings-link">Earnings and Settlements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="payment-link">Payment Confirmation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="sms-link">SMS Notifications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4 content">
                <h2>Welcome to the Driver Dashboard</h2>
                <div id="content-area">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </main>
        </div>
    </div>

    <script>
        // JavaScript to handle content loading
        document.getElementById('profile-link').addEventListener('click', function () {
            loadContent('profile.php');
        });

        function loadContent(url) {
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content-area').innerHTML = data;
                })
                .catch(error => console.error('Error loading content:', error));
        }

        // Load dashboard content by default
        loadContent('dashboard.php');
    </script>
</body>

</html>

<?php
include 'db_connect.php'; // Connects to the database

// Fetch summary statistics
$totalProperties = $conn->query("SELECT COUNT(*) AS count FROM property_listings")->fetch_assoc()['count'] ?? 0;
$totalTransactions = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetch_assoc()['count'] ?? 0;
$totalFeedback = $conn->query("SELECT COUNT(*) AS count FROM feedback")->fetch_assoc()['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Ensure the carousel fills the background */
        .carousel {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1; /* Push it behind other content */
        }

        .carousel-item img {
            object-fit: cover;
            width: 100%;
            height: 100vh;
        }

        /* Overlay effect */
        .carousel-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay */
            z-index: -1;
        }

        h2, p {
            color: #ffc107;
        }

        .navbar {
            background-color: #212529;
        }

        .navbar-brand, .nav-link {
            color: white !important;
        }

        .nav-link.active {
            font-weight: bold;
            background-color: #ffc107;
            color: black !important;
            border-radius: 5px;
        }

        .logout-btn {
            background-color: red;
            border: none;
        }
        .logout-btn a{
            color: #f8f9fa;
        }

        .card {
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <!-- Background Image Slider -->
    <div id="backgroundCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/banner1.PNG" alt="Background 1">
            </div>
            <div class="carousel-item">
                <img src="images/banner2.jpeg" alt="Background 2">
            </div>
            <div class="carousel-item">
                <img src="images/banner3.jpeg" alt="Background 3">
            </div>
        </div>
    </div>

    <!-- Dark Overlay -->
    <div class="carousel-overlay"></div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="client_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="properties.php">Properties</a></li>
                    <li class="nav-item"><a class="nav-link" href="virtual_tour.php">Virtual Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            <button class="btn btn-danger logout-btn"><a href="logout.php">Logout</a></button>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <h2>Welcome, Client!</h2>
        <p>Here’s an overview of your account.</p>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">Available Properties</div>
                    <div class="card-body"><strong><?php echo $totalProperties; ?></strong></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">Your Transactions</div>
                    <div class="card-body"><strong><?php echo $totalTransactions; ?></strong></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">Your Feedback</div>
                    <div class="card-body"><strong><?php echo $totalFeedback; ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

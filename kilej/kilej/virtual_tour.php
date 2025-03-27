<?php
session_start();
include 'db_connect.php';

// Fetch virtual tour videos
$query = "SELECT media_url, description, upload_date FROM virtual_tour WHERE media_type = 'video' ORDER BY upload_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Tour</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #212529;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .tour-container {
            padding: 20px;
        }
        .video-card {
            margin-bottom: 20px;
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

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="client_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="properties.php">Properties</a></li>
                <li class="nav-item"><a class="nav-link active" href="virtual_tour.php">Virtual Tour</a></li>
                <li class="nav-item"><a class="nav-link" href="transaction.php">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Virtual Tour Section -->
<div class="container tour-container">
    <h2 class="text-center mb-4">Explore Our Virtual Tours</h2>
    
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card video-card">
                        <div class="card-body">
                            <video width="100%" controls>
                                <source src="<?php echo htmlspecialchars($row['media_url']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <p class="mt-2"><?php echo htmlspecialchars($row['description']); ?></p>
                            <small class="text-muted">Uploaded on: <?php echo date("F j, Y", strtotime($row['upload_date'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-danger">No virtual tour videos available.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

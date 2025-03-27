<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['client_id'])) {
    die("Error: Client is not logged in. <a href='login.php'>Login here</a>");
}

$client_id = intval($_SESSION['client_id']); // Ensure client_id is an integer

// Fetch available properties using prepared statement
$query = "SELECT id, property_title, location, price, status, image_path FROM property_listings ORDER BY date_added DESC";
$availableProperties = $conn->query($query);

// Fetch client transactions securely
$stmt = $conn->prepare("
    SELECT p.property_title, t.total_amount, t.payment_status 
    FROM transactions t 
    JOIN property_listings p ON t.id = p.id 
    WHERE t.client_id = ? 
    ORDER BY t.transaction_date DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$clientTransactions = $stmt->get_result();

// Fetch client feedback securely
$stmt = $conn->prepare("
    SELECT message, response, date_submitted 
    FROM feedback 
    WHERE client_id = ? 
    ORDER BY date_submitted DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$clientFeedback = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
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
        .nav-link.active {
            font-weight: bold;
            background-color: #ffc107; /* Yellow background for active tab */
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
                    <li class="nav-item"><a class="nav-link active" href="properties.php">Properties</a></li>
                    <li class="nav-item"><a class="nav-link" href="virtual_tour.php">Virtual Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            <button class="btn btn-danger logout-btn"><a href="logout.php">Logout</a></button>
        </div>
    </nav>

    <!-- Available Properties -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Available Properties</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php while ($row = $availableProperties->fetch_assoc()) { ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="property_details.php?id=<?php echo $row['id']; ?>" 
                               class="text-decoration-none text-dark">
                                <img src="<?php 
                                    $imagePath = 'uploads/' . htmlspecialchars($row['image_path']);
                                    echo (!empty($row['image_path']) && file_exists($imagePath)) 
                                        ? $imagePath 
                                        : 'placeholder.jpg'; 
                                ?>" class="card-img-top" alt="Property Image">

                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['property_title']); ?></h5>
                                    <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p class="card-text"><strong>Price:</strong> 
                                        <span class="text-primary"><?php echo number_format($row['price'], 2); ?> KES</span>
                                    </p>
                                    <p class="card-text"><strong>Status:</strong> 
                                        <span class="badge <?php 
                                            echo ($row['status'] === 'Available') ? 'bg-success' : 
                                                 (($row['status'] === 'Sold') ? 'bg-danger' : 'bg-warning'); 
                                        ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require 'db_connect.php'; // Ensure database connection

if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);

    // Fetch property details along with seller information
    $stmt = $conn->prepare("
        SELECT p.*, s.name AS seller_name, s.phone AS seller_phone, s.email AS seller_email 
        FROM property_listings p
        JOIN sellers s ON p.seller_id = s.seller_id
        WHERE p.id = ?
    ");
    
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
    } else {
        echo "<h3>Property not found</h3>";
        exit();
    }
} else {
    echo "<h3>Invalid Request</h3>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['property_title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            <button class="btn btn-danger logout-btn">Logout</button>
        </div>
    </nav>
<div class="container mt-5">
    <div class="card">
        <img src="uploads/<?php echo htmlspecialchars($property['image_path']); ?>" class="card-img-top" alt="Property Image">
        <div class="card-body">
            <h4 class="card-title"><?php echo htmlspecialchars($property['property_title']); ?></h4>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
            <p><strong>Price:</strong> <span class="text-primary"><?php echo number_format($property['price'], 2); ?> KES</span></p>
            <p><strong>Status:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($property['status']); ?></span></p>
            
            <?php if ($property['status'] === 'Available') { ?>
                <!-- Button to open modal -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchaseModal">Purchase Property</button>
            <?php } else { ?>
                <button class="btn btn-secondary" disabled>Sold</button>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Contact Seller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Seller Name:</strong> <?php echo htmlspecialchars($property['seller_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($property['seller_phone']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($property['seller_email']); ?></p>
                <p>Contact the seller directly to discuss the purchase.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
